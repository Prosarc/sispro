<?php

namespace App\Http\Controllers;

use App\Respel;
use App\ResiduosGener;
use App\Tratamiento;
use App\Cliente;
use App\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RespelImportController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('sedes')->get();
        return view('respels.import', compact('clientes'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $csvData = array_map(function($line) {
                return str_getcsv($line, ',', '"', '\\');
            }, file($file->getRealPath()));
            
            // Obtener los encabezados
            $headers = array_shift($csvData);
            
            // Validar que el archivo tenga todas las columnas requeridas
            $requiredColumns = [
                'cliente_id',
                'sede_id',
                'nombre',
                'descripcion',
                'clasificacion_y',
                'clasificacion_a',
                'peligrosidad',
                'estado_fisico',
                'tratamiento',
                'sustancia_controlada',
                'tipo_sustancia_controlada',
                'nombre_sustancia_controlada',
                'declaracion_residuo'
            ];
            
            // Obtener los índices de las columnas
            $columnIndexes = array_flip($headers);
            
            // Verificar que todas las columnas requeridas estén presentes
            foreach ($requiredColumns as $column) {
                if (!isset($columnIndexes[$column])) {
                    throw new \Exception("La columna '$column' es requerida pero no está presente en el archivo CSV.");
                }
            }

            // Procesar cada fila
            foreach ($csvData as $row) {
                if (empty($row[$columnIndexes['nombre']])) continue; // Saltar filas vacías
                
                // Validar que el cliente y la sede existan y estén relacionados
                $clienteId = $row[$columnIndexes['cliente_id']];
                $sedeId = $row[$columnIndexes['sede_id']];
                
                $cliente = Cliente::find($clienteId);
                if (!$cliente) {
                    throw new \Exception('El cliente con ID ' . $clienteId . ' no existe.');
                }

                $sede = Sede::find($sedeId);
                if (!$sede) {
                    throw new \Exception('La sede con ID ' . $sedeId . ' no existe.');
                }

                if ($sede->FK_SedeCli != $clienteId) {
                    throw new \Exception('La sede ' . $sedeId . ' no pertenece al cliente ' . $clienteId);
                }

                // Validar la peligrosidad
                $peligrosidad = $row[$columnIndexes['peligrosidad']];
                $peligrosidadValida = ['No peligroso', 'Corrosivo', 'Reactivo', 'Explosivo', 'Toxico', 'Inflamable', 'Patógeno - Infeccioso', 'Radiactivo'];
                if (!in_array($peligrosidad, $peligrosidadValida)) {
                    throw new \Exception("Peligrosidad inválida: '$peligrosidad'. Valores permitidos: " . implode(', ', $peligrosidadValida));
                }

                // Validar el estado físico
                $estadoFisico = $row[$columnIndexes['estado_fisico']];
                $estadosValidos = ['Líquido', 'Sólido', 'Gaseoso', 'SemiSólido'];
                if (!in_array($estadoFisico, $estadosValidos)) {
                    throw new \Exception("Estado físico inválido: '$estadoFisico'. Valores permitidos: " . implode(', ', $estadosValidos));
                }

                // Validar clasificación Y si está presente
                $clasificacionY = $row[$columnIndexes['clasificacion_y']];
                if (!empty($clasificacionY)) {
                    $clasificacionesY = ['Y1','Y2','Y3','Y4','Y5','Y6','Y7','Y8','Y9','Y10','Y11','Y12','Y13','Y14','Y15','Y16','Y17','Y18','Y19','Y20','Y21','Y22','Y23','Y24','Y25','Y26','Y27','Y28','Y29','Y30','Y31','Y32','Y33','Y34','Y35','Y36','Y37','Y38','Y39','Y40','Y41','Y42','Y43','Y44','Y45'];
                    if (!in_array($clasificacionY, $clasificacionesY)) {
                        throw new \Exception("Clasificación Y inválida: '$clasificacionY'");
                    }
                }

                // Validar clasificación A si está presente
                $clasificacionA = $row[$columnIndexes['clasificacion_a']];
                if (!empty($clasificacionA)) {
                    $clasificacionesA = ['A1010','A1020','A1030','A1040','A1050','A1060','A1070','A1080','A1090','A1100','A1110','A1120','A1130','A1140','A1150','A1160','A1170','A1180','A2010','A2020','A2030','A2040','A2050','A2060','A3010','A3020','A3030','A3040','A3050','A3060','A3070','A3080','A3090','A3100','A3110','A3120','A3130','A3140','A3150','A3160','A3170','A3180','A3190','A3200','A4010','A4020','A4030','A4040','A4050','A4060','A4070','A4080','A4090','A4100','A4110','A4120','A4130','A4140','A4150','A4160'];
                    if (!in_array($clasificacionA, $clasificacionesA)) {
                        throw new \Exception("Clasificación A inválida: '$clasificacionA'");
                    }
                }

                // Crear el residuo
                $respel = new Respel();
                $respel->RespelName = $row[$columnIndexes['nombre']];
                $respel->RespelDescrip = $row[$columnIndexes['descripcion']];
                $respel->YRespelClasf4741 = $clasificacionY;
                $respel->ARespelClasf4741 = $clasificacionA;
                $respel->RespelIgrosidad = $peligrosidad;
                $respel->RespelEstado = $estadoFisico;
                
                // Asignar el tratamiento
                if (strtolower($row[$columnIndexes['tratamiento']]) == 'incineracion') {
                    $respel->RespelTratamiento = 1; // ID para incineración
                } else {
                    $respel->RespelTratamiento = 2; // ID para celda de seguridad
                }

                // Campos por defecto
                $respel->RespelStatus = 'Pendiente';
                $respel->RespelSlug = Str::slug($respel->RespelName);
                $respel->RespelDelete = 0;
                $respel->RespelPublic = 0;
                $respel->FK_RespelSede = $sedeId;
                $respel->FK_RespelCliente = $clienteId;

                // Archivos por defecto
                $respel->RespelHojaSeguridad = 'RespelHojaDefault.pdf';
                $respel->RespelTarj = 'RespelTarjetaDefault.pdf';
                $respel->RespelFoto = 'RespelFotoDefault.png';

                // Campos de sustancia controlada
                $respel->SustanciaControlada = $row[$columnIndexes['sustancia_controlada']] ?? 0;
                $respel->SustanciaControladaTipo = $row[$columnIndexes['tipo_sustancia_controlada']] ?? 0;
                $respel->SustanciaControladaNombre = $row[$columnIndexes['nombre_sustancia_controlada']] ?? null;
                $respel->SustanciaControladaDocumento = 'SustanciaControlDocDefault.pdf';
                
                // Declaración
                $respel->RespelDeclaracion = $row[$columnIndexes['declaracion_residuo']] ?? 0;
                
                $respel->save();

                // Crear requerimiento asociado
                $requerimiento = new Requerimiento();
                $requerimiento->ofertado = 1;
                $requerimiento->FK_ReqRespel = $respel->ID_Respel;
                $requerimiento->forevaluation = 1;
                $requerimiento->FK_ReqTrata = $respel->RespelTratamiento;
                $requerimiento->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Residuos importados correctamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al importar residuos: ' . $e->getMessage());
        }
    }
} 