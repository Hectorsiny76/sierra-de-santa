<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';
    protected $fillable = ['name', 'slug', 'general_description', 'characteristics', 'price', 'room_type_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function casts():array{
        return [
            'characteristics' => 'array',
        ];
    }

    public function roomType(){
        return $this->belongsTo(RoomType::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
