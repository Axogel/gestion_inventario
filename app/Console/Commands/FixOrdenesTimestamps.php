<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\OrdenEntrega;
use App\Models\OrdenEntregaProducto;
use App\Models\OrdenPagos;

class FixOrdenesTimestamps extends Command
{
    protected $signature = 'ordenes:fix-timestamps {--dry-run}';

    protected $description = 'Rellena created_at y updated_at en pagos y productos usando los timestamps de la orden';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('🔎 Buscando órdenes...');

        $ordenes = OrdenEntrega::whereNotNull('created_at')->get();

        $this->info("📦 Órdenes encontradas: {$ordenes->count()}");

        DB::transaction(function () use ($ordenes, $dryRun) {

            foreach ($ordenes as $orden) {

                $created = $orden->created_at;
                $updated = $orden->updated_at ?? $orden->created_at;

                // ===============================
                // OrdenEntregaProductos
                // ===============================
                $productos = OrdenEntregaProducto::where('orden_id', $orden->id)
                    ->whereNull('created_at');

                $countProductos = $productos->count();

                if (!$dryRun && $countProductos > 0) {
                    $productos->update([
                        'created_at' => $created,
                        'updated_at' => $updated,
                    ]);
                }

                // ===============================
                // OrdenPagos
                // ===============================
                $pagos = OrdenPagos::where('orden_id', $orden->id)
                    ->whereNull('created_at');

                $countPagos = $pagos->count();

                if (!$dryRun && $countPagos > 0) {
                    $pagos->update([
                        'created_at' => $created,
                        'updated_at' => $updated,
                    ]);
                }

                if ($countProductos || $countPagos) {
                    $this->line(
                        "✔ Orden #{$orden->id} → productos: {$countProductos}, pagos: {$countPagos}"
                    );
                }
            }
        });



        $this->info(
            $dryRun
            ? '🧪 DRY-RUN finalizado (no se modificó nada)'
            : '✅ Timestamps corregidos correctamente'
        );
    }
}
