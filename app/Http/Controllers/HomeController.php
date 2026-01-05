<?php

namespace App\Http\Controllers;

use App\Models\Divisa;
use App\Models\OrdenEntregaProducto;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // $ultimaActualizacion = Divisa::latest('updated_at')->first();

        // if ($ultimaActualizacion) {
        //     $fechaUltimaActualizacion = Carbon::parse($ultimaActualizacion->updated_at)->toDateString();
        //     $fechaHoy = now()->toDateString();
        //     $last = $fechaUltimaActualizacion == $fechaHoy;
        // }
        // if($last){
        //     Artisan::call('app:scraping');
        //     Artisan::call('app:update-ves');
        //     Artisan::call('app:happy-b');
        //     Artisan::call('verificar:productos_vencidos');
        // }
        // Artisan::call('app:scraping');
        // Artisan::call('app:update-ves');
        Artisan::call('app:happy-b');
        Artisan::call('verificar:productos_vencidos');
        // Artisan::call('verificar:productos_vencidos');
        // Artisan::call('app:update-ves');

        // Artisan::call('app:scraping');


    }
    public function index()
    {

        $metricas = OrdenEntregaController::metricas();

        return view('admin.metricas', compact('metricas'));
    }

    public function dashboardgrap()
    {
        $metricas = OrdenEntregaController::metricas();

        return view('admin.metricas', compact('metricas'));
    }
    public function dashboard()
    {
        $metricas = OrdenEntregaController::metricas();

        return view('admin.metricas', compact('metricas'));
    }
    public function metricas()
    {
        $metricas = OrdenEntregaController::metricas();

        return view('admin.metricas', compact('metricas'));
    }
}
