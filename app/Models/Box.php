<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'init',
        'final',
        'final_bs_punto',
        'final_bs_transfer',
        'final_bs_pagom',
        'final_cop_banco',
        'final_usd',
        'date',
        'status'
    ];
}
