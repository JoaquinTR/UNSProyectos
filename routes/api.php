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

/* Manejo de datos del diagrama Gantt */
Route::get('/data', [GanttController::class, 'get']);
Route::resource('task', TaskController::class);
Route::resource('link', LinkController::class);

/* Rutas de token de sincronización, manejan el sincronismo entre instancias de planeación */
Route::post('/token/keepalive', [TokenController::class, 'keepalive']);
Route::post('/token/pedir', [TokenController::class, 'pedirToken']);
Route::post('/token/soltar', [TokenController::class, 'soltarToken']);
Route::post('/token/aceptar', [TokenController::class, 'aceptarVotacion']);
Route::post('/token/rechazar', [TokenController::class, 'rechazarVotacion']);

/* DEBUG, ELIMINAR */
Route::post('/token/clear', [TokenController::class, 'clearCache']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
