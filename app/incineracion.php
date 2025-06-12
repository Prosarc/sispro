<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class incineracion extends Model
{
    protected $table = 'incineracion';

    protected $fillable = ['ID_Incineracion', 'IncColor', 'Turno', 'IngTurno', 'Hornerop', 'Horneroa', 'Cantidadprog', 'FK_SolRes', 'CantidadEje', 'EjecutadovsProgramado', 'FechaIncineracion', 'FK_Solicitud'];

    protected $primaryKey = 'ID_Incineracion';


}
