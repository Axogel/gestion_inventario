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
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 120px;
        }

        .title {
            text-align: right;
        }

        .title h2 {
            margin: 0;
            font-size: 20px;
        }

        .title p {
            margin: 2px 0 0;
            font-size: 12px;
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
            font-size: 14px;
        }

        .company-info {
            margin-top: 10px;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .company-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 4px;
            display: block;
        }

        </head><body><div class="header"><table class="header-table"><tr><td><img src="{{ public_path('assets/images/brand/logo.png') }}" class="logo" alt="Logo Empresa"><div class="company-info"><span class="company-name">Papeleria Jose Márquez</span><div>Tel: 0424-7182880</div><div>Email: fotocopiayservicios@gmail.com</div></div></td><td class="title"><h2>Nota de Entrega</h2><p>Orden #{{ $orden->id }}</p></td></tr></table></div><div class="info"><strong>Fecha:</strong>
        {{ $orden->created_at->format('d/m/Y H:i') }}
        <br><strong>Cliente:</strong>
        {{ $orden->cliente->name ?? 'CONSUMIDOR FINAL' }}
        </div><table><thead><tr><th>Ítem</th><th>Cant.</th><th class="right">Subtotal</th></tr></thead><tbody>
        @foreach($orden->items as $item)
            <tr><td>
            {{ $item->type === 'PRODUCT'
            ? $item->producto->producto
            : $item->service->name
                                            }}
            </td><td class="right">{{ $item->cantidad }}</td><td class="right">
            {{ number_format($item->subtotal, 2, ',', '.') }}
        COP </td></tr>@endforeach </tbody></table><br><table><tr><td class="right total">TOTAL:</td><td class="right total">
        {{ number_format($orden->subtotal, 2, ',', '.') }}
        COP </td></tr></table><br>
        @if($orden->pagos->count())
            <strong>Pagos:</strong><ul>
            @foreach($orden->pagos as $pago)
                <li>
                {{ $pago->method }}
                —
                {{ number_format($pago->amount, 2, ',', '.') }}
                {{ $pago->currency }}
        </li>@endforeach </ul>@endif </body></html>