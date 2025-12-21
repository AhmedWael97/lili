<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get all admin users
     */
    public function getAdmins(): Collection
    {
        return $this->model->where('role', 'admin')->get();
    }

    /**
     * Get users with active subscriptions
     */
    public function getUsersWithActiveSubscriptions(): Collection
    {
        return $this->model->whereHas('subscription', function ($query) {
            $query->where('status', 'active');
        })->get();
    }

    /**
     * Get user with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?User
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Search users by name or email
     */
    public function search(string $term)
    {
        return $this->model
            ->where('name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->paginate(15);
    }

    /**
     * Get users by subscription package
     */
    public function getUsersByPackage(string $packageName): Collection
    {
        return $this->model->whereHas('subscription', function ($query) use ($packageName) {
            $query->where('package_name', $packageName)
                  ->where('status', 'active');
        })->get();
    }

    /**
     * Update user role
     */
    public function updateRole(int $userId, string $role): bool
    {
        return $this->update($userId, ['role' => $role]);
    }
}
