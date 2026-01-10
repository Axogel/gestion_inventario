<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordenEntrega extends Model
{
    use HasFactory;

    protected $table = 'orden_entregas';

    protected $fillable = [
        'method',
        'subtotal',
        'client_id',
    ];

    /**
     * Una orden tiene muchos registros en la tabla pivote
     */
    public function items()
    {
        return $this->hasMany(OrdenEntregaProducto::class, 'orden_id');
    }

    /**
     * Acceso directo a los productos (opcional, cómodo)
     */
    public function productos()
    {
        return $this->belongsToMany(
            Inventario::class,
            'orden_entregas_productos',
            'orden_id',
            'product_id'
        )->withPivot('cantidad');
    }

    // Cambia esto en tu modelo
    public function pagos()
    {
        // Una orden tiene muchos pagos registrados
        return $this->hasMany(OrdenPagos::class, 'orden_id');
    }
    /**
     * Cliente (relación faltante en tu código)
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'client_id');
    }
}
