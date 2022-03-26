<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\Link;
use App\Models\Sprint;
 
class GanttController extends Controller
{

	/**
     * Retorna la vista de gantt conteniendo el sprint en curso.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
		$date_diff = -1;
		$text_color = "";
		$comision = auth()->user()->comision_id;
		$sprint = Sprint::where('comision_id', $comision)->where('iniciado', 1)->where('entregado',0)->get()[0];

		if($sprint){
			$date_diff = round( (strtotime($sprint['deadline']) - time()) / (60 * 60 * 24));
			$text_color = "text-primary";
			if($date_diff < 0) $text_color = "text-success";
			else if($date_diff > 0 && $date_diff <= 5) $text_color = "text-danger";
			else if($date_diff > 5 && $date_diff < 12) $text_color = "text-orange";  
		}
		

        return view('gantt', ['sprint' => $sprint, 'comision' => $comision, 'date_diff'=> $date_diff, 'text_color' => $text_color]);
    }

	/**
     * Retorna la vista de gantt conteniendo el sprint recibido por URL.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function ganttView($sprint_id, Request $request){
		$date_diff = -1;
		$text_color = "";
		$comision = auth()->user()->comision_id;
        $sprint = Sprint::where('comision_id', $comision)->where('id', $sprint_id)->get()[0];

		if($sprint->entregado==1){
			$date_diff= -1;
			$text_color = "text-success";
		}else{
			$date_diff = round( (strtotime($sprint['deadline']) - time()) / (60 * 60 * 24));
			$text_color = "text-primary";
			if($date_diff < 0) $text_color = "text-success";
			else if($date_diff > 0 && $date_diff <= 5) $text_color = "text-danger";
			else if($date_diff > 5 && $date_diff < 12) $text_color = "text-orange";
		}
		
        return view('gantt', ['sprint' => $sprint, 'comision' => $comision, 'date_diff'=> $date_diff, 'text_color' => $text_color]);
    }

	//*************************************************************
	//************************* GANTT API *************************
	//*************************************************************

	//Obtiene los datos de un sprint, para un usuario en su comisión asignada
  	public function get(Request $request){
		$sprint_id = $request->header('X-Header-Sprint-Id');
		$comision_id = $request->header('X-Header-Comision-Id');
		$tasks = new Task();
		$links = new Link();
	
    	return response()->json([
			"data" => $tasks->orderBy('sortorder')->where('sprint_id', $sprint_id)->where('comision_id', $comision_id)->get(),
			"links" => $links->where('sprint_id', $sprint_id)->where('comision_id', $comision_id)->get()
        ]);
  	}
}
