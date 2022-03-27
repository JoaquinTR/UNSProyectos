<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComisionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    \DB::table('comision')->insert([
		    ['id'=>1, 'nombre'=> 'Comision #1'],
            ['id'=>2, 'nombre'=> 'Comision #2']
        ]);			
    }
}
