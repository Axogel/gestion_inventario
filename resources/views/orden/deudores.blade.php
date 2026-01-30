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
                                    <button type="button" class="btn btn-success btn-sm btn-pagar" data-id="{{ $pago->id }}"
                                        data-orden="{{ $pago->orden_id }}" data-monto="{{ $pago->amount }}"
                                        data-currency="{{ $pago->currency }}"
                                        data-cliente="{{ $pago->orden->cliente->name ?? 'Sin cliente' }}">
                                        Pagar Deuda
                                    </button>
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

        <div class="modal fade" id="modalPagoDeuda" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content bg-white">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Pago de Deuda #<span id="txt-orden"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form-pago-deuda" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Cliente:</strong> <span id="txt-cliente"></span><br>
                                <strong>Monto Pendiente:</strong> <span id="txt-monto"></span>
                            </div>

                            <div class="form-group">
                                <label>Método de Pago Real</label>
                                <select name="method" class="form-control" required id="select-metodo">
                                    <option value="EFECTIVO" data-currency="COP">Efectivo (COP)</option>
                                    <option value="EFECTIVO" data-currency="USD">Efectivo (USD)</option>
                                    <option value="PAGOMOVIL" data-currency="Bs">Pago Móvil (Bs)</option>
                                    <option value="PUNTO" data-currency="Bs">Punto (Bs)</option>

                                    <option value="TRANSFERENCIA" data-currency="Bs">Transferencia (Bs)</option>
                                    <option value="TRANSFERENCIA" data-currency="COP">Transferencia (COP)</option>

                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Moneda de Pago</label>
                                        <input type="text" name="currency" id="input-currency" class="form-control"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tasa de Cambio</label>
                                        <input type="number" step="0.01" name="exchange_rate" id="input-tasa"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Monto Pagado (En la moneda seleccionada)</label>
                                <input type="number" step="0.01" name="amount" id="input-monto-pago" class="form-control"
                                    required>
                                <small class="text-muted">Debe cubrir el equivalente al monto adeudado.</small>
                            </div>
                            <div>
                                <p id="txt-debit"></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Confirmar Pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('js')
    <script>
        $(document).ready(function () {
            const tasas = @json($divisas);

            $('.btn-pagar').on('click', function () {
                const data = $(this).data();
                const actionUrl = "{{ route('orden.paidDebit', ':id') }}".replace(':id', data.id);

                $('#form-pago-deuda').attr('action', actionUrl);
                $('#txt-orden').text(data.orden);
                $('#txt-cliente').text(data.cliente);

                // Guardamos el monto base en un atributo oculto para no perder precisión con el texto
                $('#form-pago-deuda').data('monto-base', parseFloat(data.monto));
                $('#txt-monto').text(data.monto + ' ' + data.currency);

                $('#select-metodo').trigger('change');
                $('#modalPagoDeuda').modal('show');
            });

            function calculate() {
                const montoNumber = $('#form-pago-deuda').data('monto-base');
                const tasa = parseFloat($('#input-tasa').val()) || 1;

                // Calculamos el valor exacto y redondeamos hacia arriba a 2 decimales para la tolerancia
                // Multiplicamos por 100, redondeamos y dividimos por 100 para mantener 2 decimales limpios
                const debit = Math.ceil((montoNumber / tasa) * 100) / 100;

                // Sugerir el monto automáticamente en el input si está vacío
                if ($('#input-monto-pago').val() == "") {
                    $('#input-monto-pago').val(debit.toFixed(2));
                }

                const inputMonto = parseFloat($('#input-monto-pago').val()) || 0;

                // Tolerancia de 0.01 para comparaciones
                const diferencia = inputMonto - debit;

                if (diferencia < -0.009) {
                    $('#txt-debit').removeClass('text-success').addClass('text-danger')
                        .text(`Falta por pagar ${Math.abs(diferencia).toFixed(2)}`);
                } else if (diferencia > 0.009) {
                    $('#txt-debit').removeClass('text-danger').addClass('text-success')
                        .text(`Tiene que darle un vuelto de ${diferencia.toFixed(2)}`);
                } else {
                    $('#txt-debit').text('Monto exacto cubierto').removeClass('text-danger').addClass('text-success');
                }
            }

            // Escuchar cambios en el input de monto y tasa manualmente
            $('#input-monto-pago, #input-tasa').on('input', function () {
                calculate();
            });

            $('#select-metodo').on('change', function () {
                // Obtenemos la moneda desde el atributo data del option seleccionado
                const currency = $(this).find(':selected').data('currency');

                $('#input-currency').val(currency);

                // Buscar la tasa según la moneda detectada
                const tasaObj = tasas.find(t => t.name === currency);
                const tasa = tasaObj ? parseFloat(tasaObj.tasa) : 1;

                $('#input-tasa').val(tasa);

                // Limpiamos el monto para recalcular con la nueva tasa
                $('#input-monto-pago').val("");
                calculate();
            });

            // Manejar el envío del formulario para limpiar el método
            $('#form-pago-deuda').on('submit', function (e) {
                const selectMetodo = $('#select-metodo');
                const valorOriginal = selectMetodo.val(); // Ej: "EFECTIVOUSD"

                let metodoLimpio = 'EFECTIVO'; // Default

                if (valorOriginal.includes('TRANSFERENCIA')) {
                    metodoLimpio = 'TRANSFERENCIA';
                } else if (valorOriginal.includes('PAGOMOVIL')) {
                    metodoLimpio = 'PAGOMOVIL';
                } else if (valorOriginal.includes('EFECTIVO')) {
                    metodoLimpio = 'EFECTIVO';
                } else if (valorOriginal.includes('PUNTO')) {
                    metodoLimpio = 'PUNTO'
                }

                // Cambiamos el valor del select justo antes de que viaje al backend
                // pero primero nos aseguramos de que el backend lo reciba agregando un campo oculto 
                // o cambiando el valor del select (esta es la forma más rápida)
                selectMetodo.append(`<option value="${metodoLimpio}" selected>${metodoLimpio}</option>`);
                selectMetodo.val(metodoLimpio);
            });
        });

        $(document).ready(function () {
            // Forzar cierre al hacer clic en cualquier elemento con data-dismiss="modal"
            $('[data-dismiss="modal"]').on('click', function () {
                $('#modalPagoDeuda').modal('hide');
                // $('#modalEditarPagos').modal('hide');
            });
        });
    </script>
@endsection