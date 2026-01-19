@extends('layouts.master')

@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
    <style>
        .badge-method {
            font-size: 0.8rem;
            padding: 5px 10px;
        }

        .table-vcenter td {
            vertical-align: middle !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }
        .swal2-container {
    z-index: 20000 !important;
}
    </style>
@endsection

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Historial de Órdenes</h4>
        </div>
        <div class="page-rightheader ml-auto">
            <div class="btn-group mb-0">
                <a class="btn btn-primary" href="{{route('orden.create')}}">
                    <i class="fa fa-plus mr-2"></i>Nueva Orden
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    {{-- Filtro de Rango de Fechas --}}
    <div class="card mb-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle mr-2"></i> Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <form action="{{ route('orden.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Desde:</label>
                        <input type="date" name="desde" value="{{ $desde ?? date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hasta:</label>
                        <input type="date" name="hasta" value="{{ $hasta ?? date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fa fa-filter mr-2"></i>Filtrar Rango
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                <div class="card-header border-bottom-0">
                    <div class="card-title">Listado de Ventas / Entregas</div>
                    <div class="card-options">
                        <input type="text" id="search" class="form-control form-control-sm" placeholder="Buscar...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="ordenes-table" class="table table-bordered table-vcenter text-nowrap mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Método Pago</th>
                                    <th>Total (COP)</th>
                                    <th>Productos</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenes as $orden)
                                    <tr class="orden-row">
                                        <td><strong>#{{$orden->id}}</strong></td>
                                        <td>{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                            
                                            @if($orden->cliente)
                                                {{ $orden->cliente->name }} {{ $orden->cliente->telefono }}
                                            @else
                                                <span class="text-muted">Consumidor Final</span>
                                            @endif
                                        </td>
<td>
    @foreach($orden->pagos as $pago)
        <span class="badge badge-light border me-1">
            {{ $pago->method }}  
        </span>
        {{ $pago->amount  }}{{ $pago->currency }}
    @endforeach
</td>
                                        <td class="text-right font-weight-bold">
                                       
                                            {{ number_format($orden->subtotal, 2, ',', '.') }}</td>
                                        <td>
                                            <details>
                                                <summary class="text-primary cursor-pointer">{{ $orden->items->count() }} ítems
                                                </summary>
                                                <ul class="list-unstyled mt-2 small">
                                                    @foreach($orden->items as $item)
                                                        <li>
                                                            •
                                                            @if($item->type === 'PRODUCT' && $item->producto)
                                                                {{ $item->producto->producto }}
                                                            @elseif($item->type === 'SERVICE')
                                                                {{ $item->service->name }}
                                                            @else
                                                                Ítem desconocido
                                                            @endif
                                                            (x{{ $item->cantidad }})
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        </td>
                                        <td class="text-center">
<button 
    type="button"
    class="btn btn-sm btn-outline-danger btn-devolucion"
    data-orden='@json($orden)'
>
    <i class="fa fa-undo mr-1"></i> Devolución
</button>

                    
                                            {{-- Botón de Factura --}}
                                            <a href="{{ route('orden.print', $orden->id) }}"
                                                class="btn btn-sm btn-outline-primary btn-factura" title="Factura">
                                                <i class="fa fa-file-invoice mr-1"></i> Factura
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-5">No se encontraron órdenes en este rango de
                                            fechas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalDevolucion" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Devolución de productos</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
 <form id="form-devolucion">
        @csrf
        <input type="hidden" name="orden_id" id="orden_id">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="devolucion-items"></tbody>
                </table>

                <div class="alert alert-info">
                    <strong>Total seleccionado:</strong>
                    <span id="total-devolucion">0</span>
                </div>

                <div class="form-group">
                    <label>Acción</label>
                    <select id="accionDevolucion" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="refund">Devolver dinero</option>
                        <option value="change">Cambiar por otro producto</option>
                    </select>
                </div>

                <div id="bloqueCambio" class="d-none">
      <div class="form-group mt-3 position-relative">
    <label>Buscar producto para cambio</label>
    <input type="text" id="product-search-cambio" class="form-control"
        placeholder="ID o nombre del producto">

    <div id="product-results-cambio"
        class="list-group position-absolute w-100"
        style="z-index:1055; display:none; max-height:200px; overflow:auto;">
    </div>
</div>

                    <div id="bloqueDiferencia" class="alert alert-warning d-none">
    <strong>Diferencia a pagar:</strong>
    <span id="monto-diferencia">0</span>
</div>

    <div id="bloqueCobrar" class="alert alert-warning d-none">
    <strong>Diferencia a Cobrarle al cliente :</strong>
    <span id="monto-cobrar">0</span>
</div>

<div id="bloquePagoExtra" class="d-none">
    <label>¿Con qué se va a pagar la diferencia?</label>
    <select id="metodoPagoExtra" class="form-control">
        <option value="">Seleccione</option>
        <option value="EFECTIVO">Efectivo COP</option>
        <option value="TRANSFERENCIA">Transferencia</option>
        <option value="PAGOMOVIL">Pago Móvil</option>
        <option value="EFECTIVOUSD">Efectivo USD</option>
    </select>
</div>
<table class="table table-sm mt-3">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="productos-cambio"></tbody>
</table>

                </div>

                <div id="bloqueDinero" class="d-none">
                    <label>Con que se va a pagar la devolucion</label>
                    <select id="dineroDevolucion" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="EFECTIVO">Efectivo COP</option>
                        <option value="TRANSFERENCIA">Pago Movil </option>
                        <option value="EFECTIVOUSD">Efectivo USD</option>
                        <option value="TRANSFERENCIACOP">Transferencia COP</option>
                    </select>
                </div>
    </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="confirmarDevolucion">Confirmar</button>
            </div>

        </div>
    </div>
</div>

    </div>
@endsection

@section('js')
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
    <script>
let ordenSeleccionada = null;
let totalDevolucion = 0;
let totalCambio = 0;
let selectedIndexCambio = -1;

const products= @json($products);
const divisas = @json($divisas);

const auxDvisas = divisas.reduce((acc, d) => {
    acc[d.name] = parseFloat(d.tasa);
    return acc;
}, {});
console.log(auxDvisas, 'auxDvisas')
$('#product-search-cambio').on('input', function () {
    const query = $(this).val().toLowerCase().trim();
    const resultsBox = $('#product-results-cambio');

    resultsBox.empty();
    selectedIndexCambio = -1;

    if (query.length < 1) {
        resultsBox.hide();
        return;
    }

    const matches = products.filter(p => {
        const id = String(p.id);
        const name = (p.producto ?? '').toLowerCase();
        return id.includes(query) || name.includes(query);
    });

    if (!matches.length) {
        resultsBox.hide();
        return;
    }

    matches.slice(0, 10).forEach(p => {
        resultsBox.append(`
            <button type="button"
                class="list-group-item list-group-item-action product-item-cambio"
                data-id="${p.id}">
                <strong>ID ${p.id}</strong>
                - ${p.producto} (Stock: ${p.stock})
                <span class="float-end">$${Number(p.precio).toFixed(2)}</span>
            </button>
        `);
    });

    resultsBox.show();
});
$('.btn-devolucion').on('click', function () {
    ordenSeleccionada = $(this).data('orden');
    $('#orden_id').val(ordenSeleccionada.id);
    
    const tbody = $('#devolucion-items');
    tbody.empty();

    ordenSeleccionada.items.forEach(item => {
        console.log(item, 'item')
        // Convertimos el subtotal a número (muy importante)
        const subtotalNumeric = parseFloat(item.subtotal);
        const cantidadNumeric = parseInt(item.cantidad);
        
        // Calculamos el precio unitario
        const precioUnit = subtotalNumeric / cantidadNumeric;
        
        const productName = item.type === 'PRODUCT' 
            ? (item.producto?.producto ?? 'Producto sin nombre')
            : (item.service?.name ?? 'Servicio');
        if(item.type === "SERVICE") return;
        tbody.append(`
            <tr>
                <td>
                    <input type="checkbox" 
                        class="item-check"
                        data-cantidad="${cantidadNumeric}"
                        data-precio="${precioUnit}"
                        data-subtotal="${subtotalNumeric}"
                        data-total="${subtotalNumeric}"
                        data-product-id="${item.product_id || ''}"
                        data-type="${item.type}">
                </td>
                <td>${productName}</td>
                <td>${cantidadNumeric}</td>
                <td>${precioUnit.toFixed(2)}</td>
                <td>${subtotalNumeric.toFixed(2)}</td> 
            </tr>
        `);
    });

    // Resetear estado
    totalDevolucion = 0;
    totalCambio = 0;
    $('#total-devolucion').text('0.00');
    $('#accionDevolucion').val('').trigger('change');
    $('#productos-cambio').empty();
    
    $('#modalDevolucion').modal('show');
});

$(document).ready(function() {
    // Forzar cierre al hacer clic en cualquier elemento con data-dismiss="modal"
    $('[data-dismiss="modal"]').on('click', function() {
        $('#modalDevolucion').modal('hide');
    });
});


$(document).on('click', '.product-item-cambio', function () {
    const id = $(this).data('id');
    const product = products.find(p => p.id == id);

    if (product) {
        addProductCambio(product);
    }

    $('#product-search-cambio').val('');
    $('#product-results-cambio').hide();
});

$('#product-search-cambio').on('keydown', function (e) {
    const items = $('.product-item-cambio');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        selectedIndexCambio = (selectedIndexCambio + 1) % items.length;
        items.removeClass('active');
        items.eq(selectedIndexCambio).addClass('active');
        e.preventDefault();
    }

    if (e.key === 'ArrowUp') {
        selectedIndexCambio =
            (selectedIndexCambio - 1 + items.length) % items.length;
        items.removeClass('active');
        items.eq(selectedIndexCambio).addClass('active');
        e.preventDefault();
    }

    if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndexCambio >= 0) {
            items.eq(selectedIndexCambio).click();
        }
    }
});


