<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('links')->insert([
		    ['comision_id'=>1,'sprint_id'=>1,'type'=> 0, 'source'=>1,'target'=>2],
			['comision_id'=>1,'sprint_id'=>1,'type'=> 0, 'source'=>1,'target'=>4],
			['comision_id'=>1,'sprint_id'=>1,'type'=> 0, 'source'=>5,'target'=>3],
			['comision_id'=>1,'sprint_id'=>1,'type'=> 0, 'source'=>4,'target'=>5],
            ['comision_id'=>1,'sprint_id'=>1,'type'=> 1, 'source'=>3,'target'=>6],
            //Sprint #2
            ['comision_id'=>1,'sprint_id'=>2,'type'=> 0, 'source'=>7,'target'=>8],
            ['comision_id'=>1,'sprint_id'=>2,'type'=> 1, 'source'=>8,'target'=>9],
            ['comision_id'=>1,'sprint_id'=>2,'type'=> 2, 'source'=>9,'target'=>10]
        ]);
    }
}
