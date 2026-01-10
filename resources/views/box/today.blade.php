@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="page-header">
            <h4 class="page-title">Control de Caja - {{ date('d-m-Y') }}</h4>
        </div>

        {{-- CASO 1: NO EXISTE CAJA DE HOY --}}
        @if(!$actuallyBox)
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fe fe-alert-circle display-4 text-primary"></i>
                    <h4 class="mt-3">La caja no ha sido iniciada</h4>
                    <p>Para registrar pagos hoy, primero debes abrir la caja con un monto inicial.</p>

                    <form action="{{ route('box.store') }}" method="POST" class="d-inline-block col-md-4">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text">COP $</span>
                            <input type="number" name="init" class="form-control" placeholder="Monto inicial" required>
                            <button class="btn btn-primary" type="submit">Abrir Caja</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- CASO 2: LA CAJA EXISTE --}}
        @else
    <div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <h6>Base Inicial</h6>
                <h3>${{ number_format($actuallyBox->init, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <h6>Ventas (Ingresos)</h6>
                <h3>+${{ number_format($totalCollected, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white text-center">
            <div class="card-body">
                <h6>Gastos (Egresos)</h6>
                <h3>-${{ number_format($totalExpenses, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white text-center">
            <div class="card-body">
                <h6>Debe haber en Caja</h6>
                <h3>${{ number_format($netTotal, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Pagos Registrados</h3>
                    @if($actuallyBox->status == 'open')
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#closeBoxModal">
                            Cerrar Caja
                        </button>
                    @else
                        <span class="badge bg-secondary">CAJA CERRADA</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Método</th>
                                <th>Moneda</th>
                                <th>Monto (Original)</th>
                                <th>Subtotal (COP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('h:i A') }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $payment->metodo }}</span></td>
                                    <td>{{ $payment->moneda }}</td>
                                    <td>{{ number_format($payment->monto_original, 2) }}</td>
                                    <td><strong>${{ number_format($payment->monto_base, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="closeBoxModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('box.close', $actuallyBox->id ?? 0) }}" method="POST" class="modal-content">
                @csrf
                @method('POST') {{-- Añade esto para forzar que Laravel lo reconozca --}}
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Caja del Día</h5>
                </div>
                <div class="modal-body">
                    <label>Monto Final Real en Efectivo/Caja:</label>
                    <input type="number" name="final" class="form-control" required>
                    <small class="text-muted">Ingresa el total que tienes físicamente para comparar con el sistema.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Cierre</button>
                </div>
            </form>
        </div>
    </div>
@endsection