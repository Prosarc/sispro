<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jaulas extends Model
{
    protected $table = 'jaulas';

    protected $fillable = ['ID_Jaula', 'Nombre_Jaula', 'CapacidadMaxima', 'FK_Trata', 'EstadoOcupacion', 'PorcentajeOcupacion'];

    protected $primaryKey = 'ID_Jaula';


}
