<?php

namespace App\Providers;

use App\Models\Inventario;
use App\Models\Notificacion;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewDataComposer extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Usamos query() para asegurar una consulta limpia a la DB
            $productosCriticos = Inventario::query()
                ->whereRaw('CAST(stock AS SIGNED) <= CAST(stock_min AS SIGNED)')
                ->orderBy('stock', 'asc')
                ->get();

            $view->with('productosCriticos', $productosCriticos);
        });
    }
}
