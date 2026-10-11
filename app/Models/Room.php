<?php

namespace App\Models;

use App\ReservationStatus;
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

    public function images(){
        return $this->hasMany(RoomImage::class);
    }

    public function isAvailable(string $start_date, string $end_date, ?int $excludeReservationId = null): bool{
        return !$this->reservations()
            ->where('status', '!=', ReservationStatus::Cancelled)
            ->where('start_date', '<', $end_date)
            ->where('end_date', '>', $start_date)
            ->when($excludeReservationId, fn ($query) => $query->where('id', '!=', $excludeReservationId))
            ->exists();
    }
}
