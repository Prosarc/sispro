<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class informeturno extends Model
{
    protected $table = 'informeturno';

    protected $fillable = ['Dieta', 'CondOperacion', 'Emisiones', 'OtrasObs', 'Analisis', 'TempPost', 'TempCombustion', 'VarTiro', 'TempChim', 'TempFMangas', 'SistemaCargue', 'OtrosEquipos', 'ActividadesBodega', 'ObsGenerales'];

    protected $primaryKey = 'ID_Informe';


}
