@extends('layouts.master')

@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.css')}}" rel="stylesheet" />

    <style>
        .badge-input {
            background-color: #28a745;
        }

        .badge-output {
            background-color: #dc3545;
        }
    </style>
@endsection

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Movimientos de Inventario</h4>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card">

                {{-- FILTRO POR FECHA --}}
                <div class="card-header">
                    <form method="GET" action="{{ route('movement.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Desde</label>
                                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                            </div>

                            <div class="col-md-4">
                                <button class="btn btn-primary mt-4" type="submit">
                                    <i class="fa fa-search"></i> Buscar
                                </button>

                                <a href="{{ route('movement.index') }}" class="btn btn-light mt-4">
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABLA --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Motivo</th>
                                    <th>stock Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>{{ $movement->id }}</td>
                                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <strong>{{ $movement->description }}</strong><br>

                                        </td>
                                        <td>
                                            @if($movement->type === 'input')
                                                <span class="badge badge-input">ENTRADA</span>
                                            @else
                                                <span class="badge badge-output">SALIDA</span>
                                            @endif
                                        </td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td>{{ $movement->reason }}</td>
                                        <td>{{ $movement->balance_after ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            No hay movimientos registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINACIÓN --}}
                    <div class="mt-3">
                        {{ $movements->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection