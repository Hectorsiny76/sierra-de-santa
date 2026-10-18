<?php

use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Collection;

test('Cualquiera puede ver los paquetes disponibles', function () {
    $packages = Package::factory(10)->create();

    $response = $this->get(route('packages.index'));

    expect($packages->count())->toBe(10)
        ->and($packages)->toBeInstanceOf(Collection::class);
    $response->assertStatus(200);
});

test('Cualquiera puede ver un paquete en especifico', function () {
    $package = Package::factory()->create();

    $response = $this->get(route('packages.show', $package->id));

    expect($package)->toBeInstanceOf(Package::class)
    ->and($package->count())->toBe(1);
    $response->assertStatus(200);
});

test('Un usuario no autenticado no puede crear un paquete', function () {
    $response = $this->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'Short description',
        'long_description' => 'Long description',
        'price' => 212,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ],
    ]);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('packages', ['name' => 'Nametest']);
});

test('Un usuario autenticado no puede crear un paquete', function () {
    $user = User::factory()->make();
    $user->role = UserRole::User;
    $user->save();

    $response = $this->actingAs($user)->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'Short description',
        'long_description' => 'Long description',
        'price' => 212,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ],
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('packages', ['name' => 'Nametest']);
});

test('Un admin autenticado puede crear un paquete', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();
    $response = $this->actingAs($user)->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'Short description',
        'long_description' => 'Long description',
        'price' => 212,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ],
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('packages', ['name' => 'Nametest']);
});

test('Un admin no puede crear dos paquetes con el mismo nombre', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    Package::factory()->create(['name' => 'Nametest']);

    $response = $this->actingAs($user)->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'A different description',
        'long_description' => 'Long description',
        'price' => 212,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ]
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('name');
    $this->assertDatabaseMissing('packages', ['short_description' => 'A different description']);

});
