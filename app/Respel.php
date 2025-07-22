<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Respel extends Model
{
    protected $table='respels';

    protected $fillable=['RespelName', 'RespelDescrip', 'YRespelClasf4741', 'ARespelClasf4741', 'RespelIgrosidad', 'RespelEstado',' RespelHojaSeguridad', 'RespelTarj', 'RespelStatus','RespelDelete', 'RespelSlug', 'FK_RespelCoti', 'RespelStatusDescription', 'RespelPublic', 'FK_SubCategoryRP'];

    protected $primaryKey = 'ID_Respel';

    public function getRouteKeyName()
	{
	    return 'RespelSlug';
    }
    
	public function Cotizacion()
	{
	    return $this->belongsTo('App\Cotizacion', 'FK_RespelCoti', 'ID_Coti');
	}

     public function SolicitudResiduo(){
         return $this->hasManyThrough(
             'App\SolicitudResiduo',
             'App\ResiduosGener',
             'FK_Respel',     // Clave foránea en residuos_geners
             'FK_SolResRg',   // Clave foránea en solicitud_residuos
             'ID_Respel',     // Clave local en respels
             'ID_SGenerRes'   // Clave local en residuos_geners
         );
    }
    
    public function ResiduosGener(){
		return $this->hasMany('App\ResiduosGener', 'FK_Respel', 'ID_Respel');
	}

    // lista los requerimientos de un residuo 1 a muchos
    public function requerimientos(){
        return $this->hasMany('App\Requerimiento', 'FK_ReqRespel', 'ID_Respel');
        //como residuos tiene muchos requerimientos
    }

    public function SubcategoryRespelpublic()
    {
        return $this->belongsTo('App\Subcategoryrespelpublic', 'FK_SubCategoryRP', 'ID_SubCategoryRP');
    }
}
