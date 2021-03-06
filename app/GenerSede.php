<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenerSede extends Model
{
    protected $table='gener_sedes';

    protected $fillable=['GSedeName', 'GSedeAddress', 'GSedePhone1', 'GSedeExt1', 'GSedePhone2',' GSedeExt2', 'GSedeEmail', 'GSedeCelular', 'GSedeSlug', 'FK_GSede', 'FK_GSedeMun', 'GSedeDelete'];

    protected $primaryKey = 'ID_GSede';
    	/**
	 * Get the route key for the model.
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
	    return 'GSedeSlug';
	}

	public function generadors()
	{
	 return $this->belongsTo('App\Generador', 'FK_GSede', 'ID_Gener');
	}

	public function municipio()
	{
	 return $this->belongsTo('App\Municipio', 'FK_GSedeMun', 'ID_Mun');
	}

	public function SolicitudServicio()
    {
        return $this->hasMany('App\SolicitudServicio', 'ID_SolSer');//como genersede tiene muchas solicitudes de servicio
    }

    public function Areas(){
    	return $this->hasMany('App\GenerSede', 'ID_Area', 'id');//como genersedes tiene muchas areas
	}

	public function resgener(){
		return $this->hasMany('App\ResiduosGener', 'FK_SGener', 'ID_GSede');
	}

	public function programacionesdeservicio(){
		return $this->hasMany('App\ProgramacionVehiculo', 'recolect', 'FK_ColectSgen', 'FK_ColectProg');
	}

	public function manifiestos(){
		return $this->hasMany('App\Manifiesto','FK_ManifGenerSede', 'ID_GSede');
    }
}
