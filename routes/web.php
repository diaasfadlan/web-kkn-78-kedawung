<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\WorkProgramController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\VillageProfileController;
use App\Http\Controllers\GroupProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/profil-desa', [PublicController::class, 'profileDesa'])->name('profil-desa');
Route::get('/profil-kelompok', [PublicController::class, 'profileKelompok'])->name('profil-kelompok');
Route::get('/profil-kelompok/{id}', [PublicController::class, 'memberDetail'])->name('profil-kelompok.detail');
Route::get('/program-kerja', [PublicController::class, 'programKerja'])->name('program-kerja');
Route::get('/program-kerja/{id}', [PublicController::class, 'programKerjaDetail'])->name('program-kerja.detail');
Route::get('/artikel', [PublicController::class, 'artikel'])->name('artikel');
Route::get('/artikel/{id}', [PublicController::class, 'artikelDetail'])->name('artikel.detail');
Route::get('/galeri', [PublicController::class, 'galeri'])->name('galeri');
Route::get('/timeline', [PublicController::class, 'timeline'])->name('timeline');
Route::get('/kontak', [PublicController::class, 'kontak'])->name('kontak');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['firebase.auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('artikel', ArticleController::class)->names('artikel')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('program-kerja', WorkProgramController::class)->names('program')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('galeri', GalleryController::class)->names('gallery')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('timeline', TimelineController::class)->names('timeline')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('profil-desa', VillageProfileController::class)->names('village')->only(['edit', 'update']);
    Route::resource('profil-kelompok', GroupProfileController::class)->names('group')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('kontak', ContactController::class)->names('contact')->only(['edit', 'update']);
});
