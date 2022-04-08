<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; 
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
 
class TaskController extends Controller
{
    public function store(Request $request){
        $user = auth()->user();
        
        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId, $request->comisionId, $user)) return $res;
 
        $task = new Task();
 
        $task->text = $request->text;
        $task->start_date = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;
        $task->sortorder = Task::max("sortorder") + 1;
        $task->sprint_id = $request->sprintId;
        $task->comision_id = $request->comisionId;

        $task->save();
 
        $this->flagDirty($user);
        return response()->json([
            "action"=> "inserted",
            "tid" => $task->id
        ]);
    }   
 
    public function update($id, Request $request){
        $task = Task::find($id);
        $user = auth()->user();
        
        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId, $request->comisionId, $user)) return $res;
 
        $task->text = $request->text;
        $task->start_date = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;
 
        $task->save();

        if($request->has("target")){
            $this->updateOrder($id, $request->target);
        }
 
        $this->flagDirty($user);
        return response()->json([
            "action"=> "updated"
        ]);
    }

    /**
     * Reordena una tarea, realiza el trabajo a más bajo nivel.
     */
    private function updateOrder($taskId, $target){
        
        $nextTask = false;
        $targetId = $target;
     
        if(strpos($target, "next:") === 0){
            $targetId = substr($target, strlen("next:"));
            $nextTask = true;
        }
     
        if($targetId == "null")
            return;
     
        $targetOrder = Task::find($targetId)->sortorder;
        if($nextTask)
            $targetOrder++;
     
        Task::where("sortorder", ">=", $targetOrder)->increment("sortorder");
     
        $updatedTask = Task::find($taskId);
        $updatedTask->sortorder = $targetOrder;
        $updatedTask->save();
    }
 
    public function destroy($id, Request $request){
        $task = Task::find($id);
        $user = auth()->user();

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId, $request->comisionId, $user)) return $res;
        
        $task->delete();
 
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