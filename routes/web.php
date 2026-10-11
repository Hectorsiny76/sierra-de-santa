<?php

use App\Models\Reservation;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/fake-checkout', function (Request $request) {
    $reservation = Reservation::findOrFail($request->reservation_id);

    return response()->json([
        'message' => "This simulates a hosted payment page",
        'reservation_id' => $reservation->id,
        'total_price' => $reservation->total_price,
        'pay_url' => route('payments.fake-pay', ['reservation' => $reservation->id]),
    ]);

})->name('payments.fake-checkout');

Route::get('/fake-pay', function (Request $request) {
    return response()->json([
        'message' => "This simulates a hosted payment page",
    ]);
})->name('payments.fake-pay');
