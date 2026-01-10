@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="page-header d-flex justify-content-between">
            <h4 class="page-title">Gastos Operativos</h4>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                <i class="fe fe-plus"></i> Registrar Nuevo Gasto
            </button>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Moneda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($expense->fecha)->format('d/m/Y') }}</td>
                                <td><span class="badge bg-info">{{ $expense->categoria }}</span></td>
                                <td>{{ $expense->descripcion }}</td>
                                <td class="text-danger font-weight-bold">-${{ number_format($expense->monto, 2) }}</td>
                                <td>{{ $expense->moneda }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('expenses.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-control" required>
                            <option value="Insumos">Insumos</option>
                            <option value="Papelería">Papelería</option>
                            <option value="Almuerzo">Almuerzo</option>
                            <option value="Servicios">Servicios Públicos</option>
                            <option value="Nomina">Nomina</option>
                            <option value="Transporte">Transporte / Fletes</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" class="form-control"
                            placeholder="Ej: Compra de bolsas, Pago de luz..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Monto</label>
                                <input type="number" step="0.01" name="monto" class="form-control" placeholder="0.00"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Moneda</label>
                                <select name="moneda" class="form-control">
                                    <option value="COP">COP</option>
                                    <option value="USD">USD</option>
                                    <option value="BS">BS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
@endsection