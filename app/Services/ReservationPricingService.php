<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Room;
use Carbon\Carbon;

class ReservationPricingService
{
    public function calculateTotal(Room $room, ?Package $package, string $startDate, string $endDate): int{
        $nights = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

        $total = $room->price * $nights;

        if($package) {
            $total += $package->price;
        }

        return $total;
    }
}
