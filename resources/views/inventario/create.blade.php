@extends('layouts.master')
@section('css')
<link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
<style>
    .input-group-text { width: 45px; justify-content: center; }
    .section-title { font-size: 0.9rem; font-weight: bold; color: #5c678f; margin-top: 20px; border-bottom: 1px solid #eef0f7; padding-bottom: 5px; margin-bottom: 15px; }
</style>
@endsection

@section('page-header')
                        <div class="page-header">
                            <div class="page-leftheader">
                                <h4 class="page-title">Inventario: Nuevo Producto</h4>
                            </div>
                            <div class="page-rightheader ml-auto d-lg-flex d-none">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="d-flex"><span class="breadcrumb-icon"> Dashboard</span></a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('inventario.index') }}">Inventario</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Crear</li>
                                </ol>
                            </div>
                        </div>
                        @endsection

@section('content')
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>¡Ups!</strong> Revisa los campos obligatorios:
                                <ul class="mb-0">
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
                                        <div class="card-title">Información del Producto</div>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('inventario.store') }}" role="form">
                                            @csrf
                                            
                                            <div class="row">
                                                {{-- DATOS BÁSICOS --}}
                                                <div class="col-md-6">
                                                    <div class="section-title">Datos Generales</div>
                                                    
                                                    <label class="form-label">Código de Barras / Interno:</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-prepend"><div class="input-group-text"><i class="fa fa-barcode"></i></div></span>
                                                        <input type="text" name="codigo" class="form-control" placeholder="Ej: 7591234..." value="{{ old('codigo') }}">
                                                    </div>

                                                    <label class="form-label">Descripción del Producto:</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-prepend"><div class="input-group-text"><i class="fa fa-tag"></i></div></span>
                                                        <input type="text" name="producto" class="form-control" placeholder="Ej: Cuaderno de dibujo" value="{{ old('producto') }}" required>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-6">
                                                            <label class="form-label">Stock Inicial:</label>
                                                            <input type="number" name="stock" class="form-control" placeholder="0" value="{{ old('stock', 0) }}">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Mínimo (Alerta):</label>
                                                            <input type="number" name="stock_min" class="form-control" placeholder="5" value="{{ old('stock_min', 5) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- PRECIOS Y COSTOS --}}
                                                <div class="col-md-6">
                                                    <div class="section-title">Precios (Venta en COP)</div>
                                                    
                                                    <div class="row">
                                           
                                                        <div class="col-6">
                                                            <label class="form-label">Precio Venta:</label>
                                                            <input type="number" step="0.01" name="precio" class="form-control" placeholder="0.00" value="{{ old('precio') }}">
                                                        </div>
                            
                                                    </div>
                         

                                                </div>
                                            </div>

                                            <div class="row mt-5">
                                                <div class="col-12 text-center">
                                                    <button type="submit" class="btn btn-lg btn-primary px-5">
                                                        <i class="fa fa-save mr-2"></i> Guardar Producto
                                                    </button>
                                                    <a href="{{ route('inventario.index') }}" class="btn btn-lg btn-light px-5">Cancelar</a>
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