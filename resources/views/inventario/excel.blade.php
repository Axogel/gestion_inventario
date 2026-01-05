<table border="1">
    <thead>
        <tr>
            <th>Código</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Precio sin IVA</th>
            <th>Costo</th>
            <th>Costo sin IVA</th>
            <th>Columna 2</th>
            <th>Stock</th>
            <th>Stock mínimo</th>
            <th>USD Ref</th>
            <th>Creado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $item)
            <tr>
                <td>{{ $item['codigo'] }}</td>
                <td>{{ $item['producto'] }}</td>
                <td>{{ number_format($item['precio'], 2) }}</td>
                <td>{{ number_format($item['precio_sin_iva'], 2) }}</td>
                <td>{{ number_format($item['costo'], 2) }}</td>
                <td>{{ number_format($item['costo_sin_iva'], 2) }}</td>
                <td>{{ $item['columna2'] }}</td>
                <td>{{ $item['stock'] }}</td>
                <td>{{ $item['stock_min'] }}</td>
                <td>{{ $item['usd_ref'] ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
