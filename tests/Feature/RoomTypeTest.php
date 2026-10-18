<?php

use App\Models\RoomType;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

test('Cualquiera puede ver los tipos de habitaciones disponibles', function () {

    $roomTypes = RoomType::factory(10)->create();

    $response = $this->getJson(route('room-types.index'));

    expect($roomTypes)->toBeInstanceOf(Collection::class);
    $response->assertStatus(200);
    $this->assertDatabaseHas('room_types', $roomTypes->toArray());

});

test('Cualquiera puede ver un tipo de habitación disponible', function () {
    $roomType = RoomType::factory()->create();

    $response = $this->getJson(route('room-types.show', $roomType->id));

    expect($roomType)->toBeInstanceOf(RoomType::class);
    $response->assertStatus(200);
    $this->assertDatabaseHas('room_types', $roomType->toArray());
});

test('Un usuario no autenticado no puede acceder a la creación de un nuevo tipo de habitación', function () {
    $response = $this->postJson(route('room-types.store'), [
        'name' => 'testRoomType',
        'description' => 'testRoomTypeDescription',
        'max_capacity' => 6
    ]);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('room_types', ['name' => 'testRoomType']);
});

test('Un usuario autenticado NO puede crear un nuevo tipo de habitación', function () {
    $user = User::factory()->create();

    $user->role = UserRole::User;

    $user->save();

    $response = $this->actingAs($user)->postJson(route('room-types.store'), [
        'name' => 'testRoomType',
        'description' => 'testRoomTypeDescription',
        'max_capacity' => 6
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('room_types', ['name' => 'testRoomType']);

});

test('Un admin puede crear un tipo de habitación', function () {
    $user = User::factory()->make();

    $user->role = UserRole::Admin;

    $user->save();

    $response = $this->actingAs($user)->postJson(route('room-types.store'), [
        'name' => 'testRoomType',
        'description' => 'testRoomTypeDescription',
        'max_capacity' => 6
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('room_types', ['name' => 'testRoomType']);
});

test('Un admin puede actualizar el nombre de un tipo de habitación sin quebrar la regla de nombre único', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();
    $roomType = RoomType::factory()->create([
        'name' => 'testRoomType',
    ]);

    $response = $this->actingAs($user)->putJson(route('room-types.update', $roomType->id), [
        'name' => 'testRoomType',
        'description' => 'testRoomTypeDescription',
        'max_capacity' => 6
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('room_types', ['name' => 'testRoomType']);
});

test('Un admin puede agregar una imagen a un tipo de habitación', function () {
    Storage::fake('public');

    $roomType = RoomType::factory()->create();

    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->actingAs($user)->putJson(route('room-types.update', $roomType->id), [
        'image' => $file,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('room_types', ['name' => $roomType->name]);
});

test('Un administrador no puede asignar una imagen muy grande a un tipo de habitación', function () {
    Storage::fake('public');
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();
    $roomType = RoomType::factory()->create();
    $file = UploadedFile::fake()->create('test.jpg', 3000);

    $response = $this->actingAs($user)->putJson(route('room-types.update', $roomType->id), [
        'image' => $file,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('image');
});

test('Un usuario no puede actualizar un tipo de habitación', function () {
    $user = User::factory()->make();
    $user->role = UserRole::User;
    $user->save();
    $roomType = RoomType::factory()->create();

    $response = $this->actingAs($user)->putJson(route('room-types.update', $roomType->id), [
        'name' => 'testRoomType2',
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('room_types', ['name' => 'testRoomType2']);

});

test('Un usuario no puede eliminar un tipo de habitación', function () {
    $user = User::factory()->make();
    $user->role = UserRole::User;
    $user->save();
    $roomType = RoomType::factory()->create();

    $response = $this->actingAs($user)->deleteJson(route('room-types.destroy', $roomType->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('room_types', ['name' => $roomType->name]);
});

test('Un admin puede eliminar un tipo de habitación', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $roomType = RoomType::factory()->create();

    $response = $this->actingAs($user)->deleteJson(route('room-types.destroy', $roomType->id));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('room_types', ['name' => $roomType->name]);
});

test('Dos tipos de habitaciones no pueden tener el mismo nombre', function () {
    $roomType1 = RoomType::factory()->create([
        'name' => 'testRoomType1',
    ]);

    $this->expectException(UniqueConstraintViolationException::class);

    $roomType2 = RoomType::factory()->create([
        'name' => 'testRoomType1',
    ]);
});

test('Al crear dos habitaciones con el mismo nombre retorna un error con el nombre', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();
    RoomType::factory()->create([
        'name' => 'testRoomType1',
    ]);

    $response = $this->actingAs($user)->postJson(route('room-types.store'), [
        'name' => 'testRoomType1',
        'description' => 'testRoomType1Description',
        'max_capacity' => 6
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('name');
});

test('La capacidad maxima de un tipo de habitación no puede ser menor a 1', function () {

    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $response = $this->actingAs($user)->postJson(route('room-types.store'), [
        'name' => 'testRoomType1',
        'description' => 'testRoomType1Description',
        'max_capacity' => 0
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('max_capacity');
    $this->assertDatabaseMissing('room_types', ['max_capacity' => 0]);

});

test('La capacidad maxima de una habitacion no puede ser mayor a 6', function () {
    $user = User::factory()->make();
    $user->role = UserRole::Admin;
    $user->save();

    $response = $this->actingAs($user)->postJson(route('room-types.store'), [
        'name' => 'testRoomType1',
        'description' => 'testRoomType1Description',
        'max_capacity' => 7
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('max_capacity');
    $this->assertDatabaseMissing('room_types', ['max_capacity' => 7]);
});
