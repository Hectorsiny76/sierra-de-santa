<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{

    use HasFactory;

    protected $table = 'room_types';
    protected $fillable = ['img_path', 'name', 'description', 'max_capacity'];
    protected $hidden = ['created_at', 'updated_at'];

    public function rooms(){
        return $this->hasMany(Room::class);
    }

    public function imgUrl():Attribute{
        return Attribute::make(
            get: fn () => $this->img_path ? asset('storage/'.$this->img_path) : null,
        );
    }

}
