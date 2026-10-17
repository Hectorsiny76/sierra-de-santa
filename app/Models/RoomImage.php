<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $path
 * @property string $name
 * @property string|null $description
 * @property int $room_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read \App\Models\Room $room
 * @method static \Database\Factories\RoomImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoomImage extends Model
{

    use HasFactory;

    protected $table = 'room_images';
    protected $fillable = ['room_id', 'path', 'name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function room(){
        return $this->belongsTo(Room::class);
    }

    public function imageUrl():Attribute{
        return Attribute::make(
            get: fn () => $this->path ? asset('storage/'.$this->path) : null,
        );
    }
}
