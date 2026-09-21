<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JurnalArtikelController;
use App\Http\Controllers\Api\BulletinController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\KegiatanController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/cuaca/{kodeAdm4}', [WeatherController::class, 'show']);
Route::get('/wilayah/search', [WeatherController::class, 'search']);
// ROUTE PUBLIK — siapa saja boleh akses (untuk ditampilkan di web publik)
Route::get('/jurnal-artikel', [JurnalArtikelController::class, 'index']);
Route::get('/jurnal-artikel/{slug}', [JurnalArtikelController::class, 'show']);
Route::get('/bulletin', [BulletinController::class, 'index']);
Route::get('/bulletin/{slug}', [BulletinController::class, 'show']);

Route::get('/berita', [BeritaController::class, 'index']);
Route::get('/berita/{slug}', [BeritaController::class, 'show']);

Route::get('/kegiatan', [KegiatanController::class, 'index']);
Route::get('/kegiatan/{slug}', [KegiatanController::class, 'show']);

// ROUTE ADMIN — wajib login + role tertentu
Route::middleware(['auth:sanctum', 'role:superadmin,admin,editor'])->prefix('admin')->group(function () {
    Route::get('/kegiatan', [KegiatanController::class, 'index']);
    Route::get('/berita', [BeritaController::class, 'index']);
    Route::get('/bulletin', [BulletinController::class, 'index']);
    Route::get('/jurnal-artikel', [JurnalArtikelController::class, 'index']);
    Route::post('/jurnal-artikel', [JurnalArtikelController::class, 'store']);
    Route::put('/jurnal-artikel/{jurnalArtikel}', [JurnalArtikelController::class, 'update']);
    Route::delete('/jurnal-artikel/{jurnalArtikel}', [JurnalArtikelController::class, 'destroy']);
    Route::post('/bulletin', [BulletinController::class, 'store']);
    Route::put('/bulletin/{bulletin}', [BulletinController::class, 'update']);
    Route::delete('/bulletin/{bulletin}', [BulletinController::class, 'destroy']);

     Route::post('/berita', [BeritaController::class, 'store']);
     Route::put('/berita/{berita}', [BeritaController::class, 'update']);
     Route::delete('/berita/{berita}', [BeritaController::class, 'destroy']);

     Route::post('/kegiatan', [KegiatanController::class, 'store']);
     Route::put('/kegiatan/{kegiatan}', [KegiatanController::class, 'update']);
     Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy']);

     Route::get('/dashboard-stats', [DashboardController::class, 'stats']);
});
Route::middleware(['auth:sanctum', 'role:superadmin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});
