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

test('Un admin no puede crear un paquete con un precio de 5 dígitos', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $response = $this->actingAs($user)->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'A different description',
        'long_description' => 'Long description',
        'price' => 21256,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ]
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('price');
    $this->assertDatabaseMissing('packages', ['short_description' => 'A different description']);

});

test('Un admin no puede crear un paquete con un precio de 0', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $response = $this->actingAs($user)->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'A different description',
        'long_description' => 'Long description',
        'price' => 0,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ]
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('price');
    $this->assertDatabaseMissing('packages', ['short_description' => 'A different description']);
});

test('Un admin no puede crear un paquete con un precio negativo', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $response = $this->actingAs($user)->postJson(route('packages.store'), [
        'name' => 'Nametest',
        'short_description' => 'A different description',
        'long_description' => 'Long description',
        'price' => -324,
        'characteristics' => [
            0 => 'Characteristics',
            1 => 'Characteristics',
            2 => 'Characteristics',
        ]
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('price');
    $this->assertDatabaseMissing('packages', ['short_description' => 'A different description']);
});

test('Un usuario no autenticado no puede actualizar un paquete', function () {

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->putJson(route('packages.update', $package), [
        'name' => 'Nametest2',
    ]);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('packages', ['name' => 'Nametest2']);
});

test('Un usuario autenticado no puede actualizar un paquete', function () {
    $user = User::factory()->make();
    $user->role = UserRole::User;
    $user->save();

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->actingAs($user)->putJson(route('packages.update', $package->id), [
        'name' => 'Nametest2',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('packages', ['name' => 'Nametest2']);
});

test('Un admin autenticado puede actualizar un paquete', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->actingAs($user)->putJson(route('packages.update', $package->id), [
        'name' => 'Nametest2',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('packages', ['name' => 'Nametest2']);
});

test('Un admin autenticado puede actualizar un paquete con su mismo nombre', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->actingAs($user)->putJson(route('packages.update', $package->id), [
        'name' => 'Nametest',
        'price' => 374,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('packages', ['name' => 'Nametest']);
});

test('Un usuario no autenticado no puede eliminar un paquete', function () {

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->deleteJson(route('packages.update', $package));

    $response->assertStatus(401);
    $this->assertDatabaseHas('packages', ['name' => 'Nametest']);
});

test('Un usuario autenticado no puede eliminar un paquete', function () {
    $user = User::factory()->make();
    $user->role = UserRole::User;
    $user->save();

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->actingAs($user)->deleteJson(route('packages.update', $package->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('packages', ['name' => 'Nametest']);
});

test('Un admin autenticado puede eliminar un paquete', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $package = Package::factory()->create(['name' => 'Nametest']);

    $response = $this->actingAs($user)->deleteJson(route('packages.update', $package->id));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('packages', ['name' => 'Nametest']);
});
