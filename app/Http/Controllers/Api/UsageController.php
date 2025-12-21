<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UsageService;
use Illuminate\Support\Facades\Auth;

class UsageController extends Controller
{
    public function __construct(
        protected UsageService $usageService
    ) {}

    /**
     * Get usage summary for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $usage = $this->usageService->getUsageSummary($user->id);

        return response()->json([
            'package' => $user->subscription->package_name ?? 'free',
            'usage' => $usage,
            'subscription' => [
                'status' => $user->subscription?->status,
                'expires_at' => $user->subscription?->current_period_end,
            ],
        ]);
    }
}
