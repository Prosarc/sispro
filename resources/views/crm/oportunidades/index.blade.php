@extends('crm.layout')

@section('crm-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Oportunidades</h1>
        <a href="{{ route('crm.oportunidades.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Oportunidad
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tabla-oportunidades">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Nombre</th>
                            <th>Valor</th>
                            <th>Estado</th>
                            <th>Comercial</th>
                            <th>Fecha Cierre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($oportunidades as $oportunidad)
                        <tr>
                            <td>{{ $oportunidad->cliente->CliName }}</td>
                            <td>{{ $oportunidad->OportName }}</td>
                            <td>${{ number_format($oportunidad->OportValor, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $oportunidad->OportEstado == 'Ganado' ? 'success' : ($oportunidad->OportEstado == 'Perdido' ? 'danger' : 'info') }}">
                                    {{ $oportunidad->OportEstado }}
                                </span>
                            </td>
                            <td>{{ $oportunidad->comercial->PersFirstName }} {{ $oportunidad->comercial->PersLastName }}</td>
                            <td>{{ $oportunidad->OportFechaCierre ? $oportunidad->OportFechaCierre->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('crm.oportunidades.show', $oportunidad->ID_Oportunidad) }}" 
                                        class="btn btn-sm btn-info" 
                                        title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('crm.oportunidades.edit', $oportunidad->ID_Oportunidad) }}" 
                                        class="btn btn-sm btn-warning" 
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        title="Eliminar"
                                        onclick="confirmarEliminacion('{{ $oportunidad->ID_Oportunidad }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="form-eliminar-{{ $oportunidad->ID_Oportunidad }}" 
                                    action="{{ route('crm.oportunidades.destroy', $oportunidad->ID_Oportunidad) }}" 
                                    method="POST" 
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarEliminacion(id) {
    if (confirm('¿Está seguro que desea eliminar esta oportunidad?')) {
        document.getElementById('form-eliminar-' + id).submit();
    }
}

$(document).ready(function() {
    $('#tabla-oportunidades').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
        },
        order: [[5, 'asc']], // Ordenar por fecha de cierre
        columnDefs: [
            {
                targets: [6], // Columna de acciones
                orderable: false
            }
        ]
    });
});
</script>
@endpush 