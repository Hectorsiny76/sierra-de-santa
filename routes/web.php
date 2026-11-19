<?php

use App\Http\Controllers\PaymentController;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

Route::get('/fake-checkout', function (Request $request) {
    $reservation = Reservation::findOrFail($request->reservation_id);

    return response()->json([
        'message' => "This simulates a hosted payment page",
        'reservation_id' => $reservation->id,
        'total_price' => $reservation->total_price,
        'pay_url' => route('payments.fake-pay', ['reservation' => $reservation->id]),
    ]);

})->name('payments.fake-checkout');

Route::get('/fake-pay', [PaymentController::class, 'fakePay'])->name('payments.fake-pay');

Route::post('/reservations/payments/webhook', [PaymentController::class, 'webhook'])->name('reservations.webhook');

Route::get('/metrics', function (CollectorRegistry $registry) {
    $text = (new RenderTextFormat())->render($registry->getMetricFamilySamples());
    return response($text, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
});

Route::get('/test-error', fn () => abort(500));

Route::get('/flaky', function () {
    if (rand(1,10) === 1) {
        throw new \RuntimeException('Flaky failure');
    }
    return 'ok';
});

Route::get('/slow', function () {
    DB::select('select pg_sleep(0.5)');
    return 'done';
});

if(app()->environment('local')) {
    Route::prefix('lab')->group(function () {
        // Ejecucion del problema Query N+1
        Route::get('/n-plus-one', function () {
           $roomTypes = [];
           foreach( Room::all() as $room ) {
               $roomTypes[] = $room->roomType->name;
           }
           return count($roomTypes) . ' cuartos';
        });

        // Arreglo del problema
        Route::get('/eager', function () {
           $roomTypes = [];
           foreach( Room::with('roomType')->get() as $room ) {
               $roomTypes[] = $room->roomType->name;
           }
           return count($roomTypes) . ' cuartos';
        });
        Route::get('/env', fn () => getenv('OTEL_EXPORTER_OTLP_ENDPOINT') ?: 'missing');
    });
}
