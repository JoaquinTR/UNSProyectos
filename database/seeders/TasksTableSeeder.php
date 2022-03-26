<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    \DB::table('tasks')->insert([
			['comision_id'=>1,'sprint_id'=>1,'text'=>'Diseñar el proyecto', 'start_date'=>'2022-04-01 00:00:00','duration'=>4, 'progress'=>1, 'sortorder'=>1],
			['comision_id'=>1,'sprint_id'=>1,'text'=>'Diagrama de entidad relacion', 'start_date'=>'2022-04-05 00:00:00','duration'=>3, 'progress'=>1, 'sortorder'=>3],
			['comision_id'=>1,'sprint_id'=>1,'text'=>'Implementar presentacion de proyecto', 'start_date'=>'2022-04-14 00:00:00','duration'=>2, 'progress'=>1, 'sortorder'=>5],
			['comision_id'=>1,'sprint_id'=>1,'text'=>'Plantear Sprints', 'start_date'=>'2022-04-05 00:00:00','duration'=>5, 'progress'=>1, 'sortorder'=>2],
			['comision_id'=>1,'sprint_id'=>1,'text'=>'Dividir tareas entre integrantes', 'start_date'=>'2022-04-10 00:00:00','duration'=>4, 'progress'=>1, 'sortorder'=>4],
            ['comision_id'=>1,'sprint_id'=>1,'text'=>'Practicar presentacion', 'start_date'=>'2022-04-14 00:00:00','duration'=>2, 'progress'=>1, 'sortorder'=>6],
            //Sprint #2
            ['comision_id'=>1,'sprint_id'=>2,'text'=>'Crear estructura de base de datos', 'start_date'=>'2022-05-01 00:00:00','duration'=>5, 'progress'=>0.5, 'sortorder'=>1],
            ['comision_id'=>1,'sprint_id'=>2,'text'=>'Programar API Backend de login', 'start_date'=>'2022-05-06 00:00:00','duration'=>4, 'progress'=>0.5, 'sortorder'=>2],
            ['comision_id'=>1,'sprint_id'=>2,'text'=>'Programar Frontend de login', 'start_date'=>'2022-05-07 00:00:00','duration'=>5, 'progress'=>0.5, 'sortorder'=>3],
            ['comision_id'=>1,'sprint_id'=>2,'text'=>'Testear Frontend de login', 'start_date'=>'2022-05-07 00:00:00','duration'=>5, 'progress'=>0.5, 'sortorder'=>4],
        ]);			
    }
}
