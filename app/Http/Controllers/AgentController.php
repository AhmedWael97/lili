<?php

namespace App\Http\Controllers;

use App\Models\AgentConfiguration;
use App\Services\AgentService;
use App\Services\AgentInteractionService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        protected AgentService $agentService,
        protected AgentInteractionService $interactionService
    ) {}

    /**
     * Display agent marketplace
     */
    public function index()
    {
        $user = auth()->user();
        $availableAgents = $this->agentService->getAvailableAgentTypes();
        $activeAgents = $this->agentService->getActiveAgents($user);
        $availableSlots = $this->agentService->getAvailableSlots($user);
        
        $package = $user->subscription?->package;
        $totalSlots = $package?->agent_slots ?? 0;

        return view('agents.index', compact(
            'availableAgents',
            'activeAgents',
            'availableSlots',
            'totalSlots'
        ));
    }

    /**
     * Activate an agent
     */
    public function activate(Request $request, string $agentCode)
    {
        try {
            $user = auth()->user();
            
            // Check permission
            if (!$user->can('activate-agents')) {
                return back()->with('error', 'You do not have permission to activate agents.');
            }

            $userAgent = $this->agentService->activateAgent($user, $agentCode);

            // Redirect to onboarding for marketing agents
            if ($agentCode === 'marketing') {
                return redirect()
                    ->route('agents.onboarding', $agentCode)
                    ->with('success', 'Agent activated! Please complete the onboarding to get started.');
            }

            return back()->with('success', 'Agent activated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Use an agent - check onboarding and redirect appropriately
     */
    public function use(string $agentCode)
    {
        $user = auth()->user();
        
        // Find the user's active agent
        $userAgent = $user->userAgents()
            ->whereHas('agentType', function($q) use ($agentCode) {
                $q->where('code', $agentCode);
            })
            ->where('status', 'active')
            ->first();

        if (!$userAgent) {
            return redirect()
                ->route('agents.index')
                ->with('error', 'Please activate this agent first.');
        }

        // Check if onboarding is required and completed for marketing agents
        if ($agentCode === 'marketing') {
            $configuration = AgentConfiguration::where('user_id', $user->id)
                ->where('user_agent_id', $userAgent->id)
                ->where('is_complete', true)
                ->first();

            if (!$configuration) {
                return redirect()
                    ->route('agents.onboarding', $agentCode)
                    ->with('info', 'Please complete the setup to start using your marketing agent.');
            }

            // Onboarding complete, go to AI Studio
            return redirect()->route('marketing.studio.index');
        }

        // QA Agent
        if ($agentCode === 'qa') {
            return redirect()->route('qa-agent.index');
        }

        // For other agents (Developer, Accountant, etc.) - coming soon
        return back()->with('info', 'This agent interface is coming soon!');
    }

    /**
     * Deactivate an agent
     */
    public function deactivate(Request $request, string $agentCode)
    {
        try {
            $user = auth()->user();
            
            // Check permission
            if (!$user->can('deactivate-agents')) {
                return back()->with('error', 'You do not have permission to deactivate agents.');
            }

            $this->agentService->deactivateAgent($user, $agentCode);

            return back()->with('success', 'Agent deactivated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * View agent analytics
     */
    public function analytics(string $agentCode)
    {
        $user = auth()->user();
        
        if (!$user->can('view-agent-analytics')) {
            abort(403, 'You do not have permission to view agent analytics.');
        }

        $stats = $this->agentService->getAgentStats($user, $agentCode);
        $analytics = $this->interactionService->getAgentAnalytics($user, $agentCode, 30);
        $recentInteractions = $this->interactionService->getRecentInteractions($user, $agentCode, 10);

        return view('agents.analytics', compact(
            'agentCode',
            'stats',
            'analytics',
            'recentInteractions'
        ));
    }

    /**
     * Record user feedback on interaction
     */
    public function feedback(Request $request, int $interactionId)
    {
        $request->validate([
            'feedback' => 'required|in:positive,negative,neutral',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $this->interactionService->recordFeedback(
                $interactionId,
                $request->feedback,
                $request->comment
            );

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View interaction details
     */
    public function interaction(int $interactionId)
    {
        $user = auth()->user();
        
        if (!$user->can('view-agent-analytics')) {
            abort(403);
        }

        $interaction = $this->interactionService->getInteraction($interactionId);

        // Ensure user owns this interaction
        if ($interaction->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        return view('agents.interaction', compact('interaction'));
    }

    /**
     * Export training data (admin only)
     */
    public function exportTrainingData(Request $request, string $agentCode)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Admin access required');
        }

        $filters = [
            'success' => $request->input('success'),
            'feedback' => $request->input('feedback'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $data = $this->interactionService->exportTrainingData($agentCode, $filters);

        return response()->json($data);
    }
}
