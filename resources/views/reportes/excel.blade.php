<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Fecha de Servicio</th>
                <th>N° de Servicio</th>
                <th>Estado</th>
                <th>Residuo</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Tratamiento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicios as $servicio)
                @foreach($servicio->SolicitudResiduo as $residuo)
                    <tr>
                        <td>{{ $servicio->programacionesrecibidas->first()->ProgVehFecha ?? 'N/A' }}</td>
                        <td>{{ $servicio->ID_SolSer }}</td>
                        <td>{{ $servicio->SolSerStatus }}</td>
                        <td>{{ $residuo->generespel->respels->RespelName }}</td>
                        <td>{{ $residuo->SolResKgConciliado ?? $residuo->SolResKgRecibido ?? $residuo->SolResKgEnviado ?? 'N/A' }}</td>
                        <td>{{ $residuo->SolResTypeUnidad }}</td>
                        <td>{{ $residuo->requerimiento->tratamiento->TratName ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html> 