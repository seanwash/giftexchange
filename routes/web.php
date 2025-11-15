<?php

use App\Actions\Events\SendTestPushNotification;
use App\Actions\Events\StoreEvent;
use App\Actions\Events\SubscribeToPushNotifications;
use App\Actions\Events\UnsubscribeFromPushNotifications;
use App\Actions\Events\UpdateEvent;
use App\Actions\Exclusions\DeleteExclusion;
use App\Actions\Exclusions\StoreExclusion;
use App\Actions\Participants\ShowParticipant;
use App\Actions\Participants\ShowParticipantAssignment;
use App\Actions\Participants\ShowParticipantSpin;
use App\Actions\Participants\StoreParticipantInterests;
use App\Actions\Participants\UpdateParticipantSpin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// WebPush VAPID public key route
Route::get('/api/webpush/vapid-public-key', function () {
    return response()->json([
        'publicKey' => config('webpush.vapid.public_key'),
    ]);
})->name('webpush.vapid-public-key');

// Event routes
Route::get('/events/create', [StoreEvent::class, 'create'])->name('events.create');
Route::post('/events', StoreEvent::class)->name('events.store');
Route::get('/events/{event_token}', [UpdateEvent::class, 'show'])->name('events.show');
Route::patch('/events/{event_token}', UpdateEvent::class)->name('events.update');
Route::post('/events/{event_token}/push/subscribe', SubscribeToPushNotifications::class)->name('events.push.subscribe');
Route::post('/events/{event_token}/push/unsubscribe', UnsubscribeFromPushNotifications::class)->name('events.push.unsubscribe');
Route::post('/events/{event_token}/push/test', SendTestPushNotification::class)->name('events.push.test');

// Exclusion routes
Route::post('/events/{event_token}/exclusions', StoreExclusion::class)->name('exclusions.store');
Route::delete('/events/{event_token}/exclusions/{exclusion}', DeleteExclusion::class)->name('exclusions.destroy');

// Participant routes
Route::get('/participant/{access_token}', ShowParticipant::class)->name('participant.enter');
Route::post('/participant/{access_token}/interests', StoreParticipantInterests::class)->name('participant.storeInterests');
Route::get('/participant/{access_token}/spin', ShowParticipantSpin::class)->name('participant.spin');
Route::post('/participant/{access_token}/spin', UpdateParticipantSpin::class)->name('participant.doSpin');
Route::get('/participant/{access_token}/assignment', ShowParticipantAssignment::class)->name('participant.assignment');
