<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\ContentRepository;
use App\Repositories\AuditLogRepository;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected UserRepository $userRepo,
        protected SubscriptionRepository $subscriptionRepo,
        protected ContentRepository $contentRepo,
        protected AuditLogRepository $auditRepo
    ) {}

    /**
     * Admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_users' => $this->userRepo->all()->count(),
            'active_subscriptions' => $this->subscriptionRepo->getActive()->count(),
            'total_content' => $this->contentRepo->all()->count(),
            'monthly_revenue' => $this->subscriptionRepo->getRevenueByPackage(),
        ];

        $recentUsers = $this->userRepo->all()
            ->with('subscription')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $recentActivity = $this->auditRepo->all()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentActivity'));
    }

    /**
     * User management
     */
    public function users()
    {
        $users = $this->userRepo->all()
            ->with('subscription', 'facebookPages')
            ->paginate(50);

        return view('admin.users', compact('users'));
    }

    /**
     * Subscription analytics
     */
    public function subscriptions()
    {
        $revenue = $this->subscriptionRepo->getRevenueByPackage();
        
        $subscriptions = $this->subscriptionRepo->all()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.subscriptions', compact('revenue', 'subscriptions'));
    }

    /**
     * System logs
     */
    public function logs()
    {
        $logs = $this->auditRepo->all()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.logs', compact('logs'));
    }
}
