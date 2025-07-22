<?php

namespace App\Http\Controllers;

use App\CrmInteraccion;
use App\CrmOportunidad;
use App\Cliente;
use App\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrmInteraccionController extends Controller
{
    public function index()
    {
        $interacciones = CrmInteraccion::with(['cliente', 'personal', 'oportunidad'])
            ->orderBy('InterFecha', 'desc')
            ->get();

        return view('crm.interacciones.index', compact('interacciones'));
    }

    public function create()
    {
        $clientes = Cliente::where('CliDelete', 0)->get();
        $personal = Personal::where('PersDelete', 0)->get();
        $oportunidades = CrmOportunidad::whereNull('deleted_at')->get();

        return view('crm.interacciones.create', compact('clientes', 'personal', 'oportunidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'InterTipo' => 'required|string',
            'InterDescripcion' => 'required|string',
            'InterFecha' => 'required|date',
            'InterEstado' => 'required|string',
            'InterResultado' => 'nullable|string',
            'InterSeguimiento' => 'nullable|date',
            'FK_InterCliente' => 'required|exists:clientes,ID_Cli',
            'FK_InterPersonal' => 'required|exists:personals,ID_Pers',
            'FK_InterOportunidad' => 'nullable|exists:crm_oportunidades,ID_Oportunidad'
        ]);

        try {
            $interaccion = CrmInteraccion::create($request->all());

            // Si hay una oportunidad relacionada y la interacción está completada
            if ($request->FK_InterOportunidad && $request->InterEstado == 'Completado') {
                $oportunidad = CrmOportunidad::find($request->FK_InterOportunidad);
                // Actualizar la fecha de última interacción de la oportunidad
                $oportunidad->touch();
            }

            return redirect()->route('crm.interacciones.show', $interaccion->ID_Interaccion)
                ->with('success', 'Interacción creada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear la interacción: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $interaccion = CrmInteraccion::with(['cliente', 'personal', 'oportunidad'])
            ->findOrFail($id);

        return view('crm.interacciones.show', compact('interaccion'));
    }

    public function edit($id)
    {
        $interaccion = CrmInteraccion::findOrFail($id);
        $clientes = Cliente::where('CliDelete', 0)->get();
        $personal = Personal::where('PersDelete', 0)->get();
        $oportunidades = CrmOportunidad::whereNull('deleted_at')->get();

        return view('crm.interacciones.edit', compact('interaccion', 'clientes', 'personal', 'oportunidades'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'InterTipo' => 'required|string',
            'InterDescripcion' => 'required|string',
            'InterFecha' => 'required|date',
            'InterEstado' => 'required|string',
            'InterResultado' => 'nullable|string',
            'InterSeguimiento' => 'nullable|date',
            'FK_InterCliente' => 'required|exists:clientes,ID_Cli',
            'FK_InterPersonal' => 'required|exists:personals,ID_Pers',
            'FK_InterOportunidad' => 'nullable|exists:crm_oportunidades,ID_Oportunidad'
        ]);

        try {
            $interaccion = CrmInteraccion::findOrFail($id);
            $interaccion->update($request->all());

            return redirect()->route('crm.interacciones.show', $interaccion->ID_Interaccion)
                ->with('success', 'Interacción actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar la interacción: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $interaccion = CrmInteraccion::findOrFail($id);
            $interaccion->delete();

            return redirect()->route('crm.interacciones.index')
                ->with('success', 'Interacción eliminada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la interacción: ' . $e->getMessage());
        }
    }

    public function porCliente($clienteId)
    {
        $interacciones = CrmInteraccion::with(['personal', 'oportunidad'])
            ->where('FK_InterCliente', $clienteId)
            ->orderBy('InterFecha', 'desc')
            ->get();

        return view('crm.interacciones.por-cliente', compact('interacciones'));
    }

    public function porOportunidad($oportunidadId)
    {
        $interacciones = CrmInteraccion::with(['cliente', 'personal'])
            ->where('FK_InterOportunidad', $oportunidadId)
            ->orderBy('InterFecha', 'desc')
            ->get();

        return view('crm.interacciones.por-oportunidad', compact('interacciones'));
    }
} 