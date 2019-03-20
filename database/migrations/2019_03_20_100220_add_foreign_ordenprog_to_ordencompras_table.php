<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignOrdenprogToOrdencomprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordencompras', function (Blueprint $table) {
            $table->unsignedInteger('FK_OrdenProg');
            $table->foreign('FK_OrdenProg')->references('ID_ProgVeh')->on('ProgVehiculos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordencompras', function (Blueprint $table) {
            $table->dropForeign('ordencompras_FK_OrdenProg_foreign');
            $table->dropColumn('FK_OrdenProg');
        });
    }
}
