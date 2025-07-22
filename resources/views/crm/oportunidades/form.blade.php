@csrf

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="OportName" class="form-label">Nombre de la Oportunidad *</label>
            <input type="text" 
                class="form-control @error('OportName') is-invalid @enderror" 
                id="OportName" 
                name="OportName" 
                value="{{ old('OportName', $oportunidad->OportName ?? '') }}" 
                required>
            @error('OportName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="FK_OportCliente" class="form-label">Cliente *</label>
            <select class="form-control @error('FK_OportCliente') is-invalid @enderror" 
                id="FK_OportCliente" 
                name="FK_OportCliente" 
                required>
                <option value="">Seleccione un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->ID_Cli }}" 
                        {{ old('FK_OportCliente', $oportunidad->FK_OportCliente ?? '') == $cliente->ID_Cli ? 'selected' : '' }}>
                        {{ $cliente->CliName }}
                    </option>
                @endforeach
            </select>
            @error('FK_OportCliente')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="OportValor" class="form-label">Valor *</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" 
                    class="form-control @error('OportValor') is-invalid @enderror" 
                    id="OportValor" 
                    name="OportValor" 
                    value="{{ old('OportValor', $oportunidad->OportValor ?? '0') }}" 
                    step="0.01" 
                    min="0" 
                    required>
            </div>
            @error('OportValor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="OportEstado" class="form-label">Estado *</label>
            <select class="form-control @error('OportEstado') is-invalid @enderror" 
                id="OportEstado" 
                name="OportEstado" 
                required>
                @foreach(['Nuevo', 'Contactado', 'En Negociación', 'Propuesta Enviada', 'Ganado', 'Perdido'] as $estado)
                    <option value="{{ $estado }}" 
                        {{ old('OportEstado', $oportunidad->OportEstado ?? 'Nuevo') == $estado ? 'selected' : '' }}>
                        {{ $estado }}
                    </option>
                @endforeach
            </select>
            @error('OportEstado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="FK_OportComercial" class="form-label">Comercial *</label>
            <select class="form-control @error('FK_OportComercial') is-invalid @enderror" 
                id="FK_OportComercial" 
                name="FK_OportComercial" 
                required>
                <option value="">Seleccione un comercial</option>
                @foreach($comerciales as $comercial)
                    <option value="{{ $comercial->ID_Pers }}" 
                        {{ old('FK_OportComercial', $oportunidad->FK_OportComercial ?? '') == $comercial->ID_Pers ? 'selected' : '' }}>
                        {{ $comercial->PersFirstName }} {{ $comercial->PersLastName }}
                    </option>
                @endforeach
            </select>
            @error('FK_OportComercial')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="OportFechaCierre" class="form-label">Fecha de Cierre</label>
            <input type="date" 
                class="form-control @error('OportFechaCierre') is-invalid @enderror" 
                id="OportFechaCierre" 
                name="OportFechaCierre" 
                value="{{ old('OportFechaCierre', $oportunidad->OportFechaCierre ? $oportunidad->OportFechaCierre->format('Y-m-d') : '') }}">
            @error('OportFechaCierre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-group mb-3">
            <label for="OportFuente" class="form-label">Fuente *</label>
            <select class="form-control @error('OportFuente') is-invalid @enderror" 
                id="OportFuente" 
                name="OportFuente" 
                required>
                @foreach(['Referido', 'Web', 'Llamada', 'Email', 'Redes Sociales', 'Otro'] as $fuente)
                    <option value="{{ $fuente }}" 
                        {{ old('OportFuente', $oportunidad->OportFuente ?? '') == $fuente ? 'selected' : '' }}>
                        {{ $fuente }}
                    </option>
                @endforeach
            </select>
            @error('OportFuente')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="OportDescripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('OportDescripcion') is-invalid @enderror" 
                id="OportDescripcion" 
                name="OportDescripcion" 
                rows="3">{{ old('OportDescripcion', $oportunidad->OportDescripcion ?? '') }}</textarea>
            @error('OportDescripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="OportNotas" class="form-label">Notas Internas</label>
            <textarea class="form-control @error('OportNotas') is-invalid @enderror" 
                id="OportNotas" 
                name="OportNotas" 
                rows="3">{{ old('OportNotas', $oportunidad->OportNotas ?? '') }}</textarea>
            @error('OportNotas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-3">
    <a href="{{ route('crm.oportunidades.index') }}" class="btn btn-secondary me-2">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        {{ isset($oportunidad) ? 'Actualizar' : 'Crear' }} Oportunidad
    </button>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar select2 para los dropdowns
    $('#FK_OportCliente, #FK_OportComercial').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
@endpush 