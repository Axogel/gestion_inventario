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

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Historial de Órdenes</h4>
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
    {{-- Filtro de Rango de Fechas --}}
    <div class="card mb-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle mr-2"></i> Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <form action="{{ route('orden.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Desde:</label>
                        <input type="date" name="desde" value="{{ $desde ?? date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hasta:</label>
                        <input type="date" name="hasta" value="{{ $hasta ?? date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fa fa-filter mr-2"></i>Filtrar Rango
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                <div class="card-header border-bottom-0">
                    <div class="card-title">Listado de Ventas / Entregas</div>
                    <div class="card-options">
                        <input type="text" id="search" class="form-control form-control-sm" placeholder="Buscar...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="ordenes-table" class="table table-bordered table-vcenter text-nowrap mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Método Pago</th>
                                    <th>Total (COP)</th>
                                    <th>Productos</th>
                                    <th class="text-center">Acciones</th>
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
                                            @else
                                                <span class="text-muted">Consumidor Final</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-light border">{{ $orden->method }}</span></td>
                                        <td class="text-right font-weight-bold">
                                            {{ number_format($orden->subtotal, 2, ',', '.') }}</td>
                                        <td>
                                            <details>
                                                <summary class="text-primary cursor-pointer">{{ $orden->items->count() }} ítems
                                                </summary>
                                                <ul class="list-unstyled mt-2 small">
                                                    @foreach($orden->items as $item)
                                                        <li>
                                                            •
                                                            @if($item->type === 'PRODUCT' && $item->producto)
                                                                {{ $item->producto->producto }}
                                                            @elseif($item->type === 'SERVICE')
                                                                {{ $item->service->name }}
                                                            @else
                                                                Ítem desconocido
                                                            @endif
                                                            (x{{ $item->cantidad }})
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        </td>
                                        <td class="text-center">
                                            {{-- Botón de Devolución (DELETE) --}}
                                            <form action="{{ route('orden.destroy', $orden->id) }}" method="POST"
                                                class="d-inline form-devolucion">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-devolucion"
                                                    title="Procesar Devolución">
                                                    <i class="fa fa-undo mr-1"></i> Devolución
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-5">No se encontraron órdenes en este rango de
                                            fechas.</td>
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
    {{-- SweetAlert2 para confirmaciones más elegantes (opcional) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Buscador rápido
            const searchInput = document.getElementById('search');
            const rows = document.querySelectorAll('.orden-row');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Confirmación de Devolución
            $('.btn-devolucion').on('click', function (e) {
                const form = $(this).closest('form');

                Swal.fire({
                    title: '¿Procesar devolución?',
                    text: "Esta acción anulará la orden y devolverá los productos al inventario.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, anular orden',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection