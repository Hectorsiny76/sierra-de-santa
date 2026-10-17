<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('roomType', 'images')->get();
        return RoomResource::collection($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $this->authorize('create', Room::class);
        $room = Room::create($request->validated());
        return new RoomResource($room->load('roomType', 'images'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load('roomType', 'images');
        return new RoomResource($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $this->authorize('update', $room);
        $room->update($request->validated());
        return new RoomResource($room->load('roomType', 'images'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $this->authorize('delete', $room);

        $oldRoom = $room;

        $room->delete();

        Log::info("Room $oldRoom->id eliminado", [
            'room' => $oldRoom,
            'user' => auth()->user(),
        ]);

        return response()->json(["message" => "El cuarto se ha eliminado."]);
    }
}
