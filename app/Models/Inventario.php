<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'producto',
        'codigo',
        'nombre',
        'precio',
        'stock',
        'stock_min',
    ];
    use HasFactory;

    public function ordenInventario()
    {
        $this->belongsToMany(ordenEntrega::class, 'orden_entregas_productos', 'product_id ', 'orden_id ');
    }

}
