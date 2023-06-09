<?php

use App\Jobs\ProcessCrawl;
use App\Jobs\ProcessCrawlMongo;
use Illuminate\Support\Facades\Route;

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
    dd('choose mysql or mongo');
});

Route::get('/mysql', function () {
    $nextPage = '"after": ""';
    ProcessCrawl::dispatch($nextPage);
    dd('dispatch mysql');
});

Route::get('/mongo', function () {
    $nextPage = '"after": ""';
    ProcessCrawlMongo::dispatch($nextPage);
    dd('dispatch mongo');
});

// Route::get('/', [VideoController::class, 'index'])->name('home');

// Route::middleware(['auth'])->group(function () {
//     Route::get('/share-movie', [VideoController::class, 'shareMovie']);
//     Route::post('/share-movie', [VideoController::class, 'upShareMovie'])->name('share-movie');
// });



// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
