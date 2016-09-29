<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->increments('id');

            $table->string('parada_subida');
            $table->string('parada_bajada');

            $table->timestamps();
        });


        Schema::table('reservas', function (Blueprint $table) {
    
            $table->integer('viaje_id')->unsigned();
            $table->foreign('viaje_id')->references('id')->on('viajes');

            $table->integer('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')->on('clientes');

        });


    }    

        /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reservas');
        schema::table('reservas', function (Blueprint $table){
            $table->dropForeign('reservas_viaje_id_foreign');
            $table->dropForeign('reservas_cliente_id_foreign');
        })
    }
}
