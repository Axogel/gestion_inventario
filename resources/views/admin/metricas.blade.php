@extends('layouts.master')

@section('content')
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6>Órdenes Totales</h6>
                        <h3>{{ $metricas['ordenes_totales'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6>Facturación Total</h6>
                        <h3>{{ number_format($metricas['facturacion_total'], 2, ',', '.') }} Bs</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6>Inventario Crítico</h6>
                        <h3>{{ $metricas['inventario_critico'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h6>Valor Inventario</h6>
                        <h3>{{ number_format($metricas['valor_inventario'], 2, ',', '.') }} Bs</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCTO MÁS VENDIDO --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong>Producto Más Vendido</strong>
            </div>
            <div class="card-body">
                @if($metricas['producto_mas_vendido'])
                    <h5>
                        {{ $metricas['producto_mas_vendido']->producto->producto }}
                    </h5>
                    <p>
                        Unidades vendidas:
                        <strong>{{ $metricas['producto_mas_vendido']->total_vendido }}</strong>
                    </p>
                @else
                    <p>No hay ventas registradas.</p>
                @endif
            </div>
        </div>

        {{-- TOP PRODUCTOS --}}
        <div class="card">
            <div class="card-header">
                <strong>Top 5 Productos Más Vendidos</strong>
            </div>
            <div class="card-body">
                @php
                    $max = $metricas['top_productos']->max('total') ?: 1;
                @endphp

                @foreach($metricas['top_productos'] as $item)
                    @php
                        $porcentaje = ($item->total / $max) * 100;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ $item->producto->producto }}</span>
                            <span>{{ $item->total }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $porcentaje }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection