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
        $status_comision = (object)[]; #Status de la comisión y sincronización
        
        /* Marco mi llegada al servidor, o la refresco */
        Cache::put("alumno".$user->comision_id."-".$user->id, "alive-".now()); #Si hace 15 seg que no tiene keepalive pasa a "gone-".now()
        
        /* Token owner */
        $token_owner = Cache::get("token_owner-".$user->comision_id, null);
        /* if(!isset($token_owner)){ //no debería manotear el token
            # TODO: LOCK token_owner, setear 0 ante un lock deny (alguien ganó de mano)
            $token_owner = $user->id;
            Cache::put("token_owner-".$user->comision_id, $token_owner);
            # TODO: RELEASE LOCK
        } */
        $status_comision->token_owner = $token_owner;

        /* Estado del gannt, 1=> Debe actualizar gantt, 0 => al día */
        $status_comision->datos_dirty = Cache::get("datos-dirty-".$user->comision_id."-".$user->id,0);

        /* Datos de compañeros online */
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
            if($last_seen == "gone" && $token_owner == $compa->id){ //Si se fué el token owner debo soltar el token
                //LOCK
                Cache::put("token_owner-".$user->comision_id, 0);
                //LOCK RELEASE
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

        /* Datos de votos */
        $votacion_en_curso = Cache::get("votacion-".$user->comision_id,0);
        $votacion_pos = null;
        $votacion_neg = null;
        $votacion_target = null;
        if($votacion_en_curso){
            $votacion_timeout = Cache::get("votacion-timeout".$user->comision_id,0);
            $votacion_target = Cache::get("votacion-target".$user->comision_id, $user->id);
            $votacion_pos = Cache::get("votacion-positivo".$user->comision_id,0);
            $votacion_neg = Cache::get("votacion-negativo".$user->comision_id,0);

            /* Decisión de la votación */
            if(isset($votacion_timeout) && $votacion_timeout != 0 && $votacion_timeout < now()){ # Debo decidir la votacion
                /* TODO: LOCK token_owner, ante falla de obtención del lock, seteo $votacion en curso falso,
                se esta decidiendo la votación en otro proceso, $token_owner a 0, no soy yo (tentativamente) */
                if($votacion_pos > $votacion_neg){ # Si salio positiva actualizo token owner
                    # Debo actualizar el owner
                    Cache::put("token_owner-".$user->comision_id, $votacion_target);
                    $status_comision->token_owner = $votacion_target;
                }
                # Limpio rastros de una votación
                Cache::put("votacion-".$user->comision_id,0);
                Cache::put("votacion-timeout".$user->comision_id,0);
                Cache::put("votacion-target".$user->comision_id, $user->id);
                Cache::put("votacion-positivo".$user->comision_id,0);
                Cache::put("votacion-negativo".$user->comision_id,0);
                $votacion_en_curso = 0; #Cierro la votacion
                /* TODO: RELEASE LOCK */
            }else{ /* Debo devolver el estado de la votación, sigue en curso */
                $status_comision->votacion_timeout = $votacion_timeout;
                $status_comision->votacion_pos = $votacion_pos;
                $status_comision->votacion_neg = $votacion_neg;
                $status_comision->votacion_target = $votacion_target;
            }
        }
        $status_comision->votacion_en_curso = $votacion_en_curso;
 
        # Fin del proceso de keepalive, retorno el status de la comisión
        return $this->response($status_comision,200);
    }

    /**
     * Comienza una votación para que el alumno que la inicia tome poder del token.
     */
    public function pedirToken(Request $request){
        $user = auth()->user();

        /* Ante un lock deny espero 0.1 segundos 3 veces */
        $token_owner = Cache::get("token_owner-".$user->comision_id, null);
        if($token_owner == $user->id){
            return response()->json([
                "cod" => 0,
                "action"=> "Ya tenés el token"
            ]);
        }
        else if(!isset($token_owner) || $token_owner == 0){ //no debería manotear el token
            # TODO: LOCK token_owner, setear 0 ante un lock deny (alguien ganó de mano)
            Cache::put("token_owner-".$user->comision_id, $user->id);
            # TODO: RELEASE LOCK
            return response()->json([
                "cod" => 1,
                "action"=> "Token obtenido"
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
        if($token_owner != $user->id || !isset($token_owner)){
            return response()->json([
                "cod" => 0,
                "action"=> "No podés soltar el token si no lo tenes"
            ]);
        }
        else if($token_owner == $user->id){ //no debería manotear el token
            # TODO: LOCK token_owner, setear 0 ante un lock deny (alguien ganó de mano)
            $token_owner = $user->id;
            Cache::put("token_owner-".$user->comision_id, 0);
            # TODO: RELEASE LOCK
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
        /* Insertar una llave en cache para recordar que el user votó */
        /* Ante un lock deny espero 0.1 segundos 3 veces */
        return response()->json([
            "action"=> "voto positivo emitido"
        ]);
    }

    /**
     * Emite un voto negativo ante una votación en curso.
     */
    public function rechazarVotacion(Request $request){
        /* Insertar una llave en cache para recordar que el user votó */
        /* Ante un lock deny espero 0.1 segundos 3 veces */
        return response()->json([
            "action"=> "voto negativo emitido"
        ]);
    }

    /**
     * Lockea la clave $key por 2 segundos (la duración entre keepalive)
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