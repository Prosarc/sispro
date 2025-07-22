<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrmOportunidad;
use App\CrmInteraccion;
use App\Cliente;
use App\Personal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CrmDashboardController extends Controller
{
    public function index()
    {
        // Obtener oportunidades agrupadas por estado
        $portafolio = CrmOportunidad::with(['cliente', 'comercial'])
            ->where('OportEstado', 'Abierta')
            ->orderBy('created_at', 'desc')
            ->get();

        $cotizacion = CrmOportunidad::with(['cliente', 'comercial'])
            ->where('OportEstado', 'En Cotización')
            ->orderBy('created_at', 'desc')
            ->get();

        $programacion = CrmOportunidad::with(['cliente', 'comercial'])
            ->where('OportEstado', 'En Programación')
            ->orderBy('created_at', 'desc')
            ->get();

        $cobro15 = CrmOportunidad::with(['cliente', 'comercial'])
            ->where('OportEstado', 'Cobro 15 días')
            ->orderBy('created_at', 'desc')
            ->get();

        $cobro30 = CrmOportunidad::with(['cliente', 'comercial'])
            ->where('OportEstado', 'Cobro 30 días')
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas
        $totalOportunidades = CrmOportunidad::count();
        $totalValor = CrmOportunidad::sum('OportValor');
        $oportunidadesAbiertas = CrmOportunidad::where('OportEstado', 'Abierta')->count();
        $oportunidadesGanadas = CrmOportunidad::where('OportEstado', 'Ganada')->count();

        // Últimas interacciones
        $ultimasInteracciones = CrmInteraccion::with(['cliente', 'personal'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('crm.dashboard', compact(
            'portafolio',
            'cotizacion', 
            'programacion',
            'cobro15',
            'cobro30',
            'totalOportunidades',
            'totalValor',
            'oportunidadesAbiertas',
            'oportunidadesGanadas',
            'ultimasInteracciones'
        ));
    }
} 