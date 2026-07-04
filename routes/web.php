<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KambingController;
use App\Http\Controllers\ProduktivitasController;
use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});

// Logout (Any Role, Authenticated Only)
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Authenticated Users)
Route::middleware('auth')->group(function () {
    
    // Redirect to Dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Kambing CRUD & Features
    Route::post('kambing/import', [KambingController::class, 'import'])->name('kambing.import');
    Route::post('kambing/destroy-bulk', [KambingController::class, 'destroyBulk'])->name('kambing.destroy-bulk');
    Route::resource('kambing', KambingController::class)->except(['show', 'create', 'edit']);

    // Data Produktivitas CRUD & Features
    Route::post('produktivitas/destroy-bulk', [ProduktivitasController::class, 'destroyBulk'])->name('produktivitas.destroy-bulk');
    Route::resource('produktivitas', ProduktivitasController::class)->except(['show', 'create', 'edit']);

    // Profile Settings (Available for Admin & User)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // K-Means Hasil (Read-only for all roles)
    Route::get('clustering/hasil', [ClusteringController::class, 'hasil'])->name('clustering.hasil');
    Route::get('clustering/export-excel', [ClusteringController::class, 'exportExcel'])->name('clustering.export-excel');
    Route::get('clustering/export-pdf', [ClusteringController::class, 'exportPdf'])->name('clustering.export-pdf');

    // Admin-Only Routes
    Route::middleware('role:admin')->group(function () {
        
        // K-Means Processing & Final Dashboard
        Route::get('clustering/proses', [ClusteringController::class, 'prosesForm'])->name('clustering.proses-form');
        Route::post('clustering/proses', [ClusteringController::class, 'proses'])->name('clustering.proses');

        // User Account Management
        Route::resource('user', UserController::class)->except(['show', 'create', 'edit']);
    });
});
