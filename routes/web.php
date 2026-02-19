<?php

// Routes for Angular SPA
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeEntriesController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\httpRulesMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->file(public_path('index.html'), [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
});

// Explicit SPA Routes to prevent Laravel interference
$spaRoutes = ['/home', '/fichajes', '/user', '/myEntries', '/profile', '/login'];
foreach ($spaRoutes as $route) {
    Route::get($route, function () {
        return response()->file(public_path('index.html'), [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    });
}

// Ruta catch-all para el SPA del frontend Angular
Route::get('/{any}', function () {
    $path = request()->path();
    if (preg_match('/\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|map)$/', $path)) {
        return response()->json(['error' => 'Not Found'], 404);
    }
    return response()->file(public_path('index.html'), [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
})->where('any', '.*');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';

// Route::apiResource('/user', UserController::class)->middleware(httpRulesMiddleware::class);
// Route::apiResource('/timeEntrie', TimeEntriesController::class)->middleware(httpRulesMiddleware::class);





