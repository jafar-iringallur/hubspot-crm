<?php

// use App\Http\Controllers\HubspotContactController;

use App\Http\Controllers\ProfileController;
use App\Modules\HubSpot\Controllers\HubspotAccountController;
use App\Modules\HubSpot\Controllers\HubspotContactController;
use App\Modules\HubSpot\Controllers\HubspotWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
   return redirect('/hubspot');
});

Route::get('/dashboard', function () {
    return redirect('/hubspot');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('hubspot')->group(function () {
        Route::get('/', [HubspotAccountController::class, 'index'])->name('hubspot.index');
        Route::get('/authenticate', [HubspotAccountController::class, 'authenticate'])->name('hubspot.authenticate');
        Route::get('/callback', [HubspotAccountController::class, 'callback'])->name('hubspot.callback');
        Route::get('/accounts', [HubspotAccountController::class, 'getAccounts'])->name('hubspot.accounts');
        
        Route::get('/{id}/contacts', [HubspotContactController::class, 'index'])->name('hubspot.contacts.index');
        Route::get('/{id}/getContacts', [HubspotContactController::class, 'getContacts'])->name('hubspot.contacts.getContacts');

        Route::post('/contacts/import', [HubspotContactController::class, 'import'])->name('hubspot.contacts.import');
        Route::post('/contacts', [HubspotContactController::class, 'store'])->name('hubspot.contacts.store');
        Route::put('/contacts/{id}', [HubspotContactController::class, 'update'])->name('hubspot.contacts.update');
        Route::delete('/contacts/{id}', [HubspotContactController::class, 'destroy'])->name('hubspot.contacts.destroy');
    });

   

});

Route::post('/hubspot/webhook', [HubspotWebhookController::class, 'handle'])->name('hubspot.webhook');

require __DIR__.'/auth.php';
