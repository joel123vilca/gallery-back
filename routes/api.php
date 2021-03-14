<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DocumentsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::post('documents', [DocumentsController::class, 'store']);
Route::delete('documents/{id}', [DocumentsController::class, 'destroy']);
Route::get('documents', [DocumentsController::class, 'getDocuments']);
Route::post('documents/{id}', [DocumentsController::class, 'updateDocument']);

