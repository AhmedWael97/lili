<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function usageTracking()
    {
        return $this->hasMany(UsageTracking::class);
    }

    public function connectedPlatforms()
    {
        return $this->hasMany(ConnectedPlatform::class);
    }

    public function facebookPages()
    {
        return $this->hasMany(FacebookPage::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function brandSetting()
    {
        return $this->hasOne(BrandSetting::class);
    }

    public function userAgents()
    {
        return $this->hasMany(UserAgent::class);
    }

    public function agentInteractions()
    {
        return $this->hasMany(AgentInteraction::class);
    }

    /**
     * Helper methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription && $this->subscription->status === 'active';
    }

    public function getCurrentUsage(string $monthYear = null): ?UsageTracking
    {
        $monthYear = $monthYear ?? now()->format('Y-m');
        return $this->usageTracking()->where('month_year', $monthYear)->first();
    }
}
