<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    \DB::table('users')->insert([
		    ['name'=>'profesor', 'alias' => 'profe', 'email'=>'profesor@cs.uns.edu.ar','password' => Hash::make('profesor'), 'type'=> 'profesor','comision_id'=>null],
            ['name'=>'alumno', 'alias' => 'alu', 'email'=>'alumno@cs.uns.edu.ar','password' => Hash::make('alumno'), 'type'=> 'alumno','comision_id'=>1],
            ['name'=>'alumno2', 'alias' => 'alu', 'email'=>'alumno2@cs.uns.edu.ar','password' => Hash::make('alumno2'), 'type'=> 'alumno','comision_id'=>1],
        ]);			
    }
}
