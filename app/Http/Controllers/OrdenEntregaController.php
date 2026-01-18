<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnOrderRequest;
use App\Models\Box;
use App\Models\Cliente;
use App\Models\Divisa;
use App\Models\Expense;
use App\Models\Fecha;
use App\Models\Inventario;
use App\Models\LibroDiario;
use App\Models\movementInventory;
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


        $products = Inventario::all();
        $divisas = Divisa::all();
        return view('orden.index', compact('ordenes', 'desde', 'hasta', 'products', 'divisas'));
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
            ->where('method', '=', 'DEUDA')
            ->get();

        $divisas = Divisa::all();

        return view('orden.deudores', compact('orderFiadas', 'divisas'));
    }

    public function paidDebit($id, Request $request)
    {
        // Usamos round($val, 2) para asegurar que no viajen decimales "sucios"
        $montoFinal = round($request->amount, 2);
        $tasaFinal = round($request->exchange_rate, 2);
        $pago = OrdenPagos::find($id);
        $pago->type = 'sale';
        $pago->method = $request->method;
        $pago->currency = $request->currency;
        $pago->amount = $montoFinal;
        $pago->exchange_rate = $tasaFinal;
        // El amount_base lo recalculamos para que la suma sea exacta en COP
        $pago->amount_base = round($montoFinal / $tasaFinal, 2);
        $pago->save();

        Payment::create([
            'orden_id' => $pago->orden_id,
            'metodo_pago' => $request->method,
            'monto_base' => $pago->amount_base,
            'tasa_cambio' => $tasaFinal,
            'moneda' => $pago->currency,
            'monto_original' => $montoFinal,
        ]);

        return redirect()->route('deudores')->with('success', 'Deuda liquidada correctamente.');
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

                        'telefono' => $request->new_client['telefono'],

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
                $tolerance = 0.10;
                $difference = $totalPagadoBase - $subtotal;

                if (abs($difference) < $tolerance) {
                    // Se ajusta como pago exacto
                    $totalPagadoBase = $subtotal;
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

    }
    public function processReturn(ReturnOrderRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $orden = ordenEntrega::with(['items', 'pagos'])->lockForUpdate()->findOrFail($id);
            $accion = $request->accion;

            // 1️⃣ PROCESAR ITEMS DEVUELTOS
            $this->processReturnedItems($request->items_devolucion, $orden);

            // 2️⃣ SEGÚN LA ACCIÓN
            if ($accion === 'refund') {
                $this->processRefund($orden, $request);
            } elseif ($accion === 'change') {
                $result = $this->processChange($orden, $request);
            }

            // 3️⃣ ACTUALIZAR ORDEN ORIGINAL


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $accion === 'refund'
                    ? 'Devolución procesada correctamente. Dinero reembolsado.'
                    : 'Cambio procesado correctamente. Nueva orden generada.',
                'data' => $result ?? null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar items devueltos (reintegrar stock)
     */
    private function processReturnedItems(array $items, $orden)
    {
        foreach ($items as $itemData) {
            $item = OrdenEntregaProducto::where('orden_id', $orden->id)
                ->where('product_id', $itemData['product_id'])
                ->where('type', 'PRODUCT')
                ->firstOrFail();

            // Solo productos manejan stock
            if ($item->type === 'PRODUCT' && $item->product_id) {
                $producto = Inventario::lockForUpdate()->findOrFail($item->product_id);

                // Reintegrar stock
                $producto->increment('stock', $itemData['cantidad']);

                // Registrar movimiento
                movementInventory::create([
                    'product_id' => $producto->id,
                    'quantity' => $itemData['cantidad'],
                    'type' => 'input',
                    'reason' => 'DEVOLUCION',
                    'description' => "Devolución de orden #{$item->orden_id}",
                    'balance_after' => $producto->stock,
                ]);
            }

            // Marcar item como devuelto (opcional: agregar campo `returned_at`)

            // En lugar de $item->delete(); usa:
            OrdenEntregaProducto::where('orden_id', $orden->id)
                ->where('product_id', $itemData['product_id'])
                ->where('type', 'PRODUCT')
                ->delete();
        }
    }

    /**
     * Procesar reembolso de dinero
     */
    private function processRefund(ordenEntrega $orden, ReturnOrderRequest $request)
    {
        $metodo = $request->metodo_devolucion;
        $monto = $request->total_devolucion;

        // Obtener divisa según método
        $divisa = $this->getDivisaForMethod($metodo);
        $montoEnDivisa = $monto / $divisa->tasa;

        // Registrar pago negativo (salida de caja)
        OrdenPagos::create([
            'orden_id' => $orden->id,
            'method' => $metodo,
            'currency' => $divisa->name,
            'amount' => -$montoEnDivisa, // Negativo = salida
            'amount_base' => -$monto,
            'type' => 'debt',
            'exchange_rate' => $divisa->tasa,
        ]);

        // También en Payment (si lo usas para caja)

        Expense::create([
            'descripcion' => 'Devolucion de orden #' . $orden->id,
            'monto' => $montoEnDivisa,
            'moneda' => $divisa->name,
            'categoria' => 'Devolucion',
            'exchange_rate' => $divisa->tasa,
            'fecha' => now(),
        ]);

        $orden->subtotal -= $monto;
        $orden->save();
        // Payment::create([
        //     'orden_id' => $orden->id,
        //     'method' => $metodo,
        //     'currency' => $divisa->name,
        //     'amount' => -$montoEnDivisa,
        //     'amount_base' => -$monto,
        //     'type' => 'debiit',
        // ]);
    }

    /**
     * Procesar cambio por otros productos
     */
    private function processChange(ordenEntrega $orden, ReturnOrderRequest $request)
    {
        // 1. Procesar productos nuevos (descontar stock)
        $itemsNuevos = [];
        foreach ($request->productos_cambio as $productoCambio) {
            $producto = Inventario::lockForUpdate()->findOrFail($productoCambio['id']);

            // Validar stock
            if ($producto->stock < $productoCambio['cantidad']) {
                throw new \Exception("Stock insuficiente para {$producto->producto}");
            }

            // Descontar stock
            $producto->decrement('stock', $productoCambio['cantidad']);

            // Registrar movimiento
            movementInventory::create([
                'product_id' => $producto->id,
                'quantity' => $productoCambio['cantidad'],
                'type' => 'output',
                'reason' => 'CAMBIO',
                'description' => "Cambio de orden #{$orden->id}",
                'balance_after' => $producto->stock,
            ]);

            $itemsNuevos[] = [
                'product_id' => $producto->id,
                'type' => 'PRODUCT',
                'cantidad' => $productoCambio['cantidad'],
                'precio' => $productoCambio['precio'],
                'subtotal' => $productoCambio['precio'] * $productoCambio['cantidad'],
            ];
        }

        // 2. Manejar diferencia de precio
        $diferencia = $request->diferencia ?? 0;

        if ($diferencia != 0) {
            $metodo = $request->metodo_pago_diferencia;
            $divisa = $this->getDivisaForMethod($metodo);
            $montoEnDivisa = abs($diferencia) / $divisa->tasa;

            if ($diferencia > 0) {
                // Cliente recibe dinero (sobró)
                OrdenPagos::create([
                    'orden_id' => $orden->id,
                    'method' => $metodo,
                    'currency' => $divisa->name,
                    'amount' => -$montoEnDivisa,
                    'amount_base' => -abs($diferencia),
                    'type' => 'debt',
                    'exchange_rate' => $divisa->tasa,
                ]);
                Expense::create([
                    'descripcion' => 'Devolucion de orden #' . $orden->id,
                    'monto' => $montoEnDivisa,
                    'moneda' => $divisa->name,
                    'categoria' => 'Devolucion',
                    'fecha' => now(),
                ]);
            } else {
                // Cliente paga más (faltó)
                OrdenPagos::create([
                    'orden_id' => $orden->id,
                    'method' => $metodo,
                    'currency' => $divisa->name,
                    'amount' => $montoEnDivisa,
                    'amount_base' => abs($diferencia),
                    'type' => 'sale',
                    'exchange_rate' => $divisa->tasa,

                ]);
                Payment::create([
                    'orden_id' => $orden->id,
                    'metodo_pago' => $metodo,
                    'moneda' => $divisa->name,
                    'monto_original' => $montoEnDivisa,
                    'monto_base' => abs($diferencia),
                    'tasa_cambio' => $divisa->tasa,
                ]);

            }
        }

        $orden->update([
            'subtotal' => $request->total_cambio - abs($diferencia < 0 ? 0 : $diferencia),
        ]);

        // 3. Crear nueva orden para el cambio (opcional pero recomendado)
        // $nuevaOrden = ordenEntrega::create([
        //     'cliente_id' => $orden->cliente_id ?? null,
        //     'subtotal' => $request->total_cambio - abs($diferencia < 0 ? 0 : $diferencia),
        // ]);

        foreach ($itemsNuevos as $item) {
            $orden->items()->create($item);
        }

        return ['nueva_orden_id' => $orden->id];
    }

    /**
     * Obtener divisa según método de pago
     */
    private function getDivisaForMethod($metodo)
    {
        $mapDivisas = [
            'EFECTIVO' => 'COP',
            'TRANSFERENCIACOP' => 'COP',
            'TRANSFERENCIA' => 'Bs',
            'PAGOMOVIL' => 'Bs',
            'EFECTIVOUSD' => 'USD',
        ];

        $nombreDivisa = $mapDivisas[$metodo] ?? 'COP';

        return Divisa::where('name', $nombreDivisa)->firstOrFail();
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

                // 1. Cargar la orden con relaciones necesarias
                $orden = ordenEntrega::with(['items', 'pagos'])->lockForUpdate()->findOrFail($id);

                /*
                |--------------------------------------------------------------------------
                | 2. REINTEGRAR INVENTARIO
                |--------------------------------------------------------------------------
                */
                foreach ($orden->items as $item) {

                    // Solo productos (servicios no manejan stock)
                    if ($item->type === 'PRODUCT' && $item->product_id) {

                        $producto = Inventario::lockForUpdate()->find($item->product_id);

                        if (!$producto) {
                            throw new \Exception('Producto no encontrado en inventario');
                        }

                        // Reintegrar stock
                        $producto->increment('stock', $item->cantidad);

                        // Registrar movimiento
                        movementInventory::create([
                            'product_id' => $producto->id,
                            'quantity' => $item->cantidad,
                            'type' => 'input',
                            'reason' => 'DEVOLUCION',
                            'description' => 'Devolución del producto: ' . $producto->producto,
                            'balance_after' => $producto->stock,
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 3. ELIMINAR PAGOS (AFECTA CAJA)
                |--------------------------------------------------------------------------
                */
                Payment::where('orden_id', $orden->id)->delete();

                /*
                |--------------------------------------------------------------------------
                | 4. ELIMINAR RELACIONES INTERNAS
                |--------------------------------------------------------------------------
                */
                $orden->items()->delete();
                $orden->pagos()->delete(); // OrdenPagos (deudas, parciales)

                /*
                |--------------------------------------------------------------------------
                | 5. ELIMINAR ORDEN
                |--------------------------------------------------------------------------
                */
                $orden->delete();
            });

            return redirect()
                ->route('orden.index')
                ->with('success', 'Devolución procesada correctamente. Stock y caja actualizados.');

        } catch (\Throwable $e) {

            return redirect()
                ->route('orden.index')
                ->with('error', 'Error al procesar devolución: ' . $e->getMessage());
        }
    }

}
