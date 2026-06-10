<?php

namespace App\Services;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ContactMessageService extends BaseCrudService
{
    protected function modelClass(): string
    {
        return ContactMessage::class;
    }

    public function adminPaginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'name',
            'email',
            'subject',
            'is_read',
            'replied_at',
            'created_at',
        ])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function markAsRead(ContactMessage $message): ContactMessage
    {
        /** @var ContactMessage $message */
        $message = $this->update($message, ['is_read' => true]);

        return $message;
    }

    public function markAsReplied(ContactMessage $message, ?string $repliedAt = null): ContactMessage
    {
        /** @var ContactMessage $message */
        $message = $this->update($message, [
            'is_read' => true,
            'replied_at' => $repliedAt ?? now(),
        ]);

        return $message;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $this->whereLike($query, ['name', 'email', 'subject'], $filters['search'] ?? null)
            ->when(array_key_exists('is_read', $filters), fn (Builder $query) => $query->where('is_read', (bool) $filters['is_read']));
    }
}
