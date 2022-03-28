<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Settings;

class SettingsController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener el usuario actual
        $user_id = auth()->user()->id;
        
        /* Custom css */
		$css_skin = Settings::select('skin')->where('users_id', $user_id)->first();
		if(!isset($css_skin)){
			$css_skin = "broadway";
		}else{
			$css_skin = $css_skin->skin;
		};

        return view('dashboard.profile.settings',['css_skin' => $css_skin]);
    }

    /**
     * Guarda una selección de skin para el tablero gantt.
     */
    public function storeSkin(Request $request){
        // Obtener el usuario actual
        $user_id = auth()->user()->id;
        $settings = Settings::where('users_id', $user_id)->first();

        $request->validate(
            [
            'skin' => 'required'
            ],
            [
                'email.required' => 'Por favor seleccione un tema para el tablero gantt.'
        ]);

        $input = $request->all();

        // Lleno el modelo de usuario
        $settings->fill([
            'skin' => $input['skin'],
            'updated_at' => now()
        ]);

        $settings->save();

        return back()->with('success', 'Datos modificados correctamente.');
    }
}
