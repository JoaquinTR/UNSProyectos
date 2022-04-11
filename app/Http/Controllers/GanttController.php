<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; 
use App\Models\Task;
use App\Models\Link;
use App\Models\Sprint;
use App\Models\Settings;
use App\Models\User;
 
class GanttController extends Controller
{

	/**
     * Retorna la vista de gantt conteniendo el sprint en curso.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
		$user = auth()->user();

		if($user->isProfesor()){
			return redirect('/dashboard');
		}

		/* Token management */
		$token_owner = Cache::get("token_owner-".$user->comision_id, null);
		if($token_owner == null || $token_owner == 0){ # Token disponible
			$lock = Cache::lock("token_owner-".$user->comision_id.'_lock', 2); //Trato de obtener el lock

			try {
				if ($lock->get()) {
					Cache::put("token_owner-".$user->comision_id, $user->id);
					$token_owner = $user->id;
				}
			} finally {
				$lock->release();
			}
		}

		/* Data de logueados */
		$compañeros = User::where('comision_id', $user->comision_id)->where('id', '!=' , $user->id)->get();
		$data_compañeros = Array();
		$max_time = now()->subSeconds(15)->format('Y-m-d H:i:s');
		foreach ($compañeros as $key => $compa) {
			$last_seen = Cache::get('alumno'.$user->comision_id."-".$compa->id, "gone");
			if($last_seen != "gone"){
				$time_alive = explode("-", $last_seen, 2)[1];
				if($max_time > $time_alive){ //si ahora menos 15 segundos es mayor a la ultima vez que lo vimos, se fué
					Cache::put('alumno'.$user->comision_id."-".$compa->id, "gone");
				}
			}
			$data = Array(
				"id" => $compa->id,
				"alias" => $compa->alias,
				"nombre" => $compa->name,
				"email" => $compa->email,
				"last_seen" => $last_seen
			);
			array_push($data_compañeros, $data);
		}
		$compañeros = $data_compañeros;

		Cache::put("datos-dirty-".$user->comision_id."-".$user->id,0); //Estoy por entrar, no importa el dirty

		$date_diff = -1;
		$text_color = "";
		$comision = $user->comision_id;
		$sprint = Sprint::where('comision_id', $comision)->where('iniciado', 1)->where('entregado',0)->first();

		if($sprint){
			$date_diff = round( (strtotime($sprint['deadline']) - time()) / (60 * 60 * 24));
			$text_color = "text-primary";
			if($date_diff < 0) $text_color = "text-success";
			else if($date_diff > 0 && $date_diff <= 5) $text_color = "text-danger";
			else if($date_diff > 5 && $date_diff < 12) $text_color = "text-orange";  
		}

		/* Datos de votación */
		$votacion = Cache::get("votacion-".$user->comision_id,0);
		
		/* Custom css */
		$css_skin = Settings::select('skin')->where('users_id', $user->id)->first()->skin;

