<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    protected $table = 'clientes';

    protected $fillable = [
        'name',
        'direccion',
        'fecha_nacimiento',
        'telefono',
        'cedula',
        'correo',

    ];
    use HasFactory;
}
