<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';
    protected $fillable = ['name', 'long_description', 'short_description', 'price', 'characteristics'];
    protected $hidden = ['created_at', 'updated_at'];

    public function casts():array{
        return [
            'characteristics' => 'array',
        ];
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
