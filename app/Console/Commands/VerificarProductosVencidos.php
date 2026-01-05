<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventario;
use App\Models\Notificacion;
use Carbon\Carbon;

class VerificarProductosVencidos extends Command
{
    protected $signature = 'verificar:productos_vencidos';
    protected $description = 'Verificar productos vencidos y realizar acciones.';

    public function handle()
    {

    }
}
