<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    use HasFactory;

    protected $table = 'payments';
    protected $fillable = ['provider', 'provider_id', 'status', 'amount', 'raw_response', 'receipt_number', 'reservation_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function casts():array{
        return [
            'raw_response' => 'array',
        ];
    }

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }
}
