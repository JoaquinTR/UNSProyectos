<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GanttController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Rutas de token de sincronización, manejan el sincronismo entre instancias de planeación */
Route::post('/api/token/keepalive', [TokenController::class, 'keepalive']);
Route::post('/api/token/pedir', [TokenController::class, 'pedirToken']);
Route::post('/api/token/soltar', [TokenController::class, 'soltarToken']);
Route::post('/api/token/aceptar', [TokenController::class, 'aceptarVotacion']);
Route::post('/api/token/rechazar', [TokenController::class, 'rechazarVotacion']);

/* DEBUG, ELIMINAR */
Route::post('/token/clear', [TokenController::class, 'clearCache']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
