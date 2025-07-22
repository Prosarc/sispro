<?php

namespace App\Http\Controllers;

use App\CrmOportunidad;
use App\CrmInteraccion;
use App\Cliente;
use App\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrmOportunidadController extends Controller
{
    public function index()
    {
        $oportunidades = CrmOportunidad::with(['cliente', 'comercial', 'interacciones'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('crm.oportunidades.index', compact('oportunidades'));
    }

    public function create()
    {
        $clientes = Cliente::where('CliDelete', 0)->get();
        $comerciales = Personal::whereHas('cargo', function($query) {
            $query->where('CargName', 'Comercial');
        })->get();

        return view('crm.oportunidades.create', compact('clientes', 'comerciales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'OportName' => 'required|string|max:255',
            'OportDescripcion' => 'nullable|string',
            'OportValor' => 'required|numeric|min:0',
            'OportEstado' => 'required|string',
            'OportFechaCierre' => 'nullable|date',
            'OportFuente' => 'required|string',
            'FK_OportCliente' => 'required|exists:clientes,ID_Cli',
            'FK_OportComercial' => 'required|exists:personals,ID_Pers'
        ]);

        DB::beginTransaction();
        try {
            $oportunidad = CrmOportunidad::create($request->all());

            // Crear la primera interacción automáticamente
            CrmInteraccion::create([
                'InterTipo' => 'Creación de Oportunidad',
                'InterDescripcion' => 'Se ha creado una nueva oportunidad de negocio',
                'InterFecha' => now(),
                'InterEstado' => 'Completado',
                'FK_InterCliente' => $request->FK_OportCliente,
                'FK_InterPersonal' => Auth::user()->FK_UserPers,
                'FK_InterOportunidad' => $oportunidad->ID_Oportunidad
            ]);

            DB::commit();
            return redirect()->route('crm.oportunidades.show', $oportunidad->ID_Oportunidad)
                ->with('success', 'Oportunidad creada exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear la oportunidad: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $oportunidad = CrmOportunidad::with(['cliente', 'comercial', 'interacciones.personal'])
            ->findOrFail($id);

        return view('crm.oportunidades.show', compact('oportunidad'));
    }

    public function edit($id)
    {
        $oportunidad = CrmOportunidad::findOrFail($id);
        $clientes = Cliente::where('CliDelete', 0)->get();
        $comerciales = Personal::whereHas('cargo', function($query) {
            $query->where('CargName', 'Comercial');
        })->get();

        return view('crm.oportunidades.edit', compact('oportunidad', 'clientes', 'comerciales'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'OportName' => 'required|string|max:255',
            'OportDescripcion' => 'nullable|string',
            'OportValor' => 'required|numeric|min:0',
            'OportEstado' => 'required|string',
            'OportFechaCierre' => 'nullable|date',
            'OportFuente' => 'required|string',
            'FK_OportCliente' => 'required|exists:clientes,ID_Cli',
            'FK_OportComercial' => 'required|exists:personals,ID_Pers'
        ]);

        $oportunidad = CrmOportunidad::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $oldEstado = $oportunidad->OportEstado;
            $oportunidad->update($request->all());

            // Si cambió el estado, crear una nueva interacción
            if ($oldEstado != $request->OportEstado) {
                CrmInteraccion::create([
                    'InterTipo' => 'Cambio de Estado',
                    'InterDescripcion' => "Estado cambiado de {$oldEstado} a {$request->OportEstado}",
                    'InterFecha' => now(),
                    'InterEstado' => 'Completado',
                    'FK_InterCliente' => $oportunidad->FK_OportCliente,
                    'FK_InterPersonal' => Auth::user()->FK_UserPers,
                    'FK_InterOportunidad' => $oportunidad->ID_Oportunidad
                ]);
            }

            DB::commit();
            return redirect()->route('crm.oportunidades.show', $oportunidad->ID_Oportunidad)
                ->with('success', 'Oportunidad actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al actualizar la oportunidad: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $oportunidad = CrmOportunidad::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Eliminar interacciones relacionadas
            $oportunidad->interacciones()->delete();
            // Eliminar la oportunidad
            $oportunidad->delete();

            DB::commit();
            return redirect()->route('crm.oportunidades.index')
                ->with('success', 'Oportunidad eliminada exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al eliminar la oportunidad: ' . $e->getMessage());
        }
    }
} 