$(document).on('change', '.item-check', function () {
    let total = 0;
    $('.item-check:checked').each(function () {
        total += parseFloat($(this).data('total'));
    });
    totalDevolucion = total;

    $('#total-devolucion').text(total.toFixed(2));
});

$('#accionDevolucion').on('change', function () {
    const action = this.value;

    $('#bloqueCambio').toggleClass('d-none', action !== 'change');
    $('#bloqueDinero').toggleClass('d-none', action !== 'refund');

    totalCambio = 0;
    $('#productos-cambio').empty();
    recalcularDiferencia();
});

$('#metodoPagoExtra').on('change', function () {
    recalcularDiferencia();
});
function recalcularDiferencia() {
    const diferencia = totalDevolucion - totalCambio;
    
    const metodoPago = $('#metodoPagoExtra').val();
    const mapDivisas = {
        'PAGOMOVIL': 'Bs',
        'TRANSFERENCIA': 'Bs', 
        'EFECTIVO': 'COP',
        'EFECTIVOUSD': 'USD',
        'TRANSFERENCIACOP': 'COP'
    };
    
    const divisaCal = mapDivisas[metodoPago] || 'COP';
    const tasa = auxDvisas[divisaCal] || 1;
    const montoDivisa = diferencia / tasa;

    // Ocultar todos los bloques
    $('#bloqueDiferencia, #bloqueCobrar, #bloquePagoExtra').addClass('d-none');

    if (diferencia > 0) {
        // Cliente recibe dinero de vuelta
        $('#bloqueDiferencia').removeClass('d-none');
        $('#bloquePagoExtra').removeClass('d-none');
        $('#monto-diferencia').text(`${montoDivisa.toFixed(2)} ${divisaCal}`);
    } else if (diferencia < 0) {
        // Cliente debe pagar más
        $('#bloqueCobrar').removeClass('d-none');
        $('#bloquePagoExtra').removeClass('d-none');
        $('#monto-cobrar').text(`${Math.abs(montoDivisa).toFixed(2)} ${divisaCal}`);
    }
}
function addProductCambio(product) {
    // ✅ Validar stock
    if (product.stock < 1) {
        Swal.fire('Sin stock', `El producto ${product.producto} no tiene stock disponible`, 'error');
        return;
    }

    // ✅ Evitar duplicados
    const existing = $('#productos-cambio tr[data-id="' + product.id + '"]');
    if (existing.length) {
        Swal.fire('Duplicado', 'Este producto ya está agregado', 'warning');
        return;
    }

    const total = product.precio;

    $('#productos-cambio').append(`
        <tr data-id="${product.id}" data-precio="${product.precio}" data-total="${total}">
            <td>${product.producto}</td>
            <td>
                <input type="number" 
                    class="form-control form-control-sm cantidad-cambio" 
                    value="1" 
                    min="1" 
                    max="${product.stock}"
                    data-precio="${product.precio}">
            </td>
            <td>${product.precio.toFixed(2)}</td>
            <td class="total">${total.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-cambio">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `);

    totalCambio += total;
    recalcularDiferencia();
}

