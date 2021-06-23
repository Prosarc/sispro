<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrefacturaTratamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prefactura_tratamientos', function (Blueprint $table) {
            $table->bigIncrements('ID_PrefacTratamiento');
            $table->unsignedBigInteger('FK_Prefactura')->nullable();
            $table->foreign('FK_Prefactura')->references('ID_Prefactura')->on('prefacturas')->onDelete('set null');
            $table->unsignedInteger('FK_Tratamiento')->nullable();
            $table->foreign('FK_Tratamiento')->references('ID_Trat')->on('tratamientos')->onDelete('set null');
            $table->unsignedDecimal('Subtotal_tratamiento', 8, 2)->default(0);
            $table->unsignedDecimal('cantidad_tratamiento', 8, 2)->default(0);
            $table->unsignedDecimal('unidad_tratamiento', 8, 2)->default(0);
            $table->unsignedDecimal('total_prefactratamiento', 8, 2)->default(0);
            $table->json('RMs');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prefactura_tratamientos');
    }
}
