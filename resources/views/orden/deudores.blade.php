@extends('layouts.master')

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Órdenes Fiadas (Deudores)</h4>
        </div>
    </div>
@endsection

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            @if($orderFiadas->isEmpty())
                <div class="alert alert-info">
                    No hay órdenes fiadas pendientes 🎉
                </div>
            @else
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th># Orden</th>
                            <th>Cliente</th>
                            <th>Monto Adeudado</th>
                            <th>Método</th>
                            <th>Fecha</th>
                            <th width="120">Acción</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($orderFiadas as $pago)
                            <tr>
                                <td>#{{ $pago->orden->id }}</td>

                                <td>
                                    @if($pago->orden->cliente)
                                        {{ $pago->orden->cliente->name }} <br>
                                        <small class="text-muted">
                                            {{ $pago->orden->cliente->cedula }}
                                        </small>
                                    @else
                                        <span class="text-danger">Sin cliente</span>
                                    @endif
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($pago->amount, 2) }} {{ $pago->currency }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $pago->method }}
                                </td>

                                <td>
                                    {{ $pago->created_at->format('d/m/Y') }}
                                </td>

                                <td>
                                    <form method="POST" action="{{ route('orden.paidDebit', $pago->id) }}"
                                        onsubmit="return confirm('¿Marcar esta deuda como pagada?')">
                                        @csrf
                                        @method('PUT')

                                        <button class="btn btn-success btn-sm">
                                            Marcar pagado
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
<tfoot>
<tr>
    <th colspan="2">TOTAL ADEUDADO</th>
    <th>
        {{ number_format($orderFiadas->sum('amount'), 2) }} COP
    </th>
    <th colspan="3"></th>
</tr>
</tfoot>

                    </tbody>
                </table>
            @endif

        </div>
    </div>

@endsection