<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Insumos Médicos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #348FFF;
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stock-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            color: white;
        }
        .stock-danger {
            background-color: #dc3545; /* Rojo */
        }
        .stock-success {
            background-color: #28a745; /* Verde */
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>Reporte de Insumos Médicos</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Stock</th>
                <th>Stock Mínimo</th>
                <th>Precio</th>
                <th>Proveedor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($insumos as $insumo)
                <tr>
                    <td>{{ $insumo->id }}</td>
                    <td>{{ $insumo->nombre }}</td>
                    <td>{{ $insumo->descripcion ?? 'N/A' }}</td>
                    <td>{{ $insumo->unidad_medida }}</td>
                    <td>
                        <span class="stock-badge {{ $insumo->stock <= $insumo->stock_minimo ? 'stock-danger' : 'stock-success' }}">
                            {{ $insumo->stock }}
                        </span>
                    </td>
                    <td>{{ $insumo->stock_minimo ?? 'N/A' }}</td>
                    <td>{{ $insumo->precio ? '$' . number_format($insumo->precio, 2) : 'N/A' }}</td>
                    <td>{{ $insumo->proveedor ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No hay insumos médicos para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el: {{ \Carbon\Carbon::now()->setTimezone('America/Santiago')->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
