<?php

namespace App\Services;

use App\Models\ChatbotLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ChatbotLogService extends BaseCrudService
{
    protected function modelClass(): string
    {
        return ChatbotLog::class;
    }

    public function adminPaginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'session_id',
            'intent',
            'is_helpful',
            'created_at',
        ])
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function defaultWith(): array
    {
        return ['user'];
    }

    public function logInteraction(
        string $sessionId,
        string $userMessage,
        string $botResponse,
        ?string $intent = null,
        User|int|null $user = null,
    ): ChatbotLog {
        $userId = $user instanceof User ? $user->getKey() : $user;

        /** @var ChatbotLog $log */
        $log = $this->create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'user_message' => $userMessage,
            'bot_response' => $botResponse,
            'intent' => $intent,
        ]);

        return $log;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['session_id'] ?? null, fn (Builder $query, string $sessionId) => $query->where('session_id', $sessionId))
            ->when($filters['user_id'] ?? null, fn (Builder $query, int|string $userId) => $query->where('user_id', $userId))
            ->when($filters['intent'] ?? null, fn (Builder $query, string $intent) => $query->where('intent', $intent))
            ->when(array_key_exists('is_helpful', $filters), fn (Builder $query) => $query->where('is_helpful', (bool) $filters['is_helpful']));

        return $this->whereDateRange($query, 'created_at', $filters, 'created_from', 'created_to');
    }
}
