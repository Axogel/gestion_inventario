<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Cliente;
use App\Models\Divisa;
use App\Models\Fecha;
use App\Models\Inventario;
use App\Models\LibroDiario;
use App\Models\ordenEntrega;
use App\Models\OrdenEntregaProducto;
use App\Models\OrdenPagos;
use App\Models\Payment;
use App\Models\Services;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OrdenEntregaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Definimos las fechas. Si no vienen en el request, por defecto es el mes actual.
        $desde = $request->get('desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', Carbon::now()->endOfMonth()->toDateString());

        // 2. Filtramos las órdenes en ese rango
        // Usamos whereBetween y nos aseguramos de que las fechas incluyan todo el día
        $ordenes = ordenEntrega::whereBetween('created_at', [
            Carbon::parse($desde)->startOfDay(),
            Carbon::parse($hasta)->endOfDay()
        ])->orderBy('created_at', 'desc')->get();

        return view('orden.index', compact('ordenes', 'desde', 'hasta'));
    }

    public function today()
    {
        // whereDate filtra ignorando la hora, minutos y segundos
        $ordenes = ordenEntrega::whereDate('created_at', Carbon::today())->get();

        return view('orden.index', compact('ordenes'));
    }


    public static function metricas()
    {
        $productoMasVendido = OrdenEntregaProducto::where('type', 'PRODUCT') // <--- Filtrado
            ->select(
                'product_id',
                DB::raw('SUM(cantidad) as total_vendido')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_vendido')
            ->with('producto')
            ->first();

        $ordenesTotales = DB::table('orden_entregas')->count();

        // Facturación total
        $facturacionTotal = DB::table('orden_entregas')->sum('subtotal');

        // 2. Top 5 productos más vendidos (Solo tipo PRODUCT)
        $topProductos = OrdenEntregaProducto::where('type', 'PRODUCT') // <--- Filtrado
            ->select(
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



        return [
            'producto_mas_vendido' => $productoMasVendido,
            'ordenes_totales' => $ordenesTotales,
            'facturacion_total' => $facturacionTotal,
            'top_productos' => $topProductos,
            'inventario_critico' => $inventarioCritico,
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
        $services = Services::all();
        $products = Inventario::where('stock', '>', 0)
            ->select('id', 'codigo', 'producto', 'precio', 'stock')
            ->get();

        return view("orden.create", compact('numeroId', 'products', 'clientes', 'divisas', 'services'));
    }

    public function deudores()
    {
        $orderFiadas = OrdenPagos::with([
            'orden.cliente'
        ])
            ->where('type', 'debt')
            ->get();

        return view('orden.deudores', compact('orderFiadas'));
    }

    public function paidDebit($id)
    {
        $pago = OrdenPagos::findOrFail($id);

        $pago->type = 'sale';
        $pago->method = 'EFECTIVO';
        $pago->save();

        $payment = Payment::create([
            'orden_id' => $pago->orden_id,
            'metodo_pago' => 'EFECTIVO',
            'monto_base' => $pago->amount,
            'tasa_cambio' => $pago->exchange_rate,
            'moneda' => $pago->currency,
            'monto_original' => $pago->amount,
        ]);

        return redirect()
            ->route('deudores')
            ->with('success', 'Deuda marcada como pagada');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'client_id' => 'nullable|exists:clientes,id',

            'products' => 'required|array|min:1',
            'products.*.type' => 'required|in:PRODUCT,SERVICE',
            'products.*.product_id' => 'nullable|exists:inventarios,id',
            'products.*.cantidad' => 'required|integer|min:1',

            'payments' => 'nullable|array',
            'payments.*.method' => 'required|string',
            'payments.*.currency' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.exchange_rate' => 'required|numeric|min:0',
        ]);
        try {
            DB::transaction(function () use ($request) {


                if ($request->filled('new_client.name')) {
                    $client = Cliente::create([
                        'name' => $request->new_client['name'],
                        'direccion' => $request->new_client['direccion'],
                        'fecha_nacimiento' => $request->new_client['fecha_nacimiento'],
                        'telefono' => $request->new_client['telefono'],
                        'cedula' => $request->new_client['cedula'],
                        'correo' => $request->new_client['correo'],
                    ]);
                    $request->merge(['client_id' => $client->id]);
                }
                $orden = OrdenEntrega::create([
                    'subtotal' => 0,
                    'client_id' => $request->client_id,
                ]);

                $subtotal = 0;

                foreach ($request->products as $item) {

                    if ($item['type'] === 'PRODUCT') {

                        $producto = Inventario::lockForUpdate()
                            ->findOrFail($item['product_id']);

                        if ($producto->stock < $item['cantidad']) {
                            throw new \Exception(
                                "Stock insuficiente para {$producto->producto}"
                            );
                        }

                        $producto->decrement('stock', $item['cantidad']);
                        $lineSubtotal = $item['cantidad'] * $producto['precio'];
                    } else {
                        $lineSubtotal = $item['cantidad'] * $item['price'];
                    }

                    $subtotal += $lineSubtotal;
                    OrdenEntregaProducto::create([
                        'orden_id' => $orden->id,
                        'type' => $item['type'],
                        'product_id' => $item['product_id'] ?? null,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $lineSubtotal,
                        'service_id' => $item['service_id'] ?? null,
                    ]);
                }

                $orden->update(['subtotal' => $subtotal]);


                $totalPagadoBase = 0;
                $metodos = [];

                if ($request->filled('payments')) {
                    foreach ($request->payments as $pago) {

                        $amountBase = $pago['amount'] * $pago['exchange_rate'];
                        $totalPagadoBase += $amountBase;
                        $metodos[] = $pago['method'];

                        OrdenPagos::create([
                            'orden_id' => $orden->id,
                            'method' => $pago['method'],
                            'currency' => $pago['currency'],
                            'amount' => $pago['amount'],
                            'exchange_rate' => $pago['exchange_rate'],
                            'amount_base' => $amountBase,
                            'type' => 'sale',
                        ]);
                    }
                }
                if ($totalPagadoBase < $subtotal && !$orden->client_id) {
                    throw new \Exception(
                        '❌ Para dejar una orden fiada debes seleccionar o registrar un cliente'
                    );
                }
                if ($totalPagadoBase < $subtotal) {

                    OrdenPagos::create([
                        'orden_id' => $orden->id,
                        'method' => 'DEUDA',
                        'currency' => 'COP',
                        'amount' => $subtotal - $totalPagadoBase,
                        'exchange_rate' => 1,
                        'amount_base' => $subtotal,
                        'type' => 'debt',
                    ]);

                    Payment::create([
                        'orden_id' => $orden->id,
                        'moneda' => 'COP',
                        'monto_original' => $totalPagadoBase,
                        'tasa_cambio' => 1,
                        'monto_base' => $totalPagadoBase,
                        'metodo_pago' => implode(' + ', array_unique($metodos)),
                        'referencia' => null,
                    ]);

                } else {
                    Payment::create([
                        'orden_id' => $orden->id,
                        'moneda' => $pago['currency'],
                        'monto_original' => $subtotal / $pago['exchange_rate'],
                        'tasa_cambio' => $pago['exchange_rate'],
                        'monto_base' => $subtotal,
                        'metodo_pago' => implode(' + ', array_unique($metodos)),
                        'referencia' => null,
                    ]);
                }

            });

            return redirect()
                ->route('orden.index')
                ->with('success', 'Orden creada correctamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */

    public function show(OrdenEntrega $orden)
    {
        $orden->load([
            'items.producto',
            'items.service',
            'pagos',
            'cliente',
        ]);

        $pdf = Pdf::loadView('orden.factura', compact('orden'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'orden_' . $orden->id . '.pdf'
        );
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
    public function destroy($id)
    {

        try {
            DB::transaction(function () use ($id) {
                // 1. Cargar la orden con todas sus relaciones
                $ordenes = ordenEntrega::all();
                $orden = ordenEntrega::with(['items', 'pagos'])->findOrFail($id);

                // 2. REINTEGRAR INVENTARIO
                foreach ($orden->items as $item) {
                    // Solo reingramos si es un producto (los servicios no tienen stock)
                    if ($item->type === 'PRODUCT' && $item->product_id) {
                        $producto = Inventario::lockForUpdate()->find($item->product_id);
                        if ($producto) {
                            $producto->increment('stock', $item->cantidad);

                        }
                    }
                }

                // 3. ELIMINAR LOS PAGOS ASOCIADOS (Afecta el reporte de caja)
                // Al borrar el registro en la tabla 'payments', el BoxController 
                // dejará de sumarlo en el totalCollected de ese día.
                $pagos = Payment::where('orden_id', $orden->id)->get();
                $boxDate = Box::where('date', $orden->created_at->format('Y-m-d'))->first();
                foreach ($pagos as $pago) {
                    // if ($boxDate->status != 'closed')
                    //     $boxDate->update([
                    //         'final' => $boxDate->final - $pago->monto_base,
                    //     ]);
                    $pago->delete();
                }

                // 4. ELIMINAR RELACIONES INTERNAS (Opcional si usas onDelete('cascade'))
                // Borramos los detalles de productos y pagos internos de la orden
                $orden->items()->delete();
                $orden->pagos()->delete(); // Estos son los de la tabla OrdenPagos

                // 5. FINALMENTE BORRAR LA ORDEN
                $orden->delete();
            });

            return redirect()->route('orden.index')
                ->with('success', 'Devolución procesada: Stock reintegrado y caja actualizada.');

        } catch (\Exception $e) {
            return redirect()->route('orden.index')
                ->with('error', 'Error al procesar devolución: ' . $e->getMessage());
        }
    }
}
