<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{

    use HasFactory;

    protected $table = 'reservations';
    protected $fillable = ['start_date', 'end_date', 'guest_count', 'total_price', 'status', 'room_id', 'user_id', 'package_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function room(){
        return $this->belongsTo(Room::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function package(){
        return $this->belongsTo(Package::class);
    }
     public function payment(){
        return $this->hasOne(Payment::class);
     }
}
