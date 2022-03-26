<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Sprint;
use App\Models\Task;
 
class TaskController extends Controller
{
    public function store(Request $request){

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId)) return $res;
 
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
 
        return response()->json([
            "action"=> "inserted",
            "tid" => $task->id
        ]);
    }
 
    public function update($id, Request $request){
        $task = Task::find($id);

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId)) return $res;
 
        $task->text = $request->text;
        $task->start_date = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;
 
        $task->save();

        if($request->has("target")){
            $this->updateOrder($id, $request->target);
        }
 
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

        // Control de editabilidad
        if($res = $this->testEditable($request->sprintId)) return $res;
        
        $task->delete();
 
        return response()->json([
            "action"=> "deleted"
        ]);
    }

    private function testEditable($sprint_id){
        // Control de editabilidad
        if($sprint_id && Sprint::findOrFail($sprint_id)->entregado){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó guardar en un Sprint ya entregado"
            ]);
        }else{
            return 0;
        }
    }
}