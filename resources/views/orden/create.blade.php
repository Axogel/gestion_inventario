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



                <div class="mb-3 position-relative">
                    <label>Buscar producto (código, nombre o ID)</label>
                    <input type="text" id="product-search" class="form-control" placeholder="Ej: lapicero, 74502, agenda">

                    <div id="product-results" class="list-group position-absolute w-100"
                        style="z-index:1000; display:none; max-height:250px; overflow:auto;">
                    </div>
                </div>


                {{-- Productos --}}
                <h5>Productos</h5>

                <table class="table table-bordered" id="products-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th width="120">Cantidad</th>
                            <th width="120">Precio</th>
                            <th width="120">Subtotal Cop</th>
                            <th width="120">Subtotal Bs</th>
                            <th width="120">Subtotal USD</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="button" class="btn btn-sm btn-success mb-3" id="add-product">
                    + Agregar producto
                </button>
                {{-- Servicios --}}
                <h5>Servicios</h5>

                <table class="table table-bordered" id="services-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th width="120">Cantidad</th>
                            <th width="120">Precio</th>
                            <th width="120">Subtotal Cop</th>
                            <th width="120">Subtotal Bs</th>
                            <th width="120">Subtotal USD</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="button" class="btn btn-sm btn-success mb-3" id="add-service">
                    + Agregar Servicio
                </button>
                <input type="hidden" name="subtotal" id="subtotal">

                <h5>Métodos de Pago</h5>

                <table class="table table-bordered" id="payments-table">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Moneda</th>
                            <th>Monto</th>
                            <th>Tasa</th>
                            <th>Equivalente Base</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="button" class="btn btn-sm btn-secondary mb-3" id="add-payment">
                    + Agregar pago
                </button>

                <div class="alert alert-info">
                    Total pagado: <strong id="total-paid">0.00</strong>
                </div>

                <h4>
                    Total:
                    <br><br>
                    USD: <strong id="total-usd">$0.00</strong><br><br>
                    Bs: <strong id="total-bs">Bs 0.00</strong><br><br>
                    COP: <strong id="total">$0.00</strong>
                </h4>
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
        let services = @json($services);
        let selectedIndex = -1;
        let rowIndex = 0;


        let paymentIndex = 0;

        $('#add-payment').on('click', function () {

            let row = `
                                                                                                            <tr>
                                                                                                                <td>
                                                                                                                    <select name="payments[${paymentIndex}][method]" class="form-control">
                                                                                                                        <option value="EFECTIVO">Efectivo</option>
                                                                                                                        <option value="TRANSFERENCIA">Transferencia</option>
                                                                                                                        <option value="PAGO_MOVIL">Pago móvil</option>
                                                                                                                    </select>
                                                                                                                </td>

                                                                                                                <td>
                                                                                                                    <select name="payments[${paymentIndex}][currency]" 
                                                                                                                            class="form-control currency-select">
                                                                                                                        ${Object.keys(divisas).map(c =>
                `<option value="${c}">${c}</option>`
            ).join('')}
                                                                                                                    </select>
                                                                                                                </td>

                                                                                                                <td>
                                                                                                                    <input type="number"
                                                                                                                           step="0.01"
                                                                                                                           name="payments[${paymentIndex}][amount]"
                                                                                                                           class="form-control payment-amount"
                                                                                                                           value="0">
                                                                                                                </td>

                                                                                                                <td>
                                                                                                                    <input type="number"
                                                                                                                           step="0.0001"
                                                                                                                           name="payments[${paymentIndex}][exchange_rate]"
                                                                                                                           class="form-control exchange-rate"
                                                                                                                           value="1">
                                                                                                                </td>

                                                                                                                <td class="payment-base">0.00</td>

                                                                                                                <td>
                                                                                                                    <button type="button"
                                                                                                                            class="btn btn-danger btn-sm remove-payment">X</button>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        `;

            $('#payments-table tbody').append(row);
            paymentIndex++;
        });


        function calculatePayments() {
            let total = 0;

            $('#payments-table tbody tr').each(function () {

                let amount = parseFloat(
                    $(this).find('.payment-amount').val()
                ) || 0;

                let rate = parseFloat(
                    $(this).find('.exchange-rate').val()
                ) || 0;

                let base = amount * rate;

                $(this).find('.payment-base').text(base.toFixed(2));
                total += base;
            });

            $('#total-paid').text(total.toFixed(2));
        }

        $(document).on('keyup change', '.payment-amount, .exchange-rate', function () {
            calculatePayments();
        });

        $(document).on('click', '.remove-payment', function () {
            $(this).closest('tr').remove();
            calculatePayments();
        });
        $(document).on('change', '.currency-select', function () {
            let currency = $(this).val();
            let rateInput = $(this).closest('tr').find('.exchange-rate');

            if (divisas[currency]) {
                rateInput.val(divisas[currency]);
            }

            calculatePayments();
        });


        const divisasDb = @json($divisas);

        const divisas = divisasDb.reduce((acc, divisa) => {
            acc[divisa.name] = parseFloat(divisa.tasa);
            return acc;
        }, {});
        console.log(divisas, divisasDb);

        $('#product-search').on('input', function () {
            const query = $(this).val().toLowerCase().trim();
            const resultsBox = $('#product-results');

            resultsBox.empty();
            selectedIndex = -1;

            if (query.length < 2) {
                resultsBox.hide();
                return;
            }

            const matches = products.filter(p =>
                String(p.id).includes(query) ||
                p.codigo.toLowerCase().includes(query) ||
                p.producto.toLowerCase().includes(query)
            );

            if (!matches.length) {
                resultsBox.hide();
                return;
            }

            matches.slice(0, 10).forEach((p, index) => {
                resultsBox.append(`
                                                                                                                                        <button type="button"
                                                                                                                                            class="list-group-item list-group-item-action product-item"
                                                                                                                                            data-index="${index}">
                                                                                                                                            <strong>${p.codigo}</strong> - ${p.producto}
                                                                                                                                            <span class="float-end">$${p.precio}</span>
                                                                                                                                        </button>
                                                                                                                                    `);
            });

            resultsBox.show();
        });

        $(document).on('click', '.product-item', function () {
            const text = $(this).text();
            const code = text.split('-')[0].trim();

            const product = products.find(p => p.codigo === code);
            if (product) addProductRow(product);

            $('#product-search').val('');
            $('#product-results').hide();
        });
        $('#product-search').on('keydown', function (e) {
            const items = $('.product-item');

            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                selectedIndex = (selectedIndex + 1) % items.length;
                items.removeClass('active');
                items.eq(selectedIndex).addClass('active');
                e.preventDefault();
            }

            if (e.key === 'ArrowUp') {
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                items.removeClass('active');
                items.eq(selectedIndex).addClass('active');
                e.preventDefault();
            }

            if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0) {
                    items.eq(selectedIndex).click();
                }
            }
        });

        function addProductRow(product) {
            let existingRow = $(`input[value="${product.id}"]`);

            if (existingRow.length) {
                let qtyInput = existingRow.closest('tr').find('.cantidad');
                qtyInput.val(parseInt(qtyInput.val()) + 1).trigger('keyup');
                return;
            }

            let row = `
                                                                                                  <tr data-price="${product.precio}">
                                                                                                        <td>
                                                                                                            <input type="hidden" name="products[${rowIndex}][product_id]" value="${product.id}">
                                                                                                            <input type="hidden" name="products[${rowIndex}][type]" value="PRODUCT">
                                                                                                            <input type="hidden" name="products[${rowIndex}][unit_price]" value="${product.precio}">
                                                                                                            <input type="hidden"
                                                                                                                class="subtotal-input"
                                                                                                                name="products[${rowIndex}][subtotal]"
                                                                                                                value="${product.precio}">
                                                                                                            ${product.codigo} - ${product.producto}
                                                                                                        </td>
                                                                                                      <td>
                                                                                                          <input type="number"
                                                                                                              name="products[${rowIndex}][cantidad]"
                                                                                                              class="form-control cantidad"
                                                                                                              value="1"
                                                                                                              min="1"
                                                                                                              max="${product.stock}">
                                                                                                      </td>
                                                                                                      <td class="precio">$${product.precio.toFixed(2)}</td>
                                                                                                      <td class="subtotal">$${product.precio.toFixed(2)}</td>
                                                                                                      <td class="subtotal_bs">Bs${(product.precio / divisas['Bs']).toFixed(2)}</td>
                                                                                                      <td class="subtotal_usd">$${(product.precio / divisas['USD']).toFixed(2)}</td>

                                                                                                      <td>
                                                                                                          <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                                                                                      </td>
                                                                                                  </tr>
                                                                                            `;


            $('#products-table tbody').append(row);
            rowIndex++;
            calculateTotal();
        }



        function addServiceRow() {

            let row = `
                            <tr data-price="0">
                                <td>
                                    <select name="products[${rowIndex}][service_id]"
                                            class="form-control service-select">
                                        <option value="">Seleccione servicio</option>
                                        ${services.map(s => `
                                            <option value="${s.id}">
                                                ${s.name}
                                            </option>
                                        `).join('')}
                                    </select>

                                    <input type="hidden" name="products[${rowIndex}][type]" value="SERVICE">
                                    <input type="hidden"
                                           name="products[${rowIndex}][price]"
                                           class="unit-price-input"
                                           value="0">

                                    <input type="hidden"
                                           class="subtotal-input"
                                           name="products[${rowIndex}][subtotal]"
                                           value="0">
                                </td>

                                <td>
                                    <input type="number"
                                           name="products[${rowIndex}][cantidad]"
                                           class="form-control cantidad"
                                           value="1"
                                           min="1">
                                </td>

                                <td>
                                    <input type="number"
                                           step="0.01"
                                           class="form-control service-price"
                                           placeholder="Precio"
                                           value="0">
                                </td>

                                <td class="subtotal">$0.00</td>
                                <td class="subtotal_bs">Bs0.00</td>
                                <td class="subtotal_usd">$0.00</td>

                                <td>
                                    <button type="button"
                                            class="btn btn-danger btn-sm remove-row">X</button>
                                </td>
                            </tr>
                            `;

            $('#services-table tbody').append(row);
            rowIndex++;
        }


        $(document).on('keyup change input', '.service-price, .cantidad', function () {
            let row = $(this).closest('tr');
            let price = parseFloat(row.find('.service-price').val()) || 0;
            console.log('machucapapa', price)

            row.attr('data-price', price);
            row.find('.unit-price-input').val(price);

            calculateRowService(row);
            calculateTotal();
        });
        function calculateRowService(row) {

            let price = parseFloat(
                row.find('.unit-price-input').val()
            ) || 0;

            let qty = parseFloat(
                row.find('.cantidad').val()
            ) || 0;

            let subtotal = price * qty;

            row.find('.subtotal').text('$' + subtotal.toFixed(2));
            row.find('input.subtotal-input').val(subtotal.toFixed(2));

            row.find('.subtotal_bs')
                .text('Bs' + (subtotal / divisas['Bs']).toFixed(2));

            row.find('.subtotal_usd')
                .text('$' + (subtotal / divisas['USD']).toFixed(2));
        }



        $('#add-service').on('click', function () {
            console.log('add-service');
            if (!services.length) {
                alert('No hay servicios disponibles');
                return;
            }

            addServiceRow(services[0]); // o abre modal / select
        });


        $('#add-product').on('click', function () {
            console.log('add-product');
            let row = `
                                                                                                                                                                                                                                                           <tr data-price="0">
                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                    <select name="products[${rowIndex}][product_id]" class="form-control product-select">
                                                                                                                                                                                                                                                                        <option value="">Seleccione</option>
                                                                                                                                                                                                                                                                        ${products.map(p =>
                `<option value="${p.id}" data-price="${p.precio}">
                                                                                                                                                                                                                                                                                ${p.producto}
                                                                                                                                                                                                                                                                            </option>`
            ).join('')}
                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                                                                        <input type="hidden" name="products[${rowIndex}][type]" value="PRODUCT">

                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                        <input type="number" name="products[${rowIndex}][cantidad]" 
                                                                                                                                                                                                                               class="form-control cantidad" value="1" min="1">
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                    <td class="precio">$0.00</td>
                                                                                                                                                                                                                    <td class="subtotal">$0.00</td>

                                                                                                                                                                                                                    <td class="subtotal_bs">Bs0.00</td>
                                                                                                                                                                                                                    <td class="subtotal_usd">$0.00<                                                                                                                                             <td>
                                                                                                                                                                                                                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                `;

            $('#products-table tbody').append(row);
            rowIndex++;
        });
        function calculateRow(row) {
            let price = parseFloat(row.data('price')) || 0;
            console.log('poderes', parseFloat(row.data('data-price')))
            let qty = parseFloat(row.find('.cantidad').val()) || 0;

            let subtotal = price * qty;
            console.log('la pinga', subtotal, price, qty, row)
            row.find('.subtotal').text('$' + subtotal.toFixed(2));
            row.find('input.subtotal-input').val(subtotal.toFixed(2));
            row.find('.subtotal_bs').text('Bs' + (subtotal / divisas['Bs']).toFixed(2));
            row.find('.subtotal_usd').text('$' + (subtotal / divisas['USD']).toFixed(2));
        }

        $(document).on('change', '.product-select', function () {
            let row = $(this).closest('tr');
            let price = $(this).find('option:selected').data('price') || 0;

            row.attr('data-price', price);
            row.find('.precio').text('$' + price.toFixed(2));

            calculateRow(row);
            calculateTotal();
        });

        $(document).on('keyup change', '.cantidad', function () {
            calculateRow($(this).closest('tr'));
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

            let total_bs = total / divisas['Bs'];
            let total_usd = total / divisas['USD'];
            $('#total-bs').text('Bs' + total_bs.toFixed(2));
            $('#total-usd').text('$' + total_usd.toFixed(2));
            $('#total').text('$' + total.toFixed(2));
            $('#subtotal').val(total.toFixed(2));
        }

        $('form').on('submit', function (e) {
            let totalCOP = parseFloat($('#subtotal').val()) || 0;
            let totalPaid = parseFloat($('#total-paid').text()) || 0;
            let clientId = $('#client_id').val();
            let isNewClient = $('#new-client-check').is(':checked');
            console.log(totalPaid, totalCOP, 'matatena', totalPaid < totalCOP)
            if (totalPaid === totalCOP) {

            } else if (totalPaid < totalCOP) {
                console.log('pq llega aca')


                if (!clientId && !isNewClient) {
                    e.preventDefault();
                    alert('❌ Para dejar una orden fiada debes seleccionar o registrar un cliente');
                    return;
                }

                // Cliente nuevo pero sin nombre
                if (isNewClient && !$('input[name="new_client[name]"]').val()) {
                    e.preventDefault();
                    alert('❌ Debes completar los datos del cliente nuevo');
                    return;
                }
            }

        });

        $('form').on('submit', function (e) {

            let valid = true;

            $('#services-table tbody tr').each(function () {
                let serviceId = $(this).find('.service-select').val();
                let price = parseFloat($(this).find('.service-price').val()) || 0;

                if (!serviceId || price <= 0) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('❌ Todos los servicios deben tener nombre y precio válido');
                return;
            }

            let totalCOP = parseFloat($('#subtotal').val()) || 0;
            let totalPaid = parseFloat($('#total-paid').text()) || 0;

        });


    </script>
@endsection