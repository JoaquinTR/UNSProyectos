<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GanttController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordModifyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { /* Default, redirecciona */
    return redirect('/dashboard');
});

/* Vistas de uso */
/* Tablero gantt de administracion de sprint, salta al gantt en curso */
Route::get('/gantt', [GanttController::class, 'index'])->middleware(['auth'])->middleware('is_alumno')->name('gantt');

/* Tablero gantt de administracion de un sprint, en curso o no (ultimo caso no editable) */
Route::get('/gantt/{sprint_id}/{comision_id?}', [GanttController::class, 'ganttView'])->middleware(['auth'])->name('gantt.view');

    /* Visor de proyecto y sus sprints, para el profesor indica los proyectos de los alumnos por comisión */
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

    /* Perfil personal, administracion de cuenta */
    Route::get('/dashboard/profile', [ProfileController::class, 'index'])->middleware(['auth'])->name('dashboard.profile');

    Route::get('/dashboard/profile/contraseña', [PasswordModifyController::class, 'index'])->name('modify_passw');
    Route::post('/dashboard/profile/contraseña', [PasswordModifyController::class, 'store'])->name('modify_passw');

    Route::get('/dashboard/settings', function () { /* Settings de visor gantt, permite personalizar la interfaz aún más */
        return view('dashboard.settings');
    })->middleware(['auth'])->name('dashboard.settings');

/* Vista de administración de materia, profesor */
Route::get('/admin', function () { /* Visor de proyecto y sus sprints, para el profesor indica los proyectos de los alumnos segmentados por comision (eso el dashboard) */
    return view('admin');
})->middleware('is_profesor')->name('admin');

/* Auth */

require __DIR__.'/auth.php';

Auth::routes();
