<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyAdminResourceRequest;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(private RoomService $rooms) {}

    public function index(Request $request): View
    {
        return view('pages.admin.room.index', [
            'rooms' => $this->rooms->paginate($request->only(['search', 'is_active', 'sort', 'direction']), 12),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.room.create', ['room' => new Room(['is_active' => true])]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $room = $this->rooms->createWithImage($request->validated(), $request->file('image'));

        return to_route('admin.room.show', $room)->with('status', 'Room berhasil dibuat.');
    }

    public function show(Room $room): View
    {
        $room->load(['classes.program', 'classes.instructor']);

        return view('pages.admin.room.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        return view('pages.admin.room.edit', compact('room'));
    }

    public function update(StoreRoomRequest $request, Room $room): RedirectResponse
    {
        $this->rooms->updateWithImage($room, $request->validated(), $request->file('image'));

        return to_route('admin.room.show', $room)->with('status', 'Room berhasil diperbarui.');
    }

    public function destroy(DestroyAdminResourceRequest $request, Room $room): RedirectResponse
    {
        $request->validated();
        $this->rooms->delete($room);

        return to_route('admin.room.index')->with('status', 'Room berhasil dihapus.');
    }
}
