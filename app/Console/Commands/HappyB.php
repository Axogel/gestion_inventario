<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\Notificacion;
use Illuminate\Console\Command;
use App\Mail\Happy;
use Illuminate\Support\Facades\Mail;

class HappyB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:happy-b';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //
    }
}
