@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4 class="page-title">Historial de Cajas</h4>
            <a href="{{ route('box.create') }}" class="btn btn-primary">Ir a Caja de Hoy</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter text-nowrap table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Monto Inicial</th>
                                <th>Monto Final</th>
                                <th>Estado</th>
                                <th>Diferencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boxes as $box)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($box->date)->format('d/m/Y') }}</td>
                                    <td>${{ number_format($box->init, 2) }}</td>
                                    <td>
                                        @if($box->status == 'open')
                                            <span class="text-muted italic">En curso...</span>
                                        @else
                                            ${{ number_format($box->final, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($box->status == 'open')
                                            <span class="badge bg-success">Abierta</span>
                                        @else
                                            <span class="badge bg-secondary">Cerrada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($box->status == 'closed')
                                            @php
                                                // Esto es un cálculo rápido, lo ideal es tener 'difference' en la DB
                                                // Aquí asumo que quieres ver la diferencia respecto a lo que se guardó
                                                // Para un reporte real, deberías sumar los pagos de ese día específico
                                                $diff = $box->difference ?? 0; 
                                            @endphp

                                            @if($diff > 0)
                                                <span class="text-success font-weight-bold">+${{ number_format($diff, 2) }}</span>
                                            @elseif($diff < 0)
                                                <span class="text-danger font-weight-bold">-${{ number_format(abs($diff), 2) }}</span>
                                            @else
                                                <span class="text-muted">$0.00</span>
                                            @endif
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('box.show', $box->id) }}" class="btn btn-sm btn-info">
                                            <i class="fe fe-eye"></i> Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $boxes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection