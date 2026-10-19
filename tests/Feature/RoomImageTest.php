<?php

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\UploadedFile;

beforeEach(function(){

    $this->admin = adminUser();
    $this->user = regularUser();

    $this->room = Room::factory()->create();
    $this->secondRoom = Room::factory()->create();

    $this->image = UploadedFile::fake()->image('test.jpg');
    $this->description = 'description-test';

    $this->images = [
      0 => UploadedFile::fake()->image('test1.jpg'),
      1 => UploadedFile::fake()->image('test2.jpg'),
        2 => UploadedFile::fake()->image('test3.jpg'),
        3 => UploadedFile::fake()->image('test4.jpg'),
    ];
    $this->descriptions = [
      0 => 'description-test1',
      1 => 'description-test2',
      2 => 'description-test3',
      3 => 'description-test4',
    ];

});
function createExistingRoomImage($test): RoomImage{

    return RoomImage::factory()->create([
        'path' => 'rooms/test.jpg',
        'description' => 'another description',
        'name' => 'A cozy room',
        'room_id' => $test->room->id,
    ]);
}

test('Un usuario no autenticado no puede guardar una imagen con su cuarto', function () {
    $response = $this->postJson(route('room.images.store', $this->room->id), [
        'image' => $this->image,
        'description' => 'another-description-test',
    ]);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('room_images', [
        'description' => 'another-description-test',
    ]);
});

test('Un usuario autenticado no puede guardar una imagen con su cuarto', function () {
    $response = $this->actingAs($this->user)->postJson(route('room.images.store', $this->room->id), [
        'image' => $this->image,
        'description' => 'another-description-test',
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('room_images', [
        'description' => 'another-description-test',
    ]);
});

test('Una imagen puede ser guardada con su cuarto', function () {
    $response = $this->actingAs($this->admin)->postJson(route('room.images.store', $this->secondRoom->id), [
        'description' => $this->description,
        'image' => $this->image,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('room_images', [
        'description' => $this->description,
    ]);
});

test('Una imagen mayor a 2MB no puede ser guardada', function () {

    $largeImage = UploadedFile::fake()->create('test.jpg', 3000);

    $response = $this->actingAs($this->admin)->postJson(route('room.images.store', $this->room->id), [
        'image' => $largeImage,
        'description' => 'A chubby image'
    ]);
    $response->assertStatus(422)->assertJsonValidationErrors('image');
    $this->assertDatabaseMissing('room_images', [
        'description' => 'A chubby image',
    ]);
});

test('Solo imágenes pueden ser guardadas', function () {
    $otherFile = UploadedFile::fake()->create('test.pdf');

    $response = $this->actingAs($this->admin)->postJson(route('room.images.store', $this->room->id), [
        'image' => $otherFile,
        'description' => "A nice pdf"
    ]);
    $response->assertStatus(422)->assertJsonValidationErrors('image');
    $this->assertDatabaseMissing('room_images', [
        'description' => 'A nice pdf',
    ]);
});

test('Un admin puede guardar varias imágenes', function () {
    $response = $this->actingAs($this->admin)->postJson(route('room.images.storeMany', $this->secondRoom->id), [
        'images' => $this->images,
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('room_images', [
        'room_id' => $this->secondRoom->id,
    ]);
});

test('Un admin no puede guardar mas de 10 imágenes', function () {
    $images = [
        0 => UploadedFile::fake()->image('test.jpg'),
        1 => UploadedFile::fake()->image('test.jpg'),
        2 => UploadedFile::fake()->image('test.jpg'),
        3 => UploadedFile::fake()->image('test.jpg'),
        4 => UploadedFile::fake()->image('test.jpg'),
        5 => UploadedFile::fake()->image('test.jpg'),
        6 => UploadedFile::fake()->image('test.jpg'),
        7 => UploadedFile::fake()->image('test.jpg'),
        8 => UploadedFile::fake()->image('test.jpg'),
        9 => UploadedFile::fake()->image('test.jpg'),
        10 => UploadedFile::fake()->image('test.jpg'),
        11 => UploadedFile::fake()->image('test.jpg'),
    ];

    $response = $this->actingAs($this->admin)->postJson(route('room.images.storeMany', $this->secondRoom->id), [
        'images' => $images,
    ]);
    $response->assertStatus(422)->assertJsonValidationErrors('images');
    $this->assertDatabaseMissing('room_images', [
        'room_id' => $this->secondRoom->id,
    ]);
});

test('Un usuario no autenticado no puede actualizar una imagen', function () {
    $newImage = UploadedFile::fake()->image('test1.jpg');

    $existingImage = createExistingRoomImage($this);

    $response = $this->patchJson(route('room.images.update', $existingImage->id), [
        'image' => $newImage,
        'description' => 'another-description-test',
    ]);
    $response->assertStatus(401);
    $this->assertDatabaseMissing('room_images', [
        'description' => 'another-description-test',
    ]);
});

test('Un admin puede actualizar una imagen', function () {

    $newImage = UploadedFile::fake()->image('test1.jpg');

    $existingImage = createExistingRoomImage($this);

    $response = $this->actingAs($this->admin)->patchJson(route('room.images.update', $existingImage->id), [
        'image' => $newImage,
        'description' => 'A brand new image'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('room_images', [
        'description' => 'A brand new image'
    ]);
});

test('Un admin puede actualizar la descripción de una imagen', function () {

    $existingImage = createExistingRoomImage($this);

    $response = $this->actingAs($this->admin)->patchJson(route('room.images.update', $existingImage->id), [
        'description' => $this->description,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('room_images', [
        'description' => $this->description,
    ]);
});

test('Un usuario no autenticado no puede eliminar una imagen de un cuarto', function () {

    $existingImage = createExistingRoomImage($this);

    $response = $this->deleteJson(route('room.images.destroy', $existingImage->id));

    $response->assertStatus(401);
    $this->assertDatabaseHas('room_images', [
        'name' => 'A cozy room',
    ]);
});

test('Un usuario autenticado no puede eliminar una imagen de un cuarto', function () {
    $existingImage = createExistingRoomImage($this);

    $response = $this->actingAs($this->user)->deleteJson(route('room.images.destroy', $existingImage->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('room_images', [
        'name' => 'A cozy room',
    ]);
});

test('Un admin puede eliminar una imagen de un cuarto', function () {
    $existingImage = createExistingRoomImage($this);

    $response = $this->actingAs($this->admin)->deleteJson(route('room.images.destroy', $existingImage->id));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('room_images', [
        'name' => 'A cozy room',
    ]);
});
