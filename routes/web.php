<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(FileController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/my-files', 'myFiles')->name('myFiles');
        Route::get('/trash', 'trash')->name('trash');
        Route::post('/folders', 'createFolder')->name('folder.create');
        Route::delete('/folders', 'destroy')->name('folder.destroy');
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
