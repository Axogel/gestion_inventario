<?php

namespace App\Exports;

use App\Models\Inventario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventarioExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'Codigo',
            'Producto',
            'precio',
            'precio_sin_iva',
            'costo',
            'costo_sin_iva',
            'columna2',
            'stock',
            'stock_min',
            'usd_ref',
            'created_at'
        ];
    }
    public function map($row): array
    {

        return [
            $row['codigo'],
            $row['producto'],
            $row['precio'],
            $row['precio_sin_iva'],
            $row['costo'],
            $row['costo_sin_iva'],
            $row['columna2'],
            $row['stock'],
            $row['stock_min'],
            $row['usd_ref'],
            $row['created_at'],
        ];
    }
}