// ✅ Actualizar total al cambiar cantidad
$(document).on('input', '.cantidad-cambio', function() {
    const row = $(this).closest('tr');
    const cantidad = parseInt($(this).val()) || 1;
    const precio = parseFloat($(this).data('precio'));
    const nuevoTotal = cantidad * precio;
    
    // Actualizar total global
    const totalAnterior = parseFloat(row.data('total'));
    totalCambio = totalCambio - totalAnterior + nuevoTotal;
    
    // Actualizar row
    row.data('total', nuevoTotal);
    row.find('.total').text(nuevoTotal.toFixed(2));
    
    recalcularDiferencia();
});
$(document).on('click', '.remove-cambio', function () {
    const row = $(this).closest('tr');
    totalCambio -= parseFloat(row.data('total'));
    row.remove();
    recalcularDiferencia();
});

$('#confirmarDevolucion').on('click', function () {
    const accion = $('#accionDevolucion').val();

    // ✅ Validaciones
    if (!accion) {
        Swal.fire('Error', 'Seleccione una acción (devolver dinero o cambiar producto)', 'error');
        return;
    }

    const itemsSeleccionados = [];
    $('.item-check:checked').each(function() {
        itemsSeleccionados.push({
            id: $(this).data('id'),
            cantidad: $(this).data('cantidad'),
            precio: $(this).data('precio'),
            subtotal: $(this).data('subtotal'),
            product_id: $(this).data('product-id'),
            type: $(this).data('type')
        });
    });

    if (itemsSeleccionados.length === 0) {
        Swal.fire('Error', 'Debe seleccionar al menos un producto para devolver', 'error');
        return;
    }

    if (accion === 'refund') {
        const metodoDevolucion = $('#dineroDevolucion').val();
        if (!metodoDevolucion) {
            Swal.fire('Error', 'Debe seleccionar el método de devolución', 'error');
            return;
        }
    }

    if (accion === 'change') {
        const productosCambio = [];
        $('#productos-cambio tr').each(function() {
            productosCambio.push({
                id: $(this).data('id'),
                cantidad: $(this).find('.cantidad-cambio').val(),
                precio: $(this).data('precio')
            });
        });

        if (productosCambio.length === 0) {
            Swal.fire('Error', 'Debe agregar al menos un producto para el cambio', 'error');
            return;
        }

        // Si hay diferencia a pagar/cobrar
        const diferencia = totalDevolucion - totalCambio;
        if (diferencia !== 0 && !$('#metodoPagoExtra').val()) {
            Swal.fire('Error', 'Debe seleccionar el método de pago para la diferencia', 'error');
            return;
        }
    }

    // ✅ Construir payload
    const payload = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        orden_id: ordenSeleccionada.id,
        accion: accion,
        items_devolucion: itemsSeleccionados,
        total_devolucion: totalDevolucion
    };

    if (accion === 'refund') {
        payload.metodo_devolucion = $('#dineroDevolucion').val();
    }

    if (accion === 'change') {
        const productosCambio = [];
        $('#productos-cambio tr').each(function() {
            productosCambio.push({
                id: $(this).data('id'),
                cantidad: parseInt($(this).find('.cantidad-cambio').val()),
                precio: parseFloat($(this).data('precio'))
            });
        });

        payload.productos_cambio = productosCambio;
        payload.total_cambio = totalCambio;
        payload.diferencia = totalDevolucion - totalCambio;
        payload.metodo_pago_diferencia = $('#metodoPagoExtra').val();
    }

    // ✅ Enviar al backend
    Swal.fire({
        title: '¿Confirmar devolución?',
        html: `
            <p><strong>Acción:</strong> ${accion === 'refund' ? 'Devolver dinero' : 'Cambiar productos'}</p>
            <p><strong>Total devolución:</strong> $${totalDevolucion.toFixed(2)}</p>
            ${accion === 'change' ? `<p><strong>Total cambio:</strong> $${totalCambio.toFixed(2)}</p>` : ''}
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, procesar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/orden/${ordenSeleccionada.id}/return`,
                type: 'POST',
                data: payload,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Procesando...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Error al procesar la devolución';
                    Swal.fire('Error', error, 'error');
                }
            });
        }
    });
});


</script>

    {{-- SweetAlert2 para confirmaciones más elegantes (opcional) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>


        document.addEventListener('DOMContentLoaded', function () {
            // Buscador rápido
            const searchInput = document.getElementById('search');
            const rows = document.querySelectorAll('.orden-row');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

        });
    </script>
@endsection