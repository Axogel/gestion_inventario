@extends('layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
    <style>
        .badge-method {
            font-size: 0.8rem;
            padding: 5px 10px;
        }

        .table-vcenter td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Listado de Órdenes de Entrega</h4>
        </div>
        <div class="page-rightheader ml-auto">
            <div class="btn-group mb-0">
                <a class="btn btn-primary" href="{{route('orden.create')}}">
                    <i class="fa fa-plus mr-2"></i>Nueva Orden
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Historial de Ventas / Entregas</div>
                    <div class="card-options">
                        <input type="text" id="search" class="form-control form-control-sm"
                            placeholder="Buscar por cliente o ID...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ordenes-table" class="table table-bordered table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Método Pago</th>
                                    <th>Total (BS)</th>
                                    <th>Productos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenes as $orden)
                                    <tr class="orden-row">
                                        <td><strong>#{{$orden->id}}</strong></td>
                                        <td>{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($orden->cliente)
                                                {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                                <br><small class="text-muted">{{ $orden->cliente->telefono }}</small>
                                            @else
                                                <span class="text-muted">Consumidor Final</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="">
                                                {{ ($orden->method) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($orden->subtotal, 2, ',', '.') }}</strong></td>
                                        <td>
                                            <details>
                                                <summary class="text-primary cursor-pointer">{{ $orden->items->count() }} ítems
                                                </summary>
                                                <ul class="list-unstyled mt-2 small">
                                                    @foreach($orden->items as $item)
                                                        <li>
                                                            • {{ $item->producto->producto }} (x{{ $item->cantidad }})
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        </td>
                     
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No se encontraron órdenes registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const rows = document.querySelectorAll('.orden-row');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });
    </script>
@endsection