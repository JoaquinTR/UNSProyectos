<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; 
use App\Models\User;


/**
 * TODO:
 * LOCKS:
 * $lock = Cache::lock('foo', 10);
 *
 *   if ($lock->get()) {
 *       // Lock acquired for 10 seconds...
 *   
 *       $lock->release();
 *   }
 */

class TokenController extends Controller
{

    /**
     * Keepalive indica que el alumno sigue conectado, y devuelve la estructura caché con el status de la comisión.
     */
    public function keepalive(Request $request){
        
        $user = auth()->user();
        if($user->isProfesor()){
            return response()->json([
                "cod" => 1,
                "action"=> "Un profesor no posee keepalive."
            ],403);
        }
        $status_comision = (object)[]; #Status de la comisión y sincronización
        $cant_online = 1;
        $status_comision->time_control = now()->format('Y-m-d H:i:s');
        
        /* Marco mi llegada al servidor, o la refresco */
        Cache::put("alumno".$user->comision_id."-".$user->id, "alive-".now()); #Si hace 15 seg que no tiene keepalive pasa a "gone-".now()
        
        /* Token owner */
        $token_owner = Cache::get("token_owner-".$user->comision_id, 0);
        $status_comision->token_owner = $token_owner;

        /* Estado del gannt, 1=> Debe actualizar gantt, 0 => al día */
        $status_comision->datos_dirty = Cache::get("datos-dirty-".$user->comision_id."-".$user->id,0);

        /* Datos de compañeros online */
        $compañeros = User::where('comision_id', $user->comision_id)->where('id', '!=' , $user->id)->get();
        $data_compañeros = Array();
        foreach ($compañeros as $key => $compa) {
            $last_seen = Cache::get('alumno'.$user->comision_id."-".$compa->id, "gone");
            if($last_seen != "gone"){
                $max_time = now()->subSeconds(15)->format('Y-m-d H:i:s');
                $time_alive = explode("-", $last_seen, 2)[1];
                if($max_time > $time_alive){ //si ahora menos 15 segundos es mayor a la ultima vez que lo vimos, se fué
                    Cache::put('alumno'.$user->comision_id."-".$compa->id, "gone");
                    $last_seen = "gone";
                }else{
                    $cant_online += 1;
                }
            }
            if($last_seen == "gone" && $token_owner == $compa->id){ //Si se fué el token owner debo soltar el token
                $lock = Cache::lock("token_owner-".$user->comision_id.'_lock', 2); //Trato de obtener el lock

                try {
                    if ($lock->get()) {
                        Cache::put("token_owner-".$user->comision_id, 0);
                    }else{ //alguien vió que se fue antes, delego
                        $lock->release();
                        continue;
                    }
                } finally {
                    $lock->release();
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
        $status_comision->data_compañeros = $data_compañeros;
        $status_comision->cant_online = $cant_online;

        /* Datos de votos, TODO lock basandose solo en votacion-$user->comision_id */
        $votacion_en_curso = Cache::get("votacion-".$user->comision_id,0);
        $votacion_pos = null;
        $votacion_neg = null;
        $votacion_target = null;
        if($votacion_en_curso){
            $votacion_timeout = Cache::get("votacion-timeout".$user->comision_id,0);
            $votacion_target = Cache::get("votacion-target".$user->comision_id, null);
            $votacion_pos = Cache::get("votacion-positivo".$user->comision_id,0);
            $votacion_neg = Cache::get("votacion-negativo".$user->comision_id,0);

            /* Decisión de la votación */
            if( ($cant_online == $votacion_pos) || (($votacion_pos-$votacion_neg) >= ceil($cant_online/2))){ 
                # Si salio positiva actualizo token owner
                Cache::put("token_owner-".$user->comision_id, $votacion_target);
                $status_comision->token_owner = $votacion_target;
                # Limpio rastros de una votación
                Cache::put("votacion-".$user->comision_id,0);
                Cache::put("votacion-timeout".$user->comision_id,0);
                Cache::put("votacion-target".$user->comision_id,0);
                Cache::put("votacion-positivo".$user->comision_id,0);
                Cache::put("votacion-negativo".$user->comision_id,0);
                Cache::put("votacion-last-result".$user->comision_id,1);
                $status_comision->resultado_votacion = 1;
                $votacion_en_curso = 0; #Cierro la votacion
            }else if((isset($votacion_timeout) && $votacion_timeout != 0 && $votacion_timeout < now()) || ($votacion_neg == $cant_online) || (($votacion_neg-$votacion_pos) >= ceil($cant_online/2))){
                # Limpio rastros de una votación
                Cache::put("votacion-".$user->comision_id,0);
                Cache::put("votacion-timeout".$user->comision_id,0);
                Cache::put("votacion-target".$user->comision_id,0);
                Cache::put("votacion-positivo".$user->comision_id,0);
                Cache::put("votacion-negativo".$user->comision_id,0);
                Cache::put("votacion-last-result".$user->comision_id,0);
                $status_comision->resultado_votacion = 0;
                $votacion_en_curso = 0; #Cierro la votacion
            }else{
                /* Debo devolver el estado de la votación, sigue en curso */
                $status_comision->votacion_timeout = $votacion_timeout;
                $status_comision->votacion_pos = $votacion_pos;
                $status_comision->votacion_neg = $votacion_neg;
                $status_comision->votacion_target = $votacion_target;
            }
        }else{
            $status_comision->resultado_votacion = Cache::get("votacion-last-result".$user->comision_id,0);
        }
        $status_comision->votacion_en_curso = $votacion_en_curso;

        /* ¿FIX? trato de que no me patee el servidor, se va defasando la hora por alguna razon*/
        Cache::put("alumno".$user->comision_id."-".$user->id, "alive-".now());
 
        # Fin del proceso de keepalive, retorno el status de la comisión
        return $this->response($status_comision,200);
    }

    //TODO: Modelar los locks con lock->block(2); (esperar a obtener el lock un máximo de 2 segundos) para los casos de colisión de locks.
    /**
     * Comienza una votación para que el alumno que la inicia tome poder del token.
     */
    public function pedirToken(Request $request){
        $user = auth()->user();

        /* Ante un lock deny espero 0.1 segundos 3 veces */
        $token_owner = Cache::get("token_owner-".$user->comision_id, null);
        $votacion_en_curso = Cache::get("votacion-".$user->comision_id, 0);
        if($token_owner == $user->id){
            return response()->json([
                "cod" => 0,
                "action"=> "Ya tenés el token"
            ]);
        }else if(!isset($token_owner) || ($token_owner == 0 && $votacion_en_curso != 1)){ 
            Cache::put("token_owner-".$user->comision_id, $user->id);
            $votacion_en_curso = 0;
            //$lock->release();
            return response()->json([
                "cod" => 1,
                "action"=> "Token obtenido"
            ]);
        }

        /* Disparo una votación a mi favor */
        if(isset($votacion_en_curso) && $votacion_en_curso != 1){
            Cache::put("votacion-".$user->comision_id, 1);
            $timeout = now()->addSeconds(15)->format('Y-m-d H:i:s');
            Cache::put("votacion-timeout".$user->comision_id, $timeout);
            Cache::put("votacion-target".$user->comision_id, $user->id);
            Cache::put("votacion-positivo".$user->comision_id, 0);
            Cache::put("votacion-negativo".$user->comision_id, 0);
        }else{
            return response()->json([
                "cod" => 3,
                "action"=> "Ya hay una votación en curso."
            ]);
        }

        return response()->json([
            "cod" => 2,
            "action"=> "votacion iniciada"
        ]);
    }
 
    /**
     * Suelta el token que posee el alumno para que otro lo tome.
     */
    public function soltarToken(Request $request){
        $user = auth()->user();

        $token_owner = Cache::get("token_owner-".$user->comision_id, null);
        if(!isset($token_owner) || $token_owner != $user->id){
            return response()->json([
                "cod" => 0,
                "action"=> "No podés soltar el token si no lo tenes"
            ]);
        }
        else if($token_owner == $user->id){
            /* No debería poder soltar el token durante una votación, esto es preventivo */
            $votacion_en_curso = Cache::put("votacion-".$user->comision_id, 0);
            $votacion_timeout = Cache::put("votacion-timeout".$user->comision_id, 0);
            $votacion_target = Cache::put("votacion-target".$user->comision_id, 0);
            $votacion_pos = Cache::put("votacion-positivo".$user->comision_id, 0);
            $votacion_neg = Cache::put("votacion-negativo".$user->comision_id, 0);
            /* Suelto el token */
            Cache::put("token_owner-".$user->comision_id, 0);
        }

        return response()->json([
            "cod" => 1,
            "action"=> "token devuelto"
        ]);
    }

    /**
     * Emite un voto positivo ante una votación en curso.
     */
    public function aceptarVotacion(Request $request){
        $user = auth()->user();
        /* Insertar una llave en cache para recordar que el user votó */
        /* Ante un lock deny espero 0.1 segundos 3 veces */
        $votacion_en_curso = Cache::get("votacion-".$user->comision_id, 0);
        if($votacion_en_curso == 1){
            $votacion_pos = Cache::get("votacion-positivo".$user->comision_id, 0);
            $lock = Cache::lock("votacion-".$user->comision_id.'_lock', 2); //Trato de obtener el lock

            try {
                if ($lock->get()) {
                    Cache::put("votacion-positivo".$user->comision_id, $votacion_pos + 1);
                }else{ //alguien vió que se fue antes, delego
                    return response()->json([
                        "cod" => 1,
                        "action"=> "No se pudo registrar la votación, reintente por favor."
                    ]);
                }
            } finally {
                $lock->release();
            }
        }else{
            return response()->json([
                "cod" => 1,
                "action"=> "No hay votación en curso"
            ]);
        }
        return response()->json([
            "cod" => 0,
            "action"=> "voto positivo emitido"
        ]);
    }

    /**
     * Emite un voto negativo ante una votación en curso.
     */
    public function rechazarVotacion(Request $request){
        $user = auth()->user();
        /* Insertar una llave en cache para recordar que el user votó */
        /* Ante un lock deny espero 0.1 segundos 3 veces */
        $votacion_en_curso = Cache::get("votacion-".$user->comision_id, 0);
        if($votacion_en_curso == 1){
            $votacion_neg = Cache::get("votacion-negativo".$user->comision_id, 0);
            $lock = Cache::lock("votacion-".$user->comision_id.'_lock', 2); //Trato de obtener el lock

            try {
                if ($lock->get()) {
                    Cache::put("votacion-negativo".$user->comision_id, $votacion_neg + 1);
                }else{ //alguien vió que se fue antes, delego
                    return response()->json([
                        "cod" => 1,
                        "action"=> "No se pudo registrar la votación, reintente por favor."
                    ]);
                }
            } finally {
                $lock->release();
            }
        }else{
            return response()->json([
                "cod" => 1,
                "action"=> "No hay votación en curso"
            ]);
        }
        return response()->json([
            "cod" => 0,
            "action"=> "voto negativo emitido"
        ]);
    }

    /**
     * Wrapper de respuesta.
     */
    private function response($data="", $cod = 200){
        return response()->json($data, $cod, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE );
    }
    
    /**
     * DEBUG, limpia la cache.
     */
    public function clearCache(Request $request){
        /* Ante un lock deny espero 0.1 segundos 3 veces */
        Cache::flush();
        return response()->json([
            "action"=> "cache reventada"
        ]);
    }
}