<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmInteraccion extends Model
{
    use SoftDeletes;

    protected $table = 'crm_interacciones';
    protected $primaryKey = 'ID_Interaccion';

    protected $fillable = [
        'InterTipo',
        'InterDescripcion',
        'InterFecha',
        'InterEstado',
        'InterResultado',
        'InterSeguimiento',
        'FK_InterCliente',
        'FK_InterPersonal',
        'FK_InterOportunidad'
    ];

    protected $dates = ['deleted_at', 'InterFecha', 'InterSeguimiento'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'FK_InterCliente', 'ID_Cli');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'FK_InterPersonal', 'ID_Pers');
    }

    public function oportunidad()
    {
        return $this->belongsTo(CrmOportunidad::class, 'FK_InterOportunidad', 'ID_Oportunidad');
    }
} 