<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenEntregaProducto extends Model
{
    use HasFactory;

    protected $table = 'orden_entregas_productos';

    protected $fillable = [
        'orden_id',
        'product_id',
        'cantidad',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenEntrega::class, 'orden_id');
    }

    public function producto()
    {
        return $this->belongsTo(Inventario::class, 'product_id');
    }
}
