<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
        }
        .invoice {
            width: 80%;
            margin: 20px auto;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="invoice">
    <div class="invoice-header">
        <img src=""  alt="">
        <span>OSORIO F.P.</span>
        <div>
            la moda del buen vestir
            RIF: V-23172897-6
        </div>
    </div>
    <P>
        Unicentro el Angel al frente de la Iglesia el Angel - Diagonal a Bancamiga local P2M - Cel 0414-7391583 0414-70941130426-4770522- Fijo 0276-3579038 - San Cristóbal Edo. Táchira.
    </P>
    <br>

    <div class="invoice-details">
        <p><strong>Nombre:</strong> {{ $factura->name }}</p>
        <p><strong>Direccion:</strong> {{ $factura->direccion }}</p>
        <p><strong>Telefono:</strong> {{ $factura->telefono }}</p>
        <p><strong>RIF:</strong> {{ $factura->RIF }}</p>
        <p><strong>Control:</strong> {{ $factura->control }}</p>
        <p><strong>Divisa:</strong> {{ $factura->divisa }}</p>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Productp</th>
                <th>marca</th>
                <th>talla</th>
                <th>almacen</th>
                <th>color</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <!-- Add invoice items dynamically if needed -->
            @foreach($factura->products as $product)
                <tr>
                    <td>{{$product->producto}}</td>
                    <td>{{$product->marca}}</td>
                    <td>{{$product->talla}}</td>
                    <td>{{$product->almacen}}</td>
                    <td>{{$product->color}}</td>
                    <td>{{$product->precio}}</td>

                </tr>    
            @endforeach

            <!-- Add more rows as needed -->
        </tbody>
    </table>

    <div class="invoice-footer">
        <p><strong>SubTotal:</strong>{{ $factura->subtotal }} Bs</p>
    </div>
</div>

</body>
</html>
