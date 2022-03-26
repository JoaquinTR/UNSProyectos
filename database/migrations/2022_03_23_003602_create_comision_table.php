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
        Schema::create('comision', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre'); #Comision numero 1 comision numero 2, etc, a decisión de la cátedra
            $table->enum('nota_final', ['A+','A','A-','B+','B','B-','C+','C','C-','D+','D','D-','E'])->nullable(); #seteada en correcion de final de cuatri
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
        Schema::dropIfExists('comision');
    }
};
