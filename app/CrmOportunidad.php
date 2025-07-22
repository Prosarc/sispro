<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrmOportunidad extends Model
{
    protected $table = 'crm_oportunidades';
    protected $primaryKey = 'ID_Oport';

    protected $fillable = [
        'OportNombre',
        'OportDescripcion',
        'OportValor',
        'OportFechaCierre',
        'OportEstado',
        'FK_OportCliente',
        'FK_OportComercial'
    ];

    protected $dates = [
        'OportFechaCierre',
        'created_at',
        'updated_at'
    ];

    // Estados disponibles para las oportunidades
    const ESTADOS = [
        'Abierta',
        'En Cotización',
        'En Programación',
        'Cobro 15 días',
        'Cobro 30 días',
        'Ganada',
        'Perdida'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'FK_OportCliente', 'ID_Cli');
    }

    public function comercial()
    {
        return $this->belongsTo(Personal::class, 'FK_OportComercial', 'ID_Pers');
    }

    public function interacciones()
    {
        return $this->hasMany(CrmInteraccion::class, 'FK_InterOport', 'ID_Oport');
    }
} 