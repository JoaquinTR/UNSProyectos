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
        Schema::create('sprint', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('numero');
            $table->enum('nota_final', ['A+','A','A-','B+','B','B-','C+','C','C-','D+','D','D-','E'])->nullable(); #seteada en corecciones parciales
            $table->unsignedTinyInteger('iniciado')->default(0);
            $table->unsignedTinyInteger('entregado')->default(0);
            $table->unsignedInteger('comision_id');
            $table->date('comienzo');
            $table->date('deadline');
            $table->timestamps();
        });
        Schema::table('sprint', function (Blueprint $table) {
            //Constraints
            $table->foreign('comision_id')->references('id')->on('comision')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sprint');
    }
};
