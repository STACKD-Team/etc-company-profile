<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;

class RoomService extends BaseCrudService
{
    public function __construct(private MediaStorageService $mediaStorage) {}

    protected function modelClass(): string
    {
        return Room::class;
    }

    public function paginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'name',
            'capacity',
            'display_order',
            'is_active',
            'created_at',
        ], 'display_order', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createWithImage(array $data, ?UploadedFile $image = null): Room
    {
        $data = $this->normalize($data);

        if ($image !== null) {
            $data['image'] = $this->mediaStorage->putUploadedFile($image, 'rooms');
        }

        /** @var Room $room */
        $room = $this->create($data);

        return $room;
    }

    public function updateWithImage(Room $room, array $data, ?UploadedFile $image = null): Room
    {
        $data = $this->normalize($data);

        if ($image !== null) {
            $data['image'] = $this->mediaStorage->replace($room->image, $image, 'rooms');
        }

        /** @var Room $room */
        $room = $this->update($room, $data);

        return $room;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $this->whereLike($query, ['name', 'description'], $filters['search'] ?? null)
            ->when(array_key_exists('is_active', $filters), fn (Builder $query) => $query->where('is_active', (bool) $filters['is_active']));
    }

    private function normalize(array $data): array
    {
        $facilities = preg_split('/\r\n|\r|\n/', (string) ($data['facilities_text'] ?? ''), flags: PREG_SPLIT_NO_EMPTY) ?: [];
        unset($data['facilities_text'], $data['image']);

        $data['facilities'] = array_values(array_map('trim', $facilities));
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['display_order'] = $data['display_order'] ?? 0;

        return $data;
    }
}
