<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; 
use App\Models\Sprint;
use App\Models\Link;
use App\Models\User;
 
class LinkController extends Controller
{
    public function store(Request $request){
        $user = auth()->user();

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId, $request->comisionId, $user)) return $res;

        $link = new Link();
        
        $link->type = $request->type;
        $link->source = $request->source;
        $link->target = $request->target;
        $link->sprint_id = $request->sprintId;
        $link->comision_id = $request->comisionId;
        
        $link->save();
 
        $this->flagDirty($user);
        return response()->json([
            "action"=> "inserted",
            "tid" => $link->id
        ]);
    }
 
    public function update($id, Request $request){
        $user = auth()->user();

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId, $request->comisionId, $user)) return $res;

        $link = Link::find($id);
 
        $link->type = $request->type;
        $link->source = $request->source;
        $link->target = $request->target;
 
        $link->save();
 
        $this->flagDirty($user);
        return response()->json([
            "action"=> "updated"
        ]);
    }
 
    public function destroy($id, Request $request){
        $user = auth()->user();

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId, $request->comisionId, $user)) return $res;

        $link = Link::find($id);
        $link->delete();
 
        $this->flagDirty($user);
        return response()->json([
            "action"=> "deleted"
        ]);
    }

    /* Controla que el sprint esté en estado editable */
    private function testEditable($sprint_id, $comisionId, $user){
        // Control de editabilidad
        $token_owner = Cache::get("token_owner-".$user->comision_id, null);
        $votacion_en_curso = Cache::get("votacion-".$user->comision_id, 0);
        if($votacion_en_curso == 1){
            return response()->json([
                "action" => "error",
                "msg" => "Hay una votación en curso."
            ]);
        }
        if($sprint_id && Sprint::findOrFail($sprint_id)->entregado){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó guardar en un Sprint ya entregado."
            ]);
        }else if(isset($user) && $user->comision_id != $comisionId){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó guardar en un Sprint que no pertenece a su comisión."
            ]);
        }else if(isset($user) && $token_owner != null && $token_owner != $user->id){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó operar en un Sprint sin tener token de edición."
            ]);
        }else if(isset($user) && $token_owner == null){
            return response()->json([
                "action" => "error",
                "msg" => "No hay un token definido, decidan primero quién lo utilizará."
            ]);
        }
        else{
            return 0;
        }
    }

    /* Indica a los compañeros que tienen que recargar el gantt */
    private function flagDirty($user){
        $compañeros = User::where('comision_id', $user->comision_id)->where('id', '!=' , $user->id)->get();
        foreach ($compañeros as $key => $compa) { //no necesito lock, solo una persona lo puede acceder al momento
            Cache::put('datos-dirty-'.$compa->comision_id."-".$compa->id, 1);
        }
    }
}