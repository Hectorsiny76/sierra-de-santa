<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{

    use HasFactory;

    protected $table = 'room_types';
    protected $fillable = ['path', 'name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function rooms(){
        return $this->hasMany(Room::class);
    }

}
