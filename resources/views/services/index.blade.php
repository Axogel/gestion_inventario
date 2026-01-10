@extends('layouts.master')

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Gestión de Servicios</h4>
        </div>
        <div class="page-rightheader ml-auto">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">
                <i class="fa fa-plus mr-2"></i>Nuevo Servicio
            </button>
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
                    <div class="card-title">Listado de Servicios Ofrecidos</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th>Nombre del Servicio</th>
                                    <th width="15%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td><strong>#{{ $service->id }}</strong></td>
                                        <td>{{ $service->name }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-toggle="modal"
                                                data-target="#modalEdit{{ $service->id }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalEdit{{ $service->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('services.update', $service->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Servicio</h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Nombre del Servicio</label>
                                                            <input type="text" name="name" class="form-control"
                                                                value="{{ $service->name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Servicio</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del Servicio</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Laminado" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection