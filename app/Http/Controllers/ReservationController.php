<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Package;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\ReservationStatus;
use App\Services\ReservationPricingService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    public function __construct(protected ReservationPricingService $pricing){}

    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $this->authorize('viewReservations', $user);

        $reservations = $user->reservations()->with(['room', 'package', 'user'])->get();

        return ReservationResource::collection($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request, User $user)
    {
        $this->authorize('createReservation', $user);

        $data = $request->validated();

        $room = Room::findOrFail($data['room_id']);
        $package = isset($data['package_id']) ? Package::findOrFail($data['package_id']) : null;

        if (!$room->isAvailable($data['start_date'], $data['end_date'])) {
            return response()->json([
                'message' => 'El cuarto no está disponible para las fechas seleccionadas.'
            ], 409);
        }

        $data['status'] = ReservationStatus::Pending;

        $data['total_price'] = $this->pricing->calculateTotal($room, $package, $data['start_date'], $data['end_date']);

        $reservation = $user->reservations()->create($data);

        return new ReservationResource($reservation->load(['room', 'package', 'user']));

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Reservation $reservation)
    {
        $reservation = $user->reservations()
            ->with(['room', 'package', 'user'])
            ->findOrFail($reservation->id);

        $this->authorize('view', $reservation);

        return new ReservationResource($reservation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, User $user, Reservation $reservation)
    {

        $reservation = $user->reservations()->findOrFail($reservation->id);

        $this->authorize('update', $reservation);

        $data = $request->validated();

        $roomId = $data['room_id'] ?? $reservation->room_id;
        $startDate = $data['start_date'] ?? $reservation->start_date;
        $endDate = $data['end_date'] ?? $reservation->end_date;

        $bookingDetailsChanged = array_intersect_key($data, array_flip(['room_id', 'start_date', 'end_date']));

        if($bookingDetailsChanged){
            $room = Room::findOrFail($roomId);

            if(!$room->isAvailable($startDate, $endDate, $reservation->id)){
                return response()->json(["message" => "El cuarto no está disponible para las fechas seleccionadas."], 409);
            }

        }

        if($bookingDetailsChanged || isset($data['package_id'])){
            $room ??= Room::findOrFail($roomId);
            $package = Package::find($data['package_id'] ?? $reservation->package_id);
            $data['total_price'] = $this->pricing->calculateTotal($room, $package, $data['start_date'], $data['end_date']);
        }

        $reservation->update($data);

        return new ReservationResource($reservation->load(['room', 'package', 'user']));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Reservation $reservation)
    {
        $reservation = $user->reservations()->findOrFail($reservation->id);
        $this->authorize('delete', $reservation);

        $reservation->update(['status' => ReservationStatus::Cancelled]);

        return response()->json(["message" => "La reservación se cancelo con éxito."], 200);
    }

    public function forceDelete(Reservation $reservation){
        $this->authorize('force-delete', $reservation);

        $reservation->delete();

        return response()->json(["message" => "Reservación eliminada."]);
    }

    public function restore(User $user, int $reservationId){

        $reservation = $user->reservations()->withTrashed()->findOrFail($reservationId);

        $this->authorize('restore', $reservation);

        $reservation->restore();

        return new ReservationResource($reservation->load(['room', 'package', 'user']));
    }
    public function viewTrashed(){
        $this->authorize('viewTrashed', Reservation::class);
        $trashedReservations = Reservation::onlyTrashed()->with(['room', 'package', 'user'])->get();
        return ReservationResource::collection($trashedReservations);
    }
}
