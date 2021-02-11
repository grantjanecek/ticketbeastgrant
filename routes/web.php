<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ConcertsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConcertOrdersController;
use App\Http\Controllers\ConcertMessagesController;
use App\Http\Controllers\PublishConcertsController;
use App\Http\Controllers\Backstage\PublishedConcertOrdersController;
use App\Http\Controllers\Backstage\ConcertsController as BackStageConcertsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/concerts/{concert}', [ConcertsController::class, 'show'])->name('concerts.show');
Route::post('/concerts/{concert}/orders', [ConcertOrdersController::class, 'store']);
Route::get('/orders/{confirmationNumber}', [OrdersController::class, 'show']);

Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/register', [RegisterController::class, 'register']);

Route::get('/invitations/{code}', [InvitationsController::class, 'show']);

Route::group(['middleware' => 'auth', 'prefix' => 'backstage'], function(){
    Route::get('/concerts', [BackStageConcertsController::class, 'index'])->name('backstage.concerts.index');
    Route::get('/concerts/new', [BackstageConcertsController::class, 'create'])->name('backstage.concerts.create');
    Route::post('/concerts/new', [BackstageConcertsController::class, 'store'])->name('backstage.concerts.store');
    Route::get('/concerts/{id}/edit', [BackstageConcertsController::class, 'edit'])->name('backstage.concerts.edit');
    Route::patch('/concerts/{id}/edit', [BackstageConcertsController::class, 'update'])->name('backstage.concerts.update');

    Route::post('/publish-concert', [PublishConcertsController::class, 'store'])->name('backstage.publish-concert.store');
    Route::get('/published-concerts/{id}/orders', [PublishedConcertOrdersController::class, 'index']);

    Route::get('/concerts/{id}/messages/new', [ConcertMessagesController::class, 'create'])->name('backstage.message-concert.create');
    Route::post('/concerts/{id}/messages', [ConcertMessagesController::class, 'store']);
});
