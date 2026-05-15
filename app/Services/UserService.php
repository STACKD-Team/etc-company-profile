<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class UserService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return User::class;
    }

    public function updateAvatar(User $user, UploadedFile $avatar): User
    {
        /** @var User $user */
        $user = $this->update($user, [
            'avatar' => $this->mediaStorage->replace($user->avatar, $avatar, 'users/avatars'),
        ]);

        return $user;
    }

    public function forceDelete(Model $model): bool
    {
        /** @var User $model */
        $avatar = $model->avatar;
        $deleted = parent::forceDelete($model);

        if ($deleted) {
            $this->mediaStorage->delete($avatar);
        }

        return $deleted;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $this->whereLike($query, ['name', 'full_name', 'email', 'no_induk'], $filters['search'] ?? null)
            ->when($filters['role'] ?? null, fn (Builder $query, string $role) => $query->where('role', $role))
            ->when(array_key_exists('is_active', $filters), fn (Builder $query) => $query->where('is_active', (bool) $filters['is_active']));
    }
}
