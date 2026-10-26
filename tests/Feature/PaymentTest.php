<?php

use App\Models\Package;
use App\Models\Reservation;
use App\Models\Room;
use App\PaymentStatus;
use App\ReservationStatus;

beforeEach(function () {
    $this->user = regularUser();
    $this->admin = adminUser();
    $this->room = Room::factory()->create();
    $this->package = Package::factory()->create();
});

test('1 - Un usuario no autenticado no puede realizar el pago de una reservación.', function () {
    $reservacion = createReservation($this);

    $response = $this->postJson(route('reservations.checkout',[1, $reservacion->id]));

    $response->assertStatus(401);
});

test('2 - Un usuario autenticado no puede realizar la preparación de pago de una reservación de un otro usuario.', function () {
    $reservacion = createReservation($this);
    $user2 = regularUser();
    $response = $this->actingAs($user2)->postJson(route('reservations.checkout',[1, $reservacion->id, $user2->id]));
    $response->assertStatus(403);
});

test('3 - Un admin no puede realizar la preparación de pago de una reservación de otro usuario.', function () {
    $reservacion = createReservation($this);

    $response = $this->actingAs($this->admin)->postJson(route('reservations.checkout',[1, $reservacion->id]));
    $response->assertStatus(403);
});

test('4 - Un usuario autenticado puede realizar la preparación de pago de una de sus reservaciones.', function () {
    $reservacion = createReservation($this);
    $response = $this->actingAs($this->user)->postJson(route('reservations.checkout',[$this->user->id, $reservacion->id]));
    $response->assertStatus(200)->assertJsonStructure(['checkout_url']);
});

test('5 - Un usuario autenticado no puede realizar la preparación de pago de una reservación que tenga otro status que no sea pendiente.', function () {
    $reservacion = createReservation($this);
    $reservacion->status = ReservationStatus::Cancelled;
    $reservacion->save();

    $response = $this->actingAs($this->user)->postJson(route('reservations.checkout',[$this->user->id, $reservacion->id]));
    $response->assertStatus(409)->assertJsonStructure(['message']);
});

test('6 - El Webhook confirma el pago de una reservación.', function () {
    $reservation = createReservation($this);
    $payload = json_encode([
        'reservation_id' => $reservation->id,
        'status' => 'succeeded',
        'provider_id' => 'fake_test_123',
    ]);

    $response = $this->postJson(route('reservations.webhook'), json_decode($payload, true), [
        'Stripe-Signature' => 'fake-signature',
    ]);

    $response->assertStatus(200);

    expect($reservation->fresh()->status)->toBe(ReservationStatus::Confirmed);
    $this->assertDatabaseHas('payments', [
        'reservation_id' => $reservation->id,
        'provider_id' => 'fake_test_123',
        'status' => PaymentStatus::Succeeded,
    ]);
});
