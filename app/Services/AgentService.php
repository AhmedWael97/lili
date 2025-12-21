<?php

namespace App\Services;

use App\Models\User;
use App\Models\AgentType;
use App\Models\UserAgent;
use Illuminate\Support\Facades\DB;

class AgentService
{
    /**
     * Get available agent slots for a user
     */
    public function getAvailableSlots(User $user): int
    {
        $package = $user->subscription?->package;
        if (!$package) {
            return 0;
        }

        // -1 means unlimited
        if ($package->agent_slots === -1) {
            return PHP_INT_MAX;
        }

        $usedSlots = $user->userAgents()->where('status', 'active')->count();
        return max(0, $package->agent_slots - $usedSlots);
    }

    /**
     * Check if user can activate an agent
     */
    public function canActivateAgent(User $user): bool
    {
        return $this->getAvailableSlots($user) > 0;
    }

    /**
     * Activate an agent for a user
     */
    public function activateAgent(User $user, string $agentCode): UserAgent
    {
        if (!$this->canActivateAgent($user)) {
            throw new \Exception('No available agent slots. Please upgrade your package.');
        }

        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        if (!$agentType->is_active) {
            throw new \Exception('This agent type is not available.');
        }

        // Check if already activated
        $existing = UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                throw new \Exception('Agent already activated.');
            }

            // Reactivate if inactive
            $existing->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

            return $existing;
        }

        // Create new user agent
        return UserAgent::create([
            'user_id' => $user->id,
            'agent_type_id' => $agentType->id,
            'status' => 'active',
            'activated_at' => now(),
            'interaction_count' => 0,
        ]);
    }

    /**
     * Deactivate an agent for a user
     */
    public function deactivateAgent(User $user, string $agentCode): void
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->update(['status' => 'inactive']);
    }

    /**
     * Get all active agents for a user
     */
    public function getActiveAgents(User $user)
    {
        return UserAgent::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('agentType')
            ->get();
    }

    /**
     * Get all available agent types
     */
    public function getAvailableAgentTypes()
    {
        return AgentType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Check if user has specific agent activated
     */
    public function hasAgentActivated(User $user, string $agentCode): bool
    {
        $agentType = AgentType::where('code', $agentCode)->first();
        
        if (!$agentType) {
            return false;
        }

        return UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get agent usage statistics
     */
    public function getAgentStats(User $user, string $agentCode): array
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();
        
        $userAgent = UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->first();

        if (!$userAgent) {
            return [
                'is_active' => false,
                'interaction_count' => 0,
                'last_used_at' => null,
            ];
        }

        return [
            'is_active' => $userAgent->status === 'active',
            'interaction_count' => $userAgent->interaction_count,
            'last_used_at' => $userAgent->last_used_at,
            'activated_at' => $userAgent->activated_at,
        ];
    }

    /**
     * Pause an agent (temporarily disable without releasing slot)
     */
    public function pauseAgent(User $user, string $agentCode): void
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->update(['status' => 'paused']);
    }

    /**
     * Resume a paused agent
     */
    public function resumeAgent(User $user, string $agentCode): void
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->update(['status' => 'active']);
    }
}
