@extends('crm.layout')

@section('crm-content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Editar Oportunidad</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('crm.oportunidades.update', $oportunidad->ID_Oportunidad) }}" method="POST">
                        @method('PUT')
                        @include('crm.oportunidades.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 