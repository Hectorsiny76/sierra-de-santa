<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomTypeController extends Controller
{

    public function __construct(protected ImageService $images){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::all();
        return RoomTypeResource::collection($roomTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomTypeRequest $request)
    {
        $this->authorize('create', RoomType::class);
        if ($request->hasFile('image')) {
            $path = $this->images->store($request->file('image'), 'room_types');
        }
        $roomType = RoomType::create([
            'name' => $request->name,
            'description' => $request->description,
            $request->safe()->except('image'),
            'img_path' => $path
        ]);
        return new RoomTypeResource($roomType);
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomType $roomType)
    {
        return new RoomTypeResource($roomType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomTypeRequest $request, RoomType $roomType)
    {
        $this->authorize('update', $roomType);
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['img_path'] = $this->images->replace($roomType->img_path, $request->file('image'), 'room_types');
        }
        $roomType->update($data);
        return new RoomTypeResource($roomType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType)
    {
        $this->authorize('delete', $roomType);
        if($roomType->img_path){
            $this->images->delete($roomType->img_path);
        }
        $roomType->delete();
        return response()->json(["message" => "Tipo de cuarto eliminado correctamente."]);
    }
}
