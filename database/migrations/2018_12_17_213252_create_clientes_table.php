<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('ID_Cli')->unique();
            $table->string('CliNit', 20);
            $table->string('CliName');
            $table->string('CliShortname');
            $table->string('CliCategoria', 32);
            $table->string('CliRut');
            $table->string('CliCamaraComercio');
            $table->string('CliRepresentanteLegal');
            $table->string('CliCertificaionBancaria');
            $table->string('CliCertificaionComercial');
            $table->string('CliCertificaionComercial2');
            $table->string('CliType', 32)->nullable();
            $table->boolean('CliAuditable');
            $table->timestamps();
            $table->string('CliSlug')->unique();
            $table->string('CliStatus');
            $table->string('TipoFacturacion')->default('Contado');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
