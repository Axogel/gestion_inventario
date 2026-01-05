@extends('layouts.master')
@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
<style>
    .btn-responsive { width: 100%; max-width: 200px; }
    .table-status { font-weight: bold; text-transform: uppercase; font-size: 0.75rem; }
</style>
@endsection

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title">Inventario de Papelería</h4>
    </div>
</div>
@endsection

@section('content')
@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success.message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div class="form-group mb-0">
                    <label class="form-label">Buscar Producto:</label>
                    <input type="text" id="search" class="form-control" placeholder="Nombre o código...">
                </div>
                
                <div class="d-flex flex-wrap" style="gap: 10px;">
                    <form action="{{ route('exportInventario') }}" method="POST">
                        @csrf
                        <input type="hidden" name="searchTerm" id="searchTerm">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fa fa-file-excel-o"></i> Exportar
                        </button>
                    </form>
                    
                    <button class="btn btn-secondary" data-target="#modaldemo1" data-toggle="modal">
                        <i class="fa fa-upload"></i> Importar Excel
                    </button>
                    
                    <a class="btn btn-primary" href="{{ route('inventario.create') }}">
                        <i class="fa fa-plus"></i> Nuevo Producto
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-bordered text-nowrap key-buttons">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Artículo</th>
                                <th>Precio (BS)</th>
                                <th>Costo</th>
                                <th>Ref. USD</th>
                                <th>Stock</th>
                                <th>Mayorista</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contentTable">
                            @forelse($inventario as $producto)
                            <tr class="producto-row {{ $producto->stock <= $producto->stock_min ? 'bg-warning-transparent' : '' }}">
                            <td>{{ $producto->id }}</td>    
                            <td>{{ $producto->codigo }}</td>
                                <td>
                                    <strong>{{ $producto->producto }}</strong>
                                    @if($producto->stock <= $producto->stock_min)
                                        <span class="badge badge-danger ml-2">Stock Bajo</span>
                                    @endif
                                </td>
                                <td>{{ number_format($producto->precio, 2) }}</td>
                                <td>{{ number_format($producto->costo, 2) }}</td>
                                <td class="text-success">${{ number_format($producto->usd_ref, 2) }}</td>
                                <td>{{ $producto->stock }}</td>
                                <td>{{ number_format($producto->columna2, 2) }}</td>
                                <td>
                                    <div class="btn-list">

                                        @if(Auth::user()->isSuper())
                                            {{-- Editar --}}
                                            <a href="{{ route('inventario.edit', ['id' => $producto->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            
                                            {{-- Eliminar con formulario único --}}
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem('{{ $producto->id }}')">
                                                <i class="fa fa-trash"></i>
                                            </button>

                                            <form id="delete-form-{{ $producto->id }}" action="{{ route('inventario.destroy', $producto->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay productos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Importar --}}
<div class="modal fade" id="modaldemo1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('inventario.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Cargar Inventario (Excel/CSV)</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="custom-file">
                        <input type="file" name="excel" class="custom-file-input" accept=".xlsx, .xls, .csv" required>
                        <label class="custom-file-label">Seleccionar archivo...</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Procesar Archivo</button>
                    <button class="btn btn-light" data-dismiss="modal" type="button">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Buscador en tiempo real mejorado
    document.getElementById('search').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.producto-row');
        document.getElementById("searchTerm").value = filter;

        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    // Función de borrado segura
    function deleteItem(id) {
        if (confirm("¿Seguro que deseas eliminar este producto del inventario?")) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection