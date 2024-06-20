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
            'Codigo', 'Producto', 'Marca', 'Precio', 'Talla', 'Tipo', 'Color', 'almacen', 'disponibilidad'
        ];
    }
    public function map($row): array
    {
        $row['disponibilidad'] = ($row['disponibilidad'] == 1) ? 'disponible' : (($row['disponibilidad'] == 2) ? 'vendido' : 'alquilado');

        return [
            $row['codigo'],
            $row['producto'],
            $row['marca'],
            $row['precio'],
            $row['talla'],
            $row['tipo'],
            $row['color'],
            $row['almacen'],
            $row['disponibilidad'],
        ];
    }
}
