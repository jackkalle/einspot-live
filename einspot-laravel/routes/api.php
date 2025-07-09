<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Tag suggestions for typeaheads
Route::get('/tags/suggestions', [TagController::class, 'suggestions'])->name('api.tags.suggestions');

// Placeholder for other potential API routes if needed later
// Example:
// Route::post('/cart/add/{product}', [CartController::class, 'add'])->middleware('auth:sanctum');
