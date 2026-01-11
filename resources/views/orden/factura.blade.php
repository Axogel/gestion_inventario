<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura Orden #{{ $orden->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>FACTURA</h2>
        <p>Orden #{{ $orden->id }}</p>
    </div>

    <div class="info">
        <strong>Fecha:</strong> {{ $orden->created_at->format('d/m/Y H:i') }} <br>

        <strong>Cliente:</strong>
        {{ $orden->cliente->name ?? 'CONSUMIDOR FINAL' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Ítem</th>
                <th>Cant.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @foreach($orden->items as $item)
                <tr>
                    @if($item->type == 'PRODUCT')
                        <td>{{ $item->producto->producto }}</td>
                    @else
                        <td>{{ $item->service->name }}</td>
                    @endif
                    <td class="right">{{ $item->cantidad }}</td>
                    <td class="right">
                        {{ number_format($item->subtotal, 2, ',', '.') }} COP
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table>
        <tr>
            <td class="right total">TOTAL:</td>
            <td class="right total">
                {{ number_format($orden->subtotal, 2, ',', '.') }} COP
            </td>
        </tr>
    </table>

    <br>

    @if($orden->pagos->count())
        <strong>Pagos:</strong>
        <ul>
            @foreach($orden->pagos as $pago)
                <li>
                    {{ $pago->method }} —
                    {{ number_format($pago->amount, 2) }}
                    {{ $pago->currency }}
                </li>
            @endforeach
        </ul>
    @endif

</body>

</html>