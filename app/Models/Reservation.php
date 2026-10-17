<?php

namespace App\Models;

use App\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $start_date
 * @property string $end_date
 * @property int $guest_count
 * @property int $total_price
 * @property ReservationStatus $status
 * @property int $user_id
 * @property int $room_id
 * @property int|null $package_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Package|null $package
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Room $room
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereGuestCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation withoutTrashed()
 * @mixin \Eloquent
 */
class Reservation extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'reservations';
    protected $fillable = ['start_date', 'end_date', 'guest_count', 'total_price', 'status', 'room_id', 'user_id', 'package_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function casts()
    {
        return [
          'status' => ReservationStatus::class,
        ];
    }

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
