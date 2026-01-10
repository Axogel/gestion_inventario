<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'orden_id',
        'moneda',
        'monto_original',
        'tasa_cambio',
        'monto_base',
        'metodo_pago',
        'referencia',
    ];
}
