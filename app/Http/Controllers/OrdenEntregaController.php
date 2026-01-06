<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Divisa;
use App\Models\Fecha;
use App\Models\Inventario;
use App\Models\LibroDiario;
use App\Models\ordenEntrega;
use App\Models\OrdenEntregaProducto;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OrdenEntregaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = ordenEntrega::all();
        return view('orden.index', compact('ordenes'));
    }

    public function today()
    {
        // whereDate filtra ignorando la hora, minutos y segundos
        $ordenes = ordenEntrega::whereDate('created_at', Carbon::today())->get();

        return view('orden.index', compact('ordenes'));
    }


    public static function metricas()
    {
        // Producto más vendido
        $productoMasVendido = OrdenEntregaProducto::select(
            'product_id',
            DB::raw('SUM(cantidad) as total_vendido')
        )
            ->groupBy('product_id')
            ->orderByDesc('total_vendido')
            ->with('producto')
            ->first();

        // Órdenes totales
        $ordenesTotales = DB::table('orden_entregas')->count();

        // Facturación total (subtotal de órdenes)
        $facturacionTotal = DB::table('orden_entregas')->sum('subtotal');

        // Top 5 productos más vendidos
        $topProductos = OrdenEntregaProducto::select(
            'product_id',
            DB::raw('SUM(cantidad) as total')
        )
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('producto')
            ->limit(5)
            ->get();

        // Inventario bajo mínimo
        $inventarioCritico = DB::table('inventarios')
            ->whereColumn('stock', '<=', 'stock_min')
            ->count();

        // Valor total del inventario (a costo)
        $valorInventario = DB::table('inventarios')
            ->select(DB::raw('SUM(stock * precio) as total'))
            ->value('total');

        return [
            'producto_mas_vendido' => $productoMasVendido,
            'ordenes_totales' => $ordenesTotales,
            'facturacion_total' => $facturacionTotal,
            'top_productos' => $topProductos,
            'inventario_critico' => $inventarioCritico,
            'valor_inventario' => $valorInventario,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id = null)
    {
        $numeroId = $id;
        $clientes = Cliente::all();
        $divisas = Divisa::all();
        $products = Inventario::where('stock', '>', 0)
            ->select('id', 'codigo', 'producto', 'precio')
            ->get();

        return view("orden.create", compact('numeroId', 'products', 'clientes', 'divisas'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'method' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'client_id' => 'nullable|exists:clientes,id',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:inventarios,id',
            'products.*.cantidad' => 'required|integer|min:1',

            // validación cliente nuevo (opcional)
            'new_client.name' => 'nullable|string',
            'new_client.fecha_nacimiento' => 'nullable|date',
            'new_client.telefono' => 'nullable|string',
            'new_client.correo' => 'nullable|email',
            'new_client.direccion' => 'nullable|string',
            'new_client.cedula' => 'nullable|string',
        ]);
        DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | CLIENTE
            |--------------------------------------------------------------------------
            */
            $clientId = $request->client_id;

            if (!$clientId && $request->filled('new_client.name')) {
                $cliente = Cliente::create($request->new_client);
                $clientId = $cliente->id;
            }

            /*
            |--------------------------------------------------------------------------
            | ORDEN
            |--------------------------------------------------------------------------
            */
            $orden = ordenEntrega::create([
                'method' => $request->method,
                'subtotal' => $request->subtotal,
                'client_id' => $clientId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | PRODUCTOS + DESCUENTO DE STOCK
            |--------------------------------------------------------------------------
            */
            foreach ($request->products as $item) {

                $producto = Inventario::lockForUpdate()->findOrFail($item['product_id']);

                // Validar stock
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception(
                        "Stock insuficiente para {$producto->producto}"
                    );
                }

                // Guardar relación
                OrdenEntregaProducto::create([
                    'orden_id' => $orden->id,
                    'product_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                ]);

                // Descontar stock
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }
        });

        return redirect()
            ->route('orden.index')
            ->with('success', 'Orden creada correctamente');

        $fecha = Carbon::now()->format('Y-m-d');

        // $fechaExistente = Fecha::whereDate('fecha', $fecha)->first();
        // $libroMayorController = new LibroMayorController;

        // if (!$fechaExistente) {
        //     $fechaExistente = Fecha::create(['fecha' => $fecha]);
        // }

        // //cu
        // $libroDiario = new LibroDiario;
        // $libroDiario->concepto = "Falta por Pagar";
        // $libroDiario->debeIdMayor = ["2"];
        // $libroDiario->haberIdMayor = null;
        // $libroDiario->debe = "[\"" . (number_format((float) $orden->precio, 2) - number_format((float) $orden->abonado, 2)) . "\"]";
        // $libroDiario->haber = "[\"0\"]";
        // $libroDiario->fecha_id = $fechaExistente->id;
        // $libroDiario->fecha = $fecha;
        // $libroDiario->save();
        // $libroMayorController->calculateBalance('2');

        // //deudas
        // $libroVenta = new LibroDiario;
        // $libroVenta->concepto = "Abono";
        // $libroVenta->debeIdMayor = ["1"];
        // $libroVenta->haberIdMayor = null;
        // $libroVenta->debe = "[\"" . number_format((float) $orden->abonado, 2) . "\"]";
        // $libroVenta->haber = "[\"0\"]";
        // $libroVenta->fecha_id = $fechaExistente->id;
        // $libroVenta->fecha = $fecha;
        // $libroVenta->save();
        // $libroMayorController->calculateBalance('1');


        // if (!$request->input('cliente')) {
        //     $client = new Cliente;
        //     $client->name = $orden->name . " " . $orden->apellido;
        //     $client->fecha_nacimiento = $request->input("fechaNacimiento");
        //     $client->telefono = $request->input("telefono");
        //     $client->correo = $request->input("correo");
        //     $client->direccion = $request->input("direccion");
        //     $client->cedula = $request->input("cedula");
        //     $client->save();
        // }




        $success = array("message" => "Orden creada Satisfactoriamente", "alert" => "success");
        return redirect()->route('orden.index')->with('success', $success);
    }

    /**
     * Display the specified resource.
     */
    public function show(ordenEntrega $ordenEntrega)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ordenEntrega $ordenEntrega)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ordenEntrega $ordenEntrega)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ordenEntrega $ordenEntrega)
    {
        //
    }
}
