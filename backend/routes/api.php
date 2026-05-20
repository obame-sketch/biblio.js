<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivreController;
use App\Http\Controllers\AuteurController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\EmpruntController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RessourceNumeriqueController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('livres', LivreController::class);
    Route::apiResource('auteurs', AuteurController::class);
    Route::apiResource('categories', CategorieController::class);
    Route::apiResource('emprunts', EmpruntController::class);
    Route::apiResource('stock', StockController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('ressources-numeriques', RessourceNumeriqueController::class);
    Route::get('stock/livre/{livreId}', [StockController::class, 'getByLivreId']);
    Route::post('emprunts/{id}/retourner', [EmpruntController::class, 'retourner']);
});
