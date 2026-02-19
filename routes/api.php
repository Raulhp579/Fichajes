<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeEntriesController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\isAdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/userInfo', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// autenticado y ser admin
Route::middleware(['auth:sanctum', isAdminMiddleware::class])->group(function () {

    Route::apiResource('/user', UserController::class);

    Route::post('/timeEntrie', [TimeEntriesController::class, 'store']);
    Route::put('/timeEntrie/{id}', [TimeEntriesController::class, 'update']);
    Route::delete('/timeEntrie/{id}', [TimeEntriesController::class, 'destroy']);
    Route::get('/timeEntrie/{id}', [TimeEntriesController::class, 'show']);

});

// solo autenticado
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/timeEntrie', [TimeEntriesController::class, 'index']);
    Route::post('/clock_in_out', [TimeEntriesController::class, 'clock_In_Out']);
    Route::get('/takeThree', [TimeEntriesController::class, 'getLastThreeEntries']);
    Route::get("/getAllOfOneUser",[TimeEntriesController::class, "getAllOfOneUser"]);
    // New routes for Profile
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/time-entries/statistics', [TimeEntriesController::class, 'getStatistics']);
});

// /////////SIN TOKEN////////////
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get("/getPdf/{id}",[PdfController::class,"downloadPdf"]);
