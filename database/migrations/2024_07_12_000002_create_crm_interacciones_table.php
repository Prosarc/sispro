<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmInteraccionesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_interacciones', function (Blueprint $table) {
            $table->bigIncrements('ID_Interaccion');
            $table->string('InterTipo'); // Llamada, Email, Reunión, Visita, etc.
            $table->text('InterDescripcion');
            $table->dateTime('InterFecha');
            $table->string('InterEstado')->default('Pendiente'); // Pendiente, Completado, Cancelado
            $table->text('InterResultado')->nullable();
            $table->dateTime('InterSeguimiento')->nullable();
            $table->unsignedInteger('FK_InterCliente');
            $table->unsignedInteger('FK_InterPersonal');
            $table->unsignedBigInteger('FK_InterOportunidad')->nullable();
            $table->foreign('FK_InterCliente')->references('ID_Cli')->on('clientes');
            $table->foreign('FK_InterPersonal')->references('ID_Pers')->on('personals');
            $table->foreign('FK_InterOportunidad')->references('ID_Oportunidad')->on('crm_oportunidades');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('crm_interacciones');
    }
} 