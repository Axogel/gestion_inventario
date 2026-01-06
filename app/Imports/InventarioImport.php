<?php

namespace App\Imports;

use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class InventarioImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Evitar registros sin código o producto
        if (empty($row['codigo']) || empty($row['articulo'])) {
            return null;
        }

        $codigo = trim($row['codigo']);

        $data = [
            'producto' => trim($row['articulo']),
            'precio' => $this->parseMoney($row['precio'] ?? 0),
            'stock' => (int) ($row['stock'] ?? 0),
            'stock_min' => (int) ($row['stock_min'] ?? 0),
        ];

        // Buscar producto por código
        $productoExistente = Inventario::where('codigo', $codigo)->first();

        if ($productoExistente) {
            // Actualizar si existe
            $productoExistente->update($data);
            return $productoExistente;
        } else {
            // Crear nuevo registro
            $data['codigo'] = $codigo;
            return Inventario::create($data);
        }
    }
    private function parseMoney($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // Convertir a string
        $value = (string) $value;

        // Eliminar todo lo que NO sea número, punto o coma
        $value = preg_replace('/[^0-9.,]/', '', $value);

        // Quitar separador de miles (.)
        $value = str_replace('.', '', $value);

        // Convertir coma decimal a punto
        $value = str_replace(',', '.', $value);
        return (int) round((float) $value);
    }



    public function chunkSize(): int
    {
        return 1000;
    }

    // Eliminamos batchSize porque no lo necesitamos
}
