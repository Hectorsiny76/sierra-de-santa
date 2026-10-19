<?php

use App\Models\Room;
use App\Models\Package;
use App\Services\ReservationPricingService;

beforeEach(function () {
    $this->service = new ReservationPricingService();
});

function createRoom(): Room{
    return Room::factory()->create([
        'price' => 100
    ]);
}

function createPackage(): Package{
    return Package::factory()->create([
        'price' => 300
    ]);
}

test('Calcula el total de la estadia con un cuarto y su paquete', function () {

    $room = createRoom();
    $package = createPackage();

    $total = $this->service->calculateTotal($room, $package, '2026-10-22', '2026-10-26');

    expect($total)->toBe(700);

});

test('Calcula el total de la estadia con solo un cuarto', function () {
    $room = createRoom();
    $total = $this->service->calculateTotal($room, null,'2026-10-22', '2026-10-26');

    expect($total)->toBe(400);
});
