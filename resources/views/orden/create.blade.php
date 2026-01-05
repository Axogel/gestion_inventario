@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title">Crear Orden de Entrega</h4>
    </div>
</div>
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('orden.store') }}">
            @csrf

{{-- Cliente --}}
<div class="mb-3">
    <label>Cliente</label>
    <select name="client_id" id="client_id" class="form-control select2">
        <option value="">Cliente no registrado</option>
        @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}">
                {{ $cliente->cedula }} - {{ $cliente->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="new-client-check">
    <label class="form-check-label">
        Cliente nuevo
    </label>
</div>


<div id="new-client-form" style="display:none">
    <h5>Datos del Cliente</h5>

    <div class="mb-2">
        <input type="text" name="new_client[name]" class="form-control" placeholder="Nombre completo">
    </div>

    <div class="mb-2">
        <input type="date" name="new_client[fecha_nacimiento]" class="form-control">
    </div>

    <div class="mb-2">
        <input type="text" name="new_client[telefono]" class="form-control" placeholder="Teléfono">
    </div>

    <div class="mb-2">
        <input type="email" name="new_client[correo]" class="form-control" placeholder="Correo">
    </div>

    <div class="mb-2">
        <input type="text" name="new_client[direccion]" class="form-control" placeholder="Dirección">
    </div>

    <div class="mb-2">
        <input type="text" name="new_client[cedula]" class="form-control" placeholder="Cédula">
    </div>
</div>


            {{-- Método de pago --}}
            <div class="mb-3">
                <label>Método de Pago</label>
                <select name="method" class="form-control">
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TRANSFERENCIA">Transferencia</option>
                    <option value="PAGO_MOVIL">Pago móvil</option>
                </select>
            </div>
<div class="mb-3">
    <label>Agregar producto por código o ID</label>
    <input type="text" id="product-search" class="form-control"
           placeholder="Ej: ABC123 o 15">
</div>

            {{-- Productos --}}
            <h5>Productos</h5>

            <table class="table table-bordered" id="products-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th width="120">Cantidad</th>
                        <th width="120">Precio</th>
                        <th width="120">Subtotal</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button" class="btn btn-sm btn-success mb-3" id="add-product">
                + Agregar producto
            </button>

            <input type="hidden" name="subtotal" id="subtotal">

            <h4>Total: <span id="total">$0.00</span></h4>

            <button type="submit" class="btn btn-primary mt-3">
                Crear Orden
            </button>

            <a href="{{ route('orden.index') }}" class="btn btn-danger mt-3">
                Cancelar
            </a>

        </form>

    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    
$(document).ready(function () {
    $('.select2').select2();
});

let products = @json($products);
let rowIndex = 0;
$('#product-search').on('keypress', function (e) {
    if (e.which === 13) {
        e.preventDefault();

        let value = $(this).val().trim();
        if (!value) return;

        let product = products.find(p =>
            p.codigo === value || p.id == value
        );

        if (!product) {
            alert('Producto no encontrado');
            return;
        }

        addProductRow(product);
        $(this).val('');
    }
});

function addProductRow(product) {
    // si ya existe, suma cantidad
    let existing = $(`select[value="${product.id}"]`);
    if (existing.length) {
        let qtyInput = existing.closest('tr').find('.cantidad');
        qtyInput.val(parseInt(qtyInput.val()) + 1).trigger('keyup');
        return;
    }

    let row = `
    <tr>
        <td>
            <input type="hidden" name="products[${rowIndex}][product_id]" value="${product.id}">
            ${product.codigo} - ${product.producto}
        </td>
        <td>
            <input type="number" name="products[${rowIndex}][cantidad]"
                   class="form-control cantidad" value="1" min="1">
        </td>
        <td class="precio">$${product.precio}</td>
        <td class="subtotal">$${product.precio}</td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
        </td>
    </tr>
    `;

    $('#products-table tbody').append(row);
    rowIndex++;
    calculateTotal();
}


$('#add-product').on('click', function () {
    let row = `
    <tr>
        <td>
            <select name="products[${rowIndex}][product_id]" class="form-control product-select">
                <option value="">Seleccione</option>
                ${products.map(p =>
                    `<option value="${p.id}" data-price="${p.precio}">
                        ${p.producto}
                    </option>`
                ).join('')}
            </select>
        </td>
        <td>
            <input type="number" name="products[${rowIndex}][cantidad]" 
                   class="form-control cantidad" value="1" min="1">
        </td>
        <td class="precio">$0.00</td>
        <td class="subtotal">$0.00</td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
        </td>
    </tr>
    `;

    $('#products-table tbody').append(row);
    rowIndex++;
});

$(document).on('change keyup', '.product-select, .cantidad', function () {
    let row = $(this).closest('tr');
    let price = row.find('.product-select option:selected').data('price') || 0;
    let qty = row.find('.cantidad').val() || 0;

    let subtotal = price * qty;

    row.find('.precio').text('$' + price);
    row.find('.subtotal').text('$' + subtotal.toFixed(2));

    calculateTotal();
});

$(document).on('click', '.remove-row', function () {
    $(this).closest('tr').remove();
    calculateTotal();
});
$('#new-client-check').on('change', function () {
    if ($(this).is(':checked')) {
        $('#new-client-form').slideDown();
        $('#client_id').val('').trigger('change');
    } else {
        $('#new-client-form').slideUp();
    }
});

function calculateTotal() {
    let total = 0;
    $('.subtotal').each(function () {
        total += parseFloat($(this).text().replace('$', '')) || 0;
    });

    $('#total').text('$' + total.toFixed(2));
    $('#subtotal').val(total.toFixed(2));
}
</script>
@endsection
