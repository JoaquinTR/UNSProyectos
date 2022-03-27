<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PasswordModifyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el formulario de cambio de contraseña.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard.profile.contraseña');
    }

    /**
     * Toma los datos que ingresó el usuario y los actualiza en el modelo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Obtener el usuario actual
        $user = auth()->user();

        $request->validate(
            [
                'password' => 'required|min:8',
                'passwordr' => 'required|same:password|min:8',
            ],
            [
                'password.required' => 'Se necesita una contraseña.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'passwordr.required' => 'Por favor repita la contraseña.',
                'passwordr.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'passwordr.same' => 'Las contraseñas deben coincidir.',
            ]);

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        // Lleno el modelo de usuario
        $user->fill([
            'password' => $input['password'],
            'updated_at' => now()
        ]);

        // Grabo en la base de datos
        $user->save();

        return back()->with('success', 'Contraseña modificada correctamente.');
    }
}
