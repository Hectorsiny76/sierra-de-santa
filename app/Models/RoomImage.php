<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{

    use HasFactory;

    protected $table = 'room_images';
    protected $fillable = ['room_id', 'path', 'name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function room(){
        return $this->belongsTo(Room::class);
    }
}
