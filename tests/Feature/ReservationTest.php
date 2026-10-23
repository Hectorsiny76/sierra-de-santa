<?php

use App\Models\Package;
use App\Models\Reservation;
use App\Models\Room;
use App\ReservationStatus;
use App\Services\ReservationPricingService;

beforeEach(function () {
   $this->pricing = new ReservationPricingService();
   $this->user = regularUser();
   $this->admin = adminUser();
   $this->room = Room::factory()->create();
   $this->package = Package::factory()->create();
});

function createReservation($test) : Reservation{
    return $test->user->reservations()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(8),
        'guest_count' => 7,
        'total_price' => 1293,
        'status' => ReservationStatus::Pending,
        'room_id' => $test->room->id,
        'package_id' => $test->package->id,
    ]);
}

function softDeletedReservation($test) : Reservation{
    $reservation = createReservation($test);
    $reservation->deleted_at = now();
    $reservation->save();
    return $reservation;
}

test('1 - Un usuario no autenticado no puede ver las reservaciones de alguien más.', function () {
    $response = $this->get(route('users.reservations.index', 1));

    $response->assertStatus(401);
});


test('2 - Un usuario autenticado puede ver sus reservaciones.', function () {
    $response = $this->actingAs($this->user)->getJson(route('users.reservations.index', $this->user->id));

    $response->assertStatus(200);
});

test('3 - Un usuario autenticado no puede ver las reservaciones de otro usuario.', function () {
    $user2 = regularUser();

    $response = $this->actingAs($user2)->getJson(route('users.reservations.index', $this->user->id));

    $response->assertStatus(403);
});

test('4 - Un administrador no puede ver las reservaciones de otro usuario.', function () {
    $response = $this->actingAs($this->admin)->getJson(route('users.reservations.index', $this->user->id));

    $response->assertStatus(403);
});

