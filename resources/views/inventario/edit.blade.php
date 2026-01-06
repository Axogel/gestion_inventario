@extends('layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
    <style>
        .input-group-text {
            width: 45px;
            justify-content: center;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: bold;
            color: #5c678f;
            margin-top: 20px;
            border-bottom: 1px solid #eef0f7;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .readonly-field {
            background-color: #f3f4f7 !important;
            opacity: 1;
        }
    </style>
@endsection

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Editar Producto: {{ $product->producto }}</h4>
        </div>
        <div class="page-rightheader ml-auto d-lg-flex d-none">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="d-flex"><span class="breadcrumb-icon">
                            Dashboard</span></a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventario.index') }}">Inventario</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Error:</strong> Revisa los datos ingresados.
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Modificar Información</div>
                </div>
                <div class="card-body">
                    {{-- Cambiamos el action para apuntar a UPDATE y usamos el ID o Código --}}
                    <form method="POST" action="{{ route('inventario.update', $product->id) }}" role="form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- COLUMNA IZQUIERDA: DATOS BÁSICOS --}}
                            <div class="col-md-6">
                                <div class="section-title">Datos Generales</div>

                                <label class="form-label">Código de Barras (No editable):</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-barcode"></i></div>
                                    </span>
                                    <input type="text" name="codigo" class="form-control readonly-field"
                                        value="{{ $product->codigo }}" readonly>
                                </div>

                                <label class="form-label">Descripción del Producto:</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-tag"></i></div>
                                    </span>
                                    <input type="text" name="producto" class="form-control"
                                        value="{{ old('producto', $product->producto) }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Stock Actual:</label>
                                        <input type="number" name="stock" class="form-control"
                                            value="{{ old('stock', $product->stock) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Mínimo (Alerta):</label>
                                        <input type="number" name="stock_min" class="form-control"
                                            value="{{ old('stock_min', $product->stock_min ?? 5) }}">
                                    </div>
                                </div>


                            </div>

                            {{-- COLUMNA DERECHA: PRECIOS --}}
                            <div class="col-md-6">
                                <div class="section-title">Precios (COP)</div>

                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Precio Venta::</label>
                                        <input type="number" step="0.01" name="precio" class="form-control"
                                            value="{{ old('precio', $product->precio) }}">
                                    </div>


                                </div>

                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-lg btn-primary px-5">
                                    <i class="fa fa-refresh mr-2"></i> Actualizar Producto
                                </button>
                                <a href="{{ route('inventario.index') }}" class="btn btn-lg btn-light px-5">Volver</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{URL::asset('assets/plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/select2.js')}}"></script>
@endsection