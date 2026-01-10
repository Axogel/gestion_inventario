<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movementInventory extends Model
{
    use HasFactory;

    protected $table = 'movement_inventories';

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'reason',
        'balance_after',
    ];


    public function product()
    {
        return $this->belongsTo(Inventario::class);
    }
}
