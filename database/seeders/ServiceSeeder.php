<?php

namespace Database\Seeders;

use App\Models\Services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Anillado'],
            ['name' => 'Laminado'],
            ['name' => 'Escaneo'],
            ['name' => 'Copias'],
            ['name' => 'Impresión negro'],
            ['name' => 'Impresión color'],
            ['name' => 'Impresión fotografía'],
            ['name' => 'Impresion en adhesivo brillante'],
            ['name' => 'Impresión en adhesivo sencillo'],
        ];

        Services::insert($services);
    }
}
