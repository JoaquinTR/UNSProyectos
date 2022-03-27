<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SprintTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    \DB::table('sprint')->insert([
		    ['id'=>1, 'iniciado'=>1, 'entregado'=>1, 'numero'=>1,'comision_id'=>1, 'comienzo'=> '2022-3-01', 'deadline'=> '2022-4-30'],
            ['id'=>2, 'iniciado'=>1, 'entregado'=>0, 'numero'=>2,'comision_id'=>1, 'comienzo'=> '2022-5-01',  'deadline'=> '2022-6-30'],
            ['id'=>3, 'iniciado'=>0, 'entregado'=>0, 'numero'=>3,'comision_id'=>1, 'comienzo'=> '2022-7-01',  'deadline'=> '2022-8-30'],
            ['id'=>4, 'iniciado'=>0, 'entregado'=>0, 'numero'=>4,'comision_id'=>1, 'comienzo'=> '2022-9-01',  'deadline'=> '2022-10-30'],

            /* Comision 2 clon de la 1 */
            ['id'=>5, 'iniciado'=>1, 'entregado'=>1, 'numero'=>1,'comision_id'=>2, 'comienzo'=> '2022-3-01', 'deadline'=> '2022-4-30'],
            ['id'=>6, 'iniciado'=>1, 'entregado'=>0, 'numero'=>2,'comision_id'=>2, 'comienzo'=> '2022-5-01',  'deadline'=> '2022-6-30'],
            ['id'=>7, 'iniciado'=>0, 'entregado'=>0, 'numero'=>3,'comision_id'=>2, 'comienzo'=> '2022-7-01',  'deadline'=> '2022-8-30'],
            ['id'=>8, 'iniciado'=>0, 'entregado'=>0, 'numero'=>4,'comision_id'=>2, 'comienzo'=> '2022-9-01',  'deadline'=> '2022-10-30'],
        ]);			
    }
}
