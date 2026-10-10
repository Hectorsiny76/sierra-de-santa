<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function store(UploadedFile $file, string $directory = 'images') : string{
        return $file->store($directory, 'public');
    }

    public function delete(?string $path){
        if($path){
            Storage::disk('public')->delete($path);
        }
    }

    public function replace(?string $oldPath, UploadedFile $newFile, string $directory = 'images') : string{
        $this->delete($oldPath);
        return $this->store($newFile, $directory);
    }
}
