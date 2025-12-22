<?php

namespace App\Http\Controllers;

use App\Models\AgentConfiguration;
use App\Models\UserAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentOnboardingController extends Controller
{
    public function show($agentCode)
    {
        $user = auth()->user();
        
        // Find the user's agent
        $userAgent = $user->userAgents()
            ->whereHas('agentType', function($q) use ($agentCode) {
                $q->where('code', $agentCode);
            })
            ->where('status', 'active')
            ->firstOrFail();

        // Get or create configuration
        $configuration = AgentConfiguration::firstOrCreate(
            [
                'user_id' => $user->id,
                'user_agent_id' => $userAgent->id,
            ],
            [
                'agent_code' => $agentCode,
            ]
        );

        return view('agents.onboarding.index', [
            'agentCode' => $agentCode,
            'userAgent' => $userAgent,
            'configuration' => $configuration,
        ]);
    }

    public function store(Request $request, $agentCode)
    {
        $user = auth()->user();
        
        $userAgent = $user->userAgents()
            ->whereHas('agentType', function($q) use ($agentCode) {
                $q->where('code', $agentCode);
            })
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            // Business Foundation
            'business_name' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'products_services' => 'required|string',
            'unique_value_proposition' => 'nullable|string',
            'competitors' => 'nullable|string',
            
            // Brand Identity
            'brand_colors.primary' => 'nullable|string',
            'brand_colors.secondary' => 'nullable|string',
            'brand_colors.accent' => 'nullable|string',
            'brand_tone' => 'required|string',
            'brand_personality' => 'nullable|string',
            'brand_story' => 'nullable|string',
            
            // Target Audience
            'target_audience.age' => 'required|string',
            'target_audience.location' => 'required|string',
            'target_audience.interests' => 'nullable|string',
            'target_audience.income' => 'nullable|string',
            'pain_points' => 'required|string',
            'buying_motivations' => 'nullable|string',
            
            // Marketing Goals
            'marketing_goals' => 'required|array',
            'monthly_budget' => 'nullable|numeric|min:0',
            'timeline' => 'nullable|string',
            'key_metrics' => 'nullable|array',
            
            // Current Status
            'current_platforms' => 'nullable|array',
            'whats_working' => 'nullable|string',
            'whats_not_working' => 'nullable|string',
            
            // Content Strategy
            'content_types' => 'required|array',
            'posting_frequency' => 'required|string',
            'focus_keywords' => 'nullable|string',
            'topics_to_avoid' => 'nullable|string',
            
            // Communication
            'requires_approval' => 'boolean',
            'communication_preference' => 'required|string',
        ]);

        DB::transaction(function () use ($user, $userAgent, $agentCode, $validated) {
            // Process focus_keywords from comma-separated string to array
            if (!empty($validated['focus_keywords'])) {
                $validated['focus_keywords'] = array_map('trim', explode(',', $validated['focus_keywords']));
            } else {
                $validated['focus_keywords'] = [];
            }
            
            AgentConfiguration::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'user_agent_id' => $userAgent->id,
                ],
                array_merge($validated, [
                    'agent_code' => $agentCode,
                    'is_complete' => true,
                    'completed_at' => now(),
                ])
            );
        });

        // Redirect to the appropriate agent feature page
        if ($agentCode === 'marketing') {
            return redirect()
                ->route('marketing.os.index')
                ->with('success', 'Setup complete! Your marketing agent is ready to use.');
        }

        // For other agents, redirect to agent dashboard
        return redirect()
            ->route('dashboard.agents')
            ->with('success', 'Agent configuration completed! Your agent is ready to use.');
    }

    public function edit($agentCode)
    {
        $user = auth()->user();
        
        $userAgent = $user->userAgents()
            ->whereHas('agentType', function($q) use ($agentCode) {
                $q->where('code', $agentCode);
            })
            ->where('status', 'active')
            ->firstOrFail();

        $configuration = AgentConfiguration::where('user_id', $user->id)
            ->where('user_agent_id', $userAgent->id)
            ->firstOrFail();

        return view('agents.onboarding.edit', [
            'agentCode' => $agentCode,
            'userAgent' => $userAgent,
            'configuration' => $configuration,
        ]);
    }

    public function update(Request $request, $agentCode)
    {
        $user = auth()->user();
        
        $userAgent = $user->userAgents()
            ->whereHas('agentType', function($q) use ($agentCode) {
                $q->where('code', $agentCode);
            })
            ->where('status', 'active')
            ->firstOrFail();

        $configuration = AgentConfiguration::where('user_id', $user->id)
            ->where('user_agent_id', $userAgent->id)
            ->firstOrFail();

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'products_services' => 'required|string',
            'unique_value_proposition' => 'nullable|string',
            'competitors' => 'nullable|string',
            'brand_colors' => 'nullable|array',
            'brand_tone' => 'required|string',
            'brand_personality' => 'nullable|string',
            'brand_story' => 'nullable|string',
            'target_audience' => 'required|array',
            'pain_points' => 'required|string',
            'buying_motivations' => 'nullable|string',
            'marketing_goals' => 'required|array',
            'monthly_budget' => 'nullable|numeric|min:0',
            'timeline' => 'nullable|string',
            'key_metrics' => 'nullable|array',
            'current_platforms' => 'nullable|array',
            'whats_working' => 'nullable|string',
            'whats_not_working' => 'nullable|string',
            'content_types' => 'required|array',
            'posting_frequency' => 'required|string',
            'focus_keywords' => 'nullable|string',
            'topics_to_avoid' => 'nullable|string',
            'requires_approval' => 'boolean',
            'communication_preference' => 'required|string',
        ]);

        // Process focus_keywords from comma-separated string to array
        if (!empty($validated['focus_keywords'])) {
            $validated['focus_keywords'] = array_map('trim', explode(',', $validated['focus_keywords']));
        } else {
            $validated['focus_keywords'] = [];
        }

        $configuration->update($validated);

        return redirect()
            ->route('dashboard.agents')
            ->with('success', 'Agent configuration updated successfully!');
    }
}
