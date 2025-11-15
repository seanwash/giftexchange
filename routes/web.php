<?php

use App\Actions\Events\StoreEvent;
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

// Event routes
Route::get('/events/create', [StoreEvent::class, 'create'])->name('events.create');
Route::post('/events', StoreEvent::class)->name('events.store');
Route::get('/events/{event_token}', [UpdateEvent::class, 'show'])->name('events.show');
Route::patch('/events/{event_token}', UpdateEvent::class)->name('events.update');

// Exclusion routes
Route::post('/events/{event_token}/exclusions', StoreExclusion::class)->name('exclusions.store');
Route::delete('/events/{event_token}/exclusions/{exclusion}', DeleteExclusion::class)->name('exclusions.destroy');

// Participant routes
Route::get('/participant/{access_token}', ShowParticipant::class)->name('participant.enter');
Route::post('/participant/{access_token}/interests', StoreParticipantInterests::class)->name('participant.storeInterests');
Route::get('/participant/{access_token}/spin', ShowParticipantSpin::class)->name('participant.spin');
Route::post('/participant/{access_token}/spin', UpdateParticipantSpin::class)->name('participant.doSpin');
Route::get('/participant/{access_token}/assignment', ShowParticipantAssignment::class)->name('participant.assignment');
