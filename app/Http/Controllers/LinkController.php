<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Sprint;
use App\Models\Link;
 
class LinkController extends Controller
{
    public function store(Request $request){

        // Control de editabilidad
        if($request->sprintId && Sprint::findOrFail($request->sprintId)->entregado){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó guardar en un Sprint ya entregado"
            ]);
        }

        $link = new Link();
        
        $link->type = $request->type;
        $link->source = $request->source;
        $link->target = $request->target;
        $link->sprint_id = $request->sprintId;
        $link->comision_id = $request->comisionId;
        
        $link->save();
 
        return response()->json([
            "action"=> "inserted",
            "tid" => $link->id
        ]);
    }
 
    public function update($id, Request $request){

        // Control de editabilidad
        if($request->sprintId && Sprint::findOrFail($request->sprintId)->entregado){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó guardar en un Sprint ya entregado"
            ]);
        }

        $link = Link::find($id);
 
        $link->type = $request->type;
        $link->source = $request->source;
        $link->target = $request->target;
 
        $link->save();
 
        return response()->json([
            "action"=> "updated"
        ]);
    }
 
    public function destroy($id){

        // Control de editabilidad
        if($request->sprintId && Sprint::findOrFail($request->sprintId)->entregado){
            return response()->json([
                "action" => "error",
                "msg" => "Se intentó guardar en un Sprint ya entregado"
            ]);
        }

        $link = Link::find($id);
        $link->delete();
 
        return response()->json([
            "action"=> "deleted"
        ]);
    }
}