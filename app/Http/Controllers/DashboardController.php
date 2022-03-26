<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sprint;
use App\Models\Comision;
 
class DashboardController extends Controller
{

	/**
     * Retorna los sprints para la comisión.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $user = auth()->user();

        if($user->isProfesor()){
		    $comisiones = Comision::all();
        }
        else{
            $comision_id = auth()->user()->comision_id;
            $comisiones = Comision::where('id', $comision_id)->get();
        }

        return view('dashboard', ['comisiones' => $comisiones]);
    }
}
