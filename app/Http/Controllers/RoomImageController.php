<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomImageRequest;
use App\Http\Requests\StoreRoomImagesRequest;
use App\Http\Requests\UpdateRoomImageRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomImageResource;
use App\Models\Room;
use App\Models\RoomImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomImageController extends Controller
{

    public function __construct(protected ImageService $images){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomImageRequest $request, Room $room)
    {
        $this->authorize('update', $room);

        $file = $request->safe()->file('image');
        $originalName = $file->getClientOriginalName();

        $path = $this->images->store($file, 'rooms');

        $image = $room->images()->create([
            'name' => $originalName,
            'path' => $path,
            'description' => $request->description,
        ]);

        return new RoomImageResource($image);

    }

    /**
     * Store the newly created resources in storage.
     */
    public function storeMany(StoreRoomImagesRequest $request, Room $room){
        $this->authorize('update', $room);

        $created = collect($request->file('images'))->map(function ($file, $i) use ($room, $request) {
            return $room->images()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $this->images->store($file, 'rooms'),
                'description' => $request->descriptions[$i] ?? null,
            ]);
        });

        return RoomImageResource::collection($created);
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomImage $roomImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomImageRequest $request, RoomImage $image)
    {

        $validated = $request->safe();

        $this->authorize('update', $image->room);

        $data = [];

        if ($request->hasFile('image')) {
            $data['path'] = $this->images->replace($image->path, $request->file('image'), 'rooms');
            $data['name'] = $request->file('image')->getClientOriginalName();
        }

        if ($validated->has('description')) {
            $data['description'] = $validated->description;
        }

        $image->update($data);

        return new RoomImageResource($image);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomImage $image)
    {
        $this->authorize('update', $image->room);

        $this->images->delete($image->path);

        $oldImage = $image;

        $image->delete();

        Log::info("Imagen $oldImage->id eliminado", [
            'image' => $oldImage,
            'user' => auth()->user()->id,
        ]);

        return response()->json(["message" => "Imagen eliminada correctamente."]);
    }
}
