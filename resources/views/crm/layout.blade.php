@extends('layouts.app')

@section('title', 'CRM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('crm/dashboard*') ? 'active' : '' }}" href="{{ route('crm.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('crm/oportunidades*') ? 'active' : '' }}" href="{{ route('crm.oportunidades.index') }}">
                            <i class="fas fa-chart-line"></i>
                            Oportunidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('crm/interacciones*') ? 'active' : '' }}" href="{{ route('crm.interacciones.index') }}">
                            <i class="fas fa-comments"></i>
                            Interacciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clientes.index') }}">
                            <i class="fas fa-users"></i>
                            Clientes
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('crm-content')
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
}

.sidebar .nav-link.active {
    color: #2470dc;
}

.sidebar .nav-link:hover {
    color: #2470dc;
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
}

main {
    padding-top: 48px;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: static;
        padding-top: 0;
    }
}
</style>
@endpush 