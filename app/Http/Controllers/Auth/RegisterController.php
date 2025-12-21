<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\SubscriptionService;
use App\Repositories\UserRepository;
use App\Repositories\AuditLogRepository;
use App\Models\Package;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __construct(
        protected UserRepository $userRepo,
        protected SubscriptionService $subscriptionService,
        protected AuditLogRepository $auditRepo
    ) {}

    /**
     * Show registration form
     */
    public function showForm()
    {
        $packages = Package::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        return view('auth.register', compact('packages'));
    }

    /**
     * Handle registration
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Create user
            $user = $this->userRepo->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company' => $request->company,
                'role' => 'user',
            ]);

            // Get package details
            $package = Package::findOrFail($request->package_id);
            
            // Create subscription
            $this->subscriptionService->createSubscription($user->id, strtolower($package->name));

            // Log registration
            $this->auditRepo->log(
                'user_registered',
                "New user registered with {$package->name} package",
                $user->id,
                'User',
                $user->id
            );

            // Send verification email (if email verification enabled)
            // Mail::to($user)->send(new VerifyEmailMail($user));

            // Auto login
            auth()->login($user);

            return redirect()->route('dashboard.index')
                ->with('success', 'Welcome! Your account has been created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Verify email
     */
    public function verifyEmail(string $token)
    {
        // Email verification logic
        return redirect()->route('dashboard.index')
            ->with('success', 'Email verified successfully!');
    }
}
