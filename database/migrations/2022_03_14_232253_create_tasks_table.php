<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('text');
		    $table->integer('duration');
		    $table->float('progress');
		    $table->dateTime('start_date');
		    $table->integer('parent')->default(0);
            $table->integer('sortorder')->default(0);
            $table->unsignedInteger('sprint_id');
            $table->unsignedInteger('comision_id');
		    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
