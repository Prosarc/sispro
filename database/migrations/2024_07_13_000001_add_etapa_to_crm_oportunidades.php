<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEtapaToCrmOportunidades extends Migration
{
    public function up()
    {
        Schema::table('crm_oportunidades', function (Blueprint $table) {
            $table->enum('etapa', ['portafolio', 'cotizacion', 'programacion', 'cobro15', 'cobro30'])->after('OportEstado')->default('portafolio');
        });
    }

    public function down()
    {
        Schema::table('crm_oportunidades', function (Blueprint $table) {
            $table->dropColumn('etapa');
        });
    }
} 