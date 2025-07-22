@extends('crm.layout')

@section('crm-content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">{{ $oportunidad->OportName }}</h3>
                    <div>
                        <a href="{{ route('crm.oportunidades.edit', $oportunidad->ID_Oportunidad) }}" 
                            class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" 
                            class="btn btn-danger" 
                            onclick="confirmarEliminacion('{{ $oportunidad->ID_Oportunidad }}')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        <form id="form-eliminar-{{ $oportunidad->ID_Oportunidad }}" 
                            action="{{ route('crm.oportunidades.destroy', $oportunidad->ID_Oportunidad) }}" 
                            method="POST" 
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table">
                                <tr>
                                    <th width="30%">Cliente:</th>
                                    <td>{{ $oportunidad->cliente->CliName }}</td>
                                </tr>
                                <tr>
                                    <th>Valor:</th>
                                    <td>${{ number_format($oportunidad->OportValor, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span class="badge bg-{{ $oportunidad->OportEstado == 'Ganado' ? 'success' : ($oportunidad->OportEstado == 'Perdido' ? 'danger' : 'info') }}">
                                            {{ $oportunidad->OportEstado }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Comercial:</th>
                                    <td>{{ $oportunidad->comercial->PersFirstName }} {{ $oportunidad->comercial->PersLastName }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Cierre:</th>
                                    <td>{{ $oportunidad->OportFechaCierre ? $oportunidad->OportFechaCierre->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fuente:</th>
                                    <td>{{ $oportunidad->OportFuente }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Detalles</h5>
                            <div class="mb-4">
                                <h6>Descripción</h6>
                                <p>{{ $oportunidad->OportDescripcion ?: 'Sin descripción' }}</p>
                            </div>
                            <div>
                                <h6>Notas Internas</h6>
                                <p>{{ $oportunidad->OportNotas ?: 'Sin notas' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interacciones relacionadas -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Interacciones</h5>
                    <a href="{{ route('crm.interacciones.create', ['oportunidad' => $oportunidad->ID_Oportunidad]) }}" 
                        class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Interacción
                    </a>
                </div>
                <div class="card-body">
                    @if($oportunidad->interacciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Responsable</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($oportunidad->interacciones as $interaccion)
                                        <tr>
                                            <td>{{ $interaccion->InterFecha->format('d/m/Y H:i') }}</td>
                                            <td>{{ $interaccion->InterTipo }}</td>
                                            <td>{{ Str::limit($interaccion->InterDescripcion, 50) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $interaccion->InterEstado == 'Completado' ? 'success' : ($interaccion->InterEstado == 'Pendiente' ? 'warning' : 'danger') }}">
                                                    {{ $interaccion->InterEstado }}
                                                </span>
                                            </td>
                                            <td>{{ $interaccion->personal->PersFirstName }} {{ $interaccion->personal->PersLastName }}</td>
                                            <td>
                                                <a href="{{ route('crm.interacciones.show', $interaccion->ID_Interaccion) }}" 
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay interacciones registradas</p>
                    @endif
                </div>
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
</script>
@endpush 