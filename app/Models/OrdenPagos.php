<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenPagos extends Model
{
    use HasFactory;

    protected $table = 'orden_pagos';

    protected $fillable = [
        'orden_id',
        'method',
        'currency',
        'amount',
        'exchange_rate',
        'amount_base',
        'type',
    ];

    public function orden()
    {
        return $this->belongsTo(ordenEntrega::class, 'orden_id');
    }

}
