<?php

use App\Http\Controllers\UploadController;
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
    return view('welcome');
});

Route::get('form', [UploadController::class, 'form'])->name('form');
Route::post('upload', [UploadController::class, 'upload'])->name('upload');
Route::get('upload/{encryptedFilename}/delete', [UploadController::class, 'deleteUpload'])->name('deleteUpload');
