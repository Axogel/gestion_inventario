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
                                <th>EFECTIVO (COP)</th>
                                <th>EFECTIVO (USD)</th>
                                <th>PUNTO</th>
                                <th>PAGO MOVIL</th>
                                <th>BANCOLOMBIA</th>
                                <th>TRASNFERENCIA (BS)</th>

                                <th>Estado</th>
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
                                        ${{ number_format($box->final_usd, 2) }}
                                    </td>
                                    <td>
                                        ${{ number_format($box->final_bs_punto, 2) }}
                                    </td>
                                    <td>
                                        ${{ number_format($box->final_bs_pagom, 2) }}
                                    </td>
                                    <td>
                                        ${{ number_format($box->final_cop_banco, 2) }}
                                    </td>
                                    <td>
                                        ${{ number_format($box->final_bs_transfer, 2) }}
                                    </td>
                                    <td>
                                        @if($box->status == 'open')
                                            <span class="badge bg-success">Abierta</span>
                                        @else
                                            <span class="badge bg-secondary">Cerrada</span>
                                        @endif
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