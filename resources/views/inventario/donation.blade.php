@extends('layouts.master')

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Agregar Productos</h4>
        </div>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('donation.store') }}">
                @csrf

                {{-- BUSCADOR --}}
                <div class="mb-3 position-relative">
                    <label>Buscar producto (código, nombre o ID)</label>
                    <input type="text" id="product-search" class="form-control" placeholder="Ej: lapicero, 74502">

                    <div id="product-results" class="list-group position-absolute w-100"
                        style="z-index:1000; display:none; max-height:250px; overflow:auto;">
                    </div>
                </div>

                {{-- TABLA --}}
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

                <button type="button" class="btn btn-success btn-sm mb-3" id="add-product">
                    + Agregar producto
                </button>

                <h4 class="mt-3">
                    Total: <strong id="total">$0.00</strong>
                </h4>

                <button type="submit" class="btn btn-primary mt-3">
                    Guardar
                </button>

            </form>
        </div>
    </div>

@endsection

@section('js')
    <script>

        let products = @json($products);
        let rowIndex = 0;

        /* ================= BUSCADOR ================= */
        $('#product-search').on('input', function () {
            const query = $(this).val().toLowerCase().trim();
            const box = $('#product-results');
            box.empty();

            if (query.length < 1) {
                box.hide();
                return;
            }

            const matches = products.filter(p => {
                const id = String(p.id);
                const codigo = (p.codigo ?? '').toString().toLowerCase();
                const producto = (p.producto ?? '').toLowerCase();

                return (
                    id.includes(query) ||
                    codigo.includes(query) ||
                    producto.includes(query)
                );
            });

            if (!matches.length) {
                box.hide();
                return;
            }

            matches.slice(0, 10).forEach(p => {
                box.append(`
                    <button type="button"
                        class="list-group-item list-group-item-action product-item"
                        data-id="${p.id}">
                        <strong>ID ${p.id}</strong>
                        ${p.codigo ? ` - ${p.codigo}` : ''}
                        - ${p.producto}
                        <span class="float-end">$${Number(p.precio).toFixed(2)}</span>
                    </button>
                `);
            });

            box.show();
        });

        $(document).on('click', '.product-item', function () {
            const id = $(this).data('id');
            const product = products.find(p => p.id == id);
            if (product) addRow(product);

            $('#product-search').val('');
            $('#product-results').hide();
        });

        /* ================= TABLA ================= */

        function addRow(product) {

            let exists = $(`.product-id[value="${product.id}"]`);
            if (exists.length) {
                let qty = exists.closest('tr').find('.cantidad');
                qty.val(parseInt(qty.val()) + 1).trigger('change');
                return;
            }

            let row = `
            <tr data-price="${product.precio}">
                <td>
                    <input type="hidden" name="items[${rowIndex}][type]" value="PRODUCT">

                    <input type="hidden"
                           class="product-id"
                           name="items[${rowIndex}][product_id]"
                           value="${product.id}">

                    <input type="hidden"
                           name="items[${rowIndex}][unit_price]"
                           value="${product.precio}">

                    <strong>${product.id}</strong>
                    ${product.codigo ? ` - ${product.codigo}` : ''}
                    - ${product.producto}
                </td>

                <td>
                    <input type="number"
                        name="items[${rowIndex}][cantidad]"
                        class="form-control cantidad"
                        value="1"
                        min="1"
                        max="${product.stock}">
                </td>

                <td>$${Number(product.precio).toFixed(2)}</td>
                <td class="subtotal">$${Number(product.precio).toFixed(2)}</td>

                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                </td>
            </tr>
        `;

            $('#products-table tbody').append(row);
            rowIndex++;
            calculateTotal();
        }

        $(document).on('change keyup', '.cantidad', function () {
            let row = $(this).closest('tr');
            let price = parseFloat(row.data('price')) || 0;
            let qty = parseFloat($(this).val()) || 0;

            let subtotal = price * qty;
            row.find('.subtotal').text('$' + subtotal.toFixed(2));
            calculateTotal();
        });

        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        /* ================= TOTAL ================= */

        function calculateTotal() {
            let total = 0;
            $('.subtotal').each(function () {
                total += parseFloat($(this).text().replace('$', '')) || 0;
            });
            $('#total').text('$' + total.toFixed(2));
        }

    </script>
@endsection