test('5 - Un usuario no autenticado no puede crear una reservación.', function () {

    $payload = [
        'start_date' => now(),
        'end_date' => now()->addDays(4),
        'guest_count' => 4,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $response = $this->post(route('users.reservations.store', 1), $payload);

    $response->assertStatus(401);
});

test('6 - Un usuario autenticado puede crear una reservación.', function () {
    $payload = [
        'start_date' => now(),
        'end_date' => now()->addDays(6),
        'guest_count' => 6,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $response = $this->actingAs($this->user)->postJson(route('users.reservations.index', $this->user->id), $payload);
    $response->assertStatus(201);
    $this->assertDatabaseHas('reservations', ['guest_count' => 6, 'room_id' => $this->room->id, 'package_id' => $this->package->id]);
});

test('7 - Un usuario no puede crear una reservacion para otro usuario.', function () {
    $payload = [
        'start_date' => now(),
        'end_date' => now()->addDays(7),
        'guest_count' => 7,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $user2 = regularUser();

    $response = $this->actingAs($user2)->postJson(route('users.reservations.index', $this->user->id), $payload);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('reservations', ['guest_count' => 7, 'room_id' => $this->room->id, 'package_id' => $this->package->id]);
});

test('8 - Un admin no puede crear una reservacion para otro usuario.', function () {
    $payload = [
        'start_date' => now(),
        'end_date' => now()->addDays(8),
        'guest_count' => 8,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $response = $this->actingAs($this->admin)->postJson(route('users.reservations.index', $this->user->id), $payload);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('reservations', ['guest_count' => 8, 'room_id' => $this->room->id, 'package_id' => $this->package->id]);
});

test('9 - Un usuario no autenticado no puede ver una reservacion de otro usuario.', function () {

    $reservation = createReservation($this);

    $response = $this->getJson(route('users.reservations.show', [$this->user->id, $reservation->id]));
    $response->assertStatus(401);
});

test('10 - Un usuario autenticado no puede ver una reservacion de otro usuario.', function () {
    $reservation = createReservation($this);

    $user2 = regularUser();

    $response = $this->actingAs($user2)->getJson(route('users.reservations.show', [$this->user->id, $reservation->id]));
    $response->assertStatus(403);
});

test('11 - Un admin no puede ver una reservacion de otro usuario.', function () {
   $reservation = createReservation($this);

   $response = $this->actingAs($this->admin)->getJson(route('users.reservations.show', [$this->user->id, $reservation->id]));
   $response->assertStatus(403);
});

test('12 - Un usuario no autenticado no puede actualizar una reservación.', function () {
    $reservation = createReservation($this);

    $payload = [
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(5),
        'guest_count' => 2,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $response = $this->patchJson(route('users.reservations.update', [$this->user->id, $reservation->id]), $payload);
    $response->assertStatus(401);
});

test('13 - Un usuario autenticado no puede actualizar una reservación de otro usuario.', function () {
    $reservation = createReservation($this);

    $user2 = regularUser();

    $payload = [
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(5),
        'guest_count' => 2,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $response = $this->actingAs($user2)->patchJson(route('users.reservations.update', [$this->user->id, $reservation->id]), $payload);
    $response->assertStatus(403);
});

test('14 - Un usuario autenticado puede actualizar su reservación.', function () {
    $reservation = createReservation($this);

    $payload = [
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(5),
        'guest_count' => 2,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ];

    $response = $this->actingAs($this->user)->patchJson(route('users.reservations.update', [$this->user->id, $reservation->id]), $payload);
    $response->assertStatus(200);
    $this->assertDatabaseHas('reservations', ['guest_count' => 2, 'room_id' => $this->room->id, 'package_id' => $this->package->id]);
});

test('15 - Un admin no puede actualizar la reservación de otro usuario.', function () {
    $reservation = createReservation($this);
    $payload = [
      'start_date' => now()->addDays(2),
      'end_date' => now()->addDays(5),
      'guest_count' => 2,
      'room_id' => $this->room->id,
      'package_id' => $this->package->id,
    ];

    $response = $this->actingAs($this->admin)->patchJson(route('users.reservations.update', [$this->user->id, $reservation->id]), $payload);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('reservations', [
        'guest_count' => 2,
        'room_id' => $this->room->id,
        'package_id' => $this->package->id,
    ]);
});

test('16 - Un usuario no autenticado no puede cancelar una reservación.', function () {
    $reservation = createReservation($this);

    $response = $this->deleteJson(route('users.reservations.destroy', [$this->user->id, $reservation->id]));
    $response->assertStatus(401);
});

test('17 - Un usuario autenticado no puede cancelar la reservación de otro usuario.', function () {
    $reservation = createReservation($this);
    $user2 = regularUser();

    $response = $this->actingAs($user2)->deleteJson(route('users.reservations.destroy', [$this->user->id, $reservation->id]));
    $response->assertStatus(403);
    $this->assertDatabaseHas('reservations', $reservation->toArray());
});

test('18 - Un usuario puede cancelar su reservación.', function () {
    $reservation = createReservation($this);

    $response = $this->actingAs($this->user)->deleteJson(route('users.reservations.destroy', [$this->user->id, $reservation->id]));
    $response->assertStatus(200);

    $this->assertDatabaseHas('reservations', ['status' => ReservationStatus::Cancelled]);

});

test('19 - Un admin no puede cancelar la reservación de otro usuario.', function () {
   $reservation = createReservation($this);

   $response = $this->actingAs($this->admin)->deleteJson(route('users.reservations.destroy', [$this->user->id, $reservation->id]));
   $response->assertStatus(403);
    $this->assertDatabaseHas('reservations', $reservation->toArray());
});

test('20 - Un usuario no autenticado no puede forzar la eliminación de una reservación.', function () {
    $reservation = createReservation($this);
    $response = $this->deleteJson(route('reservations.forcedelete', $reservation->id));
    $response->assertStatus(401);
});

test('21 - Un usuario autenticado no puede forzar la eliminación de una reservación.', function () {
   $reservation = createReservation($this);

   $response = $this->actingAs($this->user)->deleteJson(route('reservations.forcedelete', $reservation->id));
   $response->assertStatus(403);
});

test('22 - Un administrador puede forzar la eliminación de una reservación.', function () {
    $reservation = createReservation($this);

    $response = $this->actingAs($this->admin)->deleteJson(route('reservations.forcedelete', $reservation->id));
    $response->assertStatus(200);

    $this->assertSoftDeleted('reservations', $reservation->toArray());
});

test('23 - Un administrador puede restaurar una reservación eliminada', function () {
    $softDeletedReservation = softDeletedReservation($this);

    $response = $this->actingAs($this->admin)->postJson(route('users.reservations.restore', [$this->user->id, $softDeletedReservation->id]));
    $response->assertStatus(200);
});

test('24 - Un usuario no autenticado no puede ver las reservaciones eliminadas.', function () {
    $response = $this->getJson(route('reservations.viewtrashed'));
    $response->assertStatus(401);
});

test('25 - Un usuario autenticado no puede ver las reservaciones eliminadas.', function () {
    $response = $this->actingAs($this->user)->getJson(route('reservations.viewtrashed'));
    $response->assertStatus(403);
});

test('26 - Un admin puede ver las reservaciones eliminadas.', function () {
    $response = $this->actingAs($this->admin)->getJson(route('reservations.viewtrashed'));
    $response->assertStatus(200);
});
