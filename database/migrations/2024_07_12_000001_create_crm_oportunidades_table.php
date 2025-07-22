<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmOportunidadesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_oportunidades', function (Blueprint $table) {
            $table->bigIncrements('ID_Oport');
            $table->string('OportNombre');
            $table->text('OportDescripcion')->nullable();
            $table->decimal('OportValor', 15, 2);
            $table->date('OportFechaCierre');
            $table->enum('OportEstado', ['Abierta', 'Ganada', 'Perdida']);
            $table->enum('etapa', ['portafolio', 'cotizacion', 'programacion', 'cobro15', 'cobro30']);
            $table->unsignedBigInteger('FK_OportCliente');
            $table->unsignedBigInteger('FK_OportComercial');
            $table->timestamps();

            $table->foreign('FK_OportCliente')
                ->references('ID_Cli')
                ->on('clientes')
                ->onDelete('cascade');

            $table->foreign('FK_OportComercial')
                ->references('ID_Pers')
                ->on('personals')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('crm_oportunidades');
    }
} 