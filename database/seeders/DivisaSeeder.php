<?php

namespace Database\Seeders;

use App\Models\Divisa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisa = [
            [
                'name' => 'COP',
                'tasa' => 1
            ],
            [
                'name' => 'USD',
                'tasa' => 3700
            ],

            [
                'name' => 'Bs',
                'tasa' => 5
            ]
        ];
        Divisa::insert($divisa);
    }
}
