<?php

use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
   Storage::fake('public');
   $this->service = new ImageService();
   $this->image = UploadedFile::fake()->image('test.jpg');
});

test('Una imagen puede ser guardada para su visualización publica', function () {

    $path = $this->service->store($this->image, 'rooms');

    expect($path)->toStartWith('rooms/');
    Storage::disk('public')->assertExists($path);

});

test('Una imagen puede ser eliminada', function () {
    $path = $this->service->store($this->image, 'rooms');
    $this->service->delete($path);
    Storage::disk('public')->assertMissing($path);
});

test('Eliminar una imagen que no existe no lanza error', function () {
    $this->service->delete(null);
    expect(true)->toBeTrue();
});

test('Se puede reemplazar una imagen ya existente', function () {
    $path = $this->service->store($this->image, 'rooms');
    $newImage = UploadedFile::fake()->image('test2.jpg');
    $newPath = $this->service->replace($path, $newImage, 'rooms');
    expect($newPath)->toStartWith('rooms/');
    Storage::disk('public')->assertExists($newPath);
});
