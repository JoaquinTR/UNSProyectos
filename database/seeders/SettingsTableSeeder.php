<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    \DB::table('settings')->insert([
		    ['id'=>1, 'users_id' => 1],
            ['id'=>2, 'users_id' => 2],
            ['id'=>3, 'users_id' => 3],
            ['id'=>4, 'users_id' => 4],
            ['id'=>5, 'users_id' => 5]
        ]);			
    }
}
