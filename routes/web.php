<?php

use App\Events\MessageCreated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

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

    Route::get('/message/created', function () {

        MessageCreated::dispatch('Petani Negeriku Anjayy');

    });

    Route::get('/messages/{receiverId}', [ChatController::class, 'fetchMessages']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);


    // Route::controller(AuthController::class)->group(function () {
    //     Route::get('register', 'register')->name('register');
    //     Route::post('register', 'registerSimpan')->name('register.simpan');
    //     Route::get('login', 'login')->name('login');
    //     Route::post('login', 'loginAksi')->name('login.aksi');
    //     Route::get('logout', 'logout')->middleware('auth')->name('logout');

    // });
