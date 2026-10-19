<?php

use App\Models\Room;
use App\Models\RoomType;

beforeEach(function () {
    $this->roomType = RoomType::factory()->create();
    $this->admin = adminUser();
    $this->user = regularUser();
    $this->room = Room::factory()->create([
        'name' => 'Nametest',
        'slug' => 'name-test',
        'room_type_id' => $this->roomType->id,
    ]);
});

test('Cualquiera puede ver los cuartos disponibles', function () {
    $response = $this->getJson(route('rooms.index'));

    $response->assertStatus(200);
    $this->assertDatabaseHas('rooms', [
        'name' => 'Nametest',
    ]);
});

test('Cualquiera puede un cuarto en especifico', function () {

    $response = $this->getJson(route('rooms.show', $this->room->id));

    $response->assertStatus(200);
    $this->assertDatabaseHas('rooms', [
        'name' => 'Nametest',
    ]);
});

test('Un usuario no autenticado no puede crear un cuarto', function () {

    $response = $this->postJson(route('rooms.store'), [
        'name' => 'Nametest2',
        'slug' => 'name-test',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 451,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un usuario autenticado no puede crear un cuarto', function () {

    $response = $this->actingAs($this->user)->postJson(route('rooms.store'), [
        'name' => 'Nametest2',
        'slug' => 'name-test2',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 451,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin puede crear un cuarto', function () {

    $response = $this->actingAs($this->admin)->postJson(route('rooms.store'), [
        'name' => 'Nametest2',
        'slug' => 'name-test2',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 451,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin no puede crear un cuarto con el mismo nombre que otro previamente creado', function () {

    $response = $this->actingAs($this->admin)->postJson(route('rooms.store'), [
        'name' => 'Nametest',
        'slug' => 'name-test2',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 451,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('name');
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin no puede crear un cuarto con el mismo slug que otro previamente creado', function () {
    $response = $this->actingAs($this->admin)->postJson(route('rooms.store'), [
        'name' => 'Nametest2',
        'slug' => 'name-test',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 451,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('slug');
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin no puede crear un cuarto con un id de tipo de cuarto que no existe', function () {
    $response = $this->actingAs($this->admin)->postJson(route('rooms.store'), [
        'name' => 'Nametest2',
        'slug' => 'name-test2',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 451,
        'room_type_id' => 412,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('room_type_id');
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin no puede crear un cuarto con un precio menor a 50', function () {
    $response = $this->actingAs($this->admin)->postJson(route('rooms.store'), [
        'name' => 'Nametest2',
        'slug' => 'name-test2',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 45,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('price');
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin no puede crear un cuarto con un precio mayor a 9999', function () {
    $response = $this->actingAs($this->admin)->postJson(route('rooms.store'), [
        'name' => 'Nametest',
        'slug' => 'name-test',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 45123,
        'room_type_id' => $this->roomType->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('price');
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un usuario no autenticado no puede actualizar un cuarto', function () {
    $response = $this->putJson(route('rooms.update', $this->room->id), [
        'name' => 'Nametest2',
    ]);
    $response->assertStatus(401);
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un usuario autenticado no puede actualizar un cuarto', function () {
    $response = $this->actingAs($this->user)->putJson(route('rooms.update', $this->room->id), [
        'name' => 'Nametest2',
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin puede actualizar un cuarto', function () {
    $response = $this->actingAs($this->admin)->putJson(route('rooms.update', $this->room->id), [
        'name' => 'Nametest2',
        'slug' => 'name-test2',
        'general_description' => 'A general description made for testing.',
        'characteristics' => [
            0 => 'Hello'
        ],
        'price' => 152,
        'room_type_id' => $this->roomType->id,
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('rooms', [
        'name' => 'Nametest2',
    ]);
});

test('Un admin puede actualizar un cuarto con el mismo nombre', function () {
    $response = $this->actingAs($this->admin)->putJson(route('rooms.update', $this->room->id), [
        'name' => 'Nametest',
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('rooms', [
        'name' => 'Nametest',
    ]);
});

test('Un admin puede actualizar un cuarto con el mismo slug', function () {
    $response = $this->actingAs($this->admin)->putJson(route('rooms.update', $this->room->id), [
        'slug' => 'name-test',
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('rooms', [
        'slug' => 'name-test',
    ]);
});

test('Un usuario no autenticado no puede eliminar un cuarto', function () {
    $response = $this->deleteJson(route('rooms.destroy', $this->room->id));
    $response->assertStatus(401);
    $this->assertDatabaseHas('rooms', [
        'slug' => 'name-test',
    ]);
});

test('Un usuario autenticado no puede eliminar un cuarto', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('rooms.destroy', $this->room->id));
    $response->assertStatus(403);
    $this->assertDatabaseHas('rooms', [
        'slug' => 'name-test',
    ]);
});

test('Un admin puede eliminar un cuarto', function () {
    $response = $this->actingAs($this->admin)->deleteJson(route('rooms.destroy', $this->room->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('rooms', [
        'slug' => 'name-test',
    ]);
});