        return view('gantt', ['sprint' => $sprint, 'comision' => $comision, 'date_diff'=> $date_diff, 
			'text_color' => $text_color, 'css_skin' => $css_skin, 'token_owner' => $token_owner,
			'compañeros' => $compañeros, 'votacion' => $votacion]);
    }

	/**
     * Retorna la vista de gantt conteniendo el sprint recibido por URL.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function ganttView(Request $request, $sprint_id, $comision_id = null){
		$date_diff = -1;
		$text_color = "";
		$user = null;
		$comision = null;
		$token_owner = null;
		$compañeros = null;
		$votacion = null;

		$user = auth()->user();
		if(!isset($comision_id) && !$user->isProfesor()){
			/* Token management */
			$token_owner = Cache::get("token_owner-".$user->comision_id, null);
			if($token_owner == null || $token_owner == 0){ # Token disponible
				$lock = Cache::lock("token_owner-".$user->comision_id.'_lock', 2);
 
				try {
					if ($lock->get()) {
						Cache::put("token_owner-".$user->comision_id, $user->id);
						$token_owner = $user->id;
					}
				} finally {
					$lock->release();
				}
			}

			/* Data de logueados */
			$compañeros = User::where('comision_id', $user->comision_id)->where('id', '!=' , $user->id)->get();
        	$data_compañeros = Array();
			$max_time = now()->subSeconds(15)->format('Y-m-d H:i:s');
        	foreach ($compañeros as $key => $compa) {
        	    $last_seen = Cache::get('alumno'.$user->comision_id."-".$compa->id, "gone");
				if($last_seen != "gone"){
					$time_alive = explode("-", $last_seen, 2)[1];
					if($max_time > $time_alive){ //si ahora menos 15 segundos es mayor a la ultima vez que lo vimos, se fué
						Cache::put('alumno'.$user->comision_id."-".$compa->id, "gone");
						$last_seen = "gone";
					}
				}
        	    $data = Array(
					"id" => $compa->id,
        	        "alias" => $compa->alias,
        	        "nombre" => $compa->name,
					"email" => $compa->email,
        	        "last_seen" => $last_seen
        	    );
        	    array_push($data_compañeros, $data);
        	}
			$compañeros = $data_compañeros;

			Cache::put("datos-dirty-".$user->comision_id."-".$user->id,0); //Estoy por entrar, no importa el dirty

			/* Datos de votación */
			$votacion = Cache::get("votacion-".$user->comision_id,0);
		}

		if($user->isProfesor()){ /* El profesor debe indicar sprint_id Y ADEMÁS comision_id */
			if(!isset($comision_id)){
				return redirect("dashboard");
			}
			$comision = $comision_id;
			$sprint = Sprint::where('comision_id', $comision)->find($sprint_id);
		}else{ /* solo la comision del alumno */
			$comision = $user->comision_id;
			$sprint = Sprint::where('comision_id', $comision)->find($sprint_id);
		}

		if($sprint->entregado == 1 || $sprint->iniciado == 0){
			$date_diff= -1;
			$text_color = "text-success";
			$token_owner = null; #Utilizo esto para flaggear que desactive el sistema de token por completo
		}else{
			$date_diff = round( (strtotime($sprint['deadline']) - time()) / (60 * 60 * 24));
			$text_color = "text-primary";
			if($date_diff < 0) $text_color = "text-success";
			else if($date_diff > 0 && $date_diff <= 5) $text_color = "text-danger";
			else if($date_diff > 5 && $date_diff < 12) $text_color = "text-orange";
		}

		/* Custom css */
		$css_skin = Settings::select('skin')->where('users_id', $user->id)->first();
		if(!isset($css_skin)){
			$css_skin = "broadway";
		}else{
			$css_skin = $css_skin->skin;
		}
		
        return view('gantt', ['sprint' => $sprint, 'comision' => $comision, 'date_diff'=> $date_diff, 
			'text_color' => $text_color, 'css_skin' => $css_skin, 'token_owner' => $token_owner,
			'compañeros' => $compañeros, 'votacion' => $votacion]);
    }

	//*************************************************************
	//************************* GANTT API *************************
	//*************************************************************

	//Obtiene los datos de un sprint, para un usuario en su comisión asignada
  	public function get(Request $request){
		$user = auth()->user();
		$sprint_id = $request->header('X-Header-Sprint-Id');
		if($user->isProfesor()){
			$comision_id = $request->header('X-Header-Comision-Id');
		}else{
			$comision_id = $user->comision_id;
		}
		$tasks = new Task();
		$links = new Link();
		
		#Limpio la entrada de datos dirty
		if(!$user->isProfesor()){
			try {
				Cache::put("datos-dirty-".$user->comision_id."-".$user->id,0);
			}catch(Exception $e){/* No interesa el lock, va a refrescar los datos en el frontend */}
		}

    	return response()->json([
			"data" => $tasks->orderBy('sortorder')->where('sprint_id', $sprint_id)->where('comision_id', $comision_id)->get(),
			"links" => $links->where('sprint_id', $sprint_id)->where('comision_id', $comision_id)->get()
        ]);
  	}
}
