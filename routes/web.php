<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Jemaat\ChatbotController;
use App\Http\Controllers\Jemaat\JadwalController as JemaatJadwalController;
use App\Http\Controllers\Jemaat\KuesionerController;
use App\Http\Controllers\Jemaat\PermohonanDoaController;
use App\Http\Controllers\Jemaat\RekomendasiController;
use App\Http\Controllers\Jemaat\ReservasiController;
use App\Http\Controllers\Jemaat\WartaController;
use App\Http\Controllers\Pengurus\AbsenController;
use App\Http\Controllers\Pengurus\JadwalController as PengurusJadwalController;
use App\Http\Controllers\Pengurus\KontenController;
use App\Http\Controllers\Pengurus\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/register/biometric', [AuthController::class, 'showBiometric'])->name('register.biometric');
    Route::post('/register/biometric', [AuthController::class, 'storeBiometric'])->name('register.biometric.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('jemaat')->name('jemaat.')->group(function () {
        Route::get('/jadwal', [JemaatJadwalController::class, 'index'])->name('jadwal');
        Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi');
        Route::post('/reservasi/{jadwal}', [ReservasiController::class, 'store'])->name('reservasi.store');
        Route::get('/doa', [PermohonanDoaController::class, 'index'])->name('doa');
        Route::post('/doa', [PermohonanDoaController::class, 'store'])->name('doa.store');
        Route::get('/warta', [WartaController::class, 'index'])->name('warta');
        Route::get('/warta/{warta:slug}', [WartaController::class, 'show'])->name('warta.show');
        Route::get('/kuesioner', [KuesionerController::class, 'index'])->name('kuesioner');
        Route::post('/kuesioner', [KuesionerController::class, 'store'])->name('kuesioner.store');
        Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
        Route::post('/rekomendasi/tim', [RekomendasiController::class, 'pilihTim'])->name('rekomendasi.tim');
        Route::post('/rekomendasi/cool', [RekomendasiController::class, 'pilihCool'])->name('rekomendasi.cool');
        Route::get('/chat', [ChatbotController::class, 'index'])->name('chat');
        Route::post('/chat/send', [ChatbotController::class, 'send'])->name('chat.send');
    });

    Route::prefix('pengurus')->name('pengurus.')->middleware('role:pengurus')->group(function () {
        Route::get('/jadwal', [PengurusJadwalController::class, 'index'])->name('jadwal');
        Route::post('/jadwal', [PengurusJadwalController::class, 'store'])->name('jadwal.store');
        Route::delete('/jadwal/{jadwal}', [PengurusJadwalController::class, 'destroy'])->name('jadwal.destroy');
        Route::get('/konten', [KontenController::class, 'index'])->name('konten');
        Route::post('/konten', [KontenController::class, 'store'])->name('konten.store');
        Route::delete('/konten/{konten}', [KontenController::class, 'destroy'])->name('konten.destroy');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/absen', [AbsenController::class, 'index'])->name('absen');
        Route::post('/absen/verify', [AbsenController::class, 'verify'])->name('absen.verify');
        Route::get('/absen/list', [AbsenController::class, 'todayList'])->name('absen.list');
    });
});
