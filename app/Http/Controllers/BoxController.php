<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Http\Controllers\Controller;
use App\Models\Divisa;
use App\Models\Expense;
use App\Models\OrdenPagos;
use App\Models\Payment;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtenemos todas las cajas ordenadas por la más reciente
        $boxes = Box::orderBy('date', 'desc')->paginate(10);

        return view('box.index', compact('boxes'));
    }

    public function today()
    {
        $today = date('Y-m-d');

        $payments = Payment::whereDate('created_at', $today)->get();
        $actuallyBox = Box::whereDate('date', $today)->first();

        // 1. Calcular ingresos y gastos
        $totalCollected = $payments->sum('monto_base');
        $totalExpenses = Expense::whereDate('fecha', $today)->sum('monto'); // <--- Sumar gastos

        // El neto es lo que debería haber físicamente
        $netTotal = ($actuallyBox ? $actuallyBox->init : 0) + $totalCollected - $totalExpenses;

        return view('box.today', compact('payments', 'actuallyBox', 'totalCollected', 'totalExpenses', 'netTotal'));
    }

    public function openBox(Request $request)
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $today = date('Y-m-d');
        $divisas = Divisa::all()->keyBy('name');
        $payments = Payment::whereDate('created_at', $today)->get();
        $actuallyBox = Box::whereDate('date', $today)->first();
        $orderPay = OrdenPagos::whereDate('created_at', $today)->where('type', 'sale')->get();
        // // Total ingresos
        // $totalCollected = $payments->sum('monto_base');

        // Total gastos
        $expenses = Expense::whereDate('fecha', $today)->get();

        $netTotal = 0;
        // Asumiendo que $orderPay es una colección de la tabla cuya estructura enviaste
        $paymentsGrouped = $orderPay->groupBy('method') // Columna 3
            ->map(function ($methodGroup) {
                return $methodGroup->groupBy('currency') // Columna 4
                    ->map(function ($currencyGroup) {
                        return [
                            // Usamos 'amount' (Columna 5) para el total en moneda original
                            'total' => $currencyGroup->sum('amount'),
                            // 'amount_base' (Columna 7) si quieres el total unificado en tu moneda local
                            'total_base' => $currencyGroup->sum('amount_base'),
                            'count' => $currencyGroup->count(),
                        ];
                    });
            });
        $expensesGrouped = $expenses->flatMap(function ($expense) {
            $methods = explode('+', $expense->metodo_pago);

            return array_map(function ($method) use ($expense) {
                return [
                    'metodo_pago' => trim($method),
                    'moneda' => $expense->moneda,
                    'monto' => $expense->monto,
                ];
            }, $methods);
        })
            ->groupBy('metodo_pago')
            ->map(function ($methodGroup) {
                return $methodGroup->groupBy('moneda')->map(function ($currencyGroup) {
                    return [
                        'total' => $currencyGroup->sum('monto'),
                        'count' => $currencyGroup->count(),
                    ];
                });
            });
        //             'totalCollected',
        // 'totalExpenses',
        // 'netTotal'
        $totalExpenses = 0;

        // Suma total de pagos que son Efectivo Y moneda COP
        $totalCollected = $orderPay->where('method', 'EFECTIVO')
            ->where('currency', 'COP')
            ->sum('amount');

        // Suma total de gastos que son moneda COP
        $totalExpenses = $expenses->where('moneda', 'COP')
            ->sum('monto');
        // Neto en caja
        $netTotal = ($actuallyBox ? $actuallyBox->init : 0)
            + $totalCollected
            - $totalExpenses;

        return view('box.today', compact(
            'payments',
            'paymentsGrouped',
            'expensesGrouped',
            'actuallyBox',
            'totalCollected',
            'totalExpenses',
            'netTotal'
        ));
    }

    public function closeBox(Request $request, $id)
    {
        $box = Box::findOrFail($id);
        $today = date('Y-m-d');

        // 1. Obtener pagos del día
        $orderPay = OrdenPagos::whereDate('created_at', $today)
            ->where('type', 'sale')
            ->get();

        // 2. Obtener gastos del día agrupados por moneda
        $expenses = Expense::whereDate('fecha', $today)->get();
        $expensesGrouped = $expenses->groupBy('moneda')
            ->map(function ($group) {
                return $group->sum('monto');
            });

        // 3. Agrupar pagos por método y moneda
        $paymentsGrouped = $orderPay->groupBy('method')
            ->map(function ($methodGroup) {
                return $methodGroup->groupBy('currency')
                    ->map(function ($currencyGroup) {
                        return $currencyGroup->sum('amount');
                    });
            });
        // 4. Calcular totales esperados por método/moneda (restando expenses correspondientes)
        $expectedAmounts = [
            'efectivo_cop' => ($paymentsGrouped['EFECTIVO']['COP'] ?? 0) - ($expensesGrouped['COP'] ?? 0),
            'punto_bs' => $paymentsGrouped['PUNTO']['Bs'] ?? 0,
            'efectivo_usd' => ($paymentsGrouped['EFECTIVO']['USD'] ?? 0) - ($expensesGrouped['USD'] ?? 0),
            'transferencia_bs' => $paymentsGrouped['TRANSFERENCIA']['Bs'] ?? 0,
            'pagomovil_bs' => ($paymentsGrouped['PAGO_MOVIL']['Bs'] ?? 0) - ($expensesGrouped['Bs'] ?? 0),
            'banco_cop' => $paymentsGrouped['TRANSFERENCIA']['COP'] ?? 0,
        ];

        // 5. Montos reportados por el usuario
        $actualAmounts = [
            'efectivo_cop' => (float) ($request->final ?? 0),
            'punto_bs' => (float) ($request->final_bs_punto ?? 0),
            'efectivo_usd' => (float) ($request->final_usd ?? 0),
            'transferencia_bs' => (float) ($request->final_bs_transfer ?? 0),
            'pagomovil_bs' => (float) ($request->final_bs_pagom ?? 0),
            'banco_cop' => (float) ($request->final_cop_banco ?? 0),
        ];

        // 6. Calcular diferencias por cada método/moneda
        $differences = [];
        $hasDiscrepancy = false;

        foreach ($expectedAmounts as $key => $expected) {
            $actual = $actualAmounts[$key];
            $diff = round($actual - $expected, 2);

            $differences[$key] = [
                'label' => $this->getLabel($key),
                'expected' => $expected,
                'actual' => $actual,
                'difference' => $diff,
            ];

            if ($diff != 0) {
                $hasDiscrepancy = true;
            }
        }

        // 7. Guardar el cierre de caja
        $box->update([
            'final' => $request->final,
            'final_bs_punto' => $request->final_bs_punto,
            'final_bs_transfer' => $request->final_bs_transfer,
            'final_bs_pagom' => $request->final_bs_pagom,
            'final_cop_banco' => $request->final_cop_banco,
            'final_usd' => $request->final_usd ?? 0,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        // 8. Generar mensaje de feedback
        if (!$hasDiscrepancy) {
            $msg = "✅ Caja cerrada correctamente. ¡Todo cuadra!";
            $type = 'success';
        } else {
            $msg = "⚠️ Caja cerrada con diferencias:\n\n";
            foreach ($differences as $diff) {
                if ($diff['difference'] != 0) {
                    $status = $diff['difference'] > 0 ? '📈 Sobrante' : '📉 Faltante';
                    $msg .= sprintf(
                        "• %s: %s de $%s (Esperado: $%s - Real: $%s)\n",
                        $diff['label'],
                        $status,
                        number_format(abs($diff['difference']), 2),
                        number_format($diff['expected'], 2),
                        number_format($diff['actual'], 2)
                    );
                }
            }
            $type = 'warning';
        }

        return redirect()->back()
            ->with($type, $msg)
            ->with('closeDetails', $differences);
    }

    /**
     * Obtener etiquetas amigables para cada método/moneda
     */
    private function getLabel($key)
    {
        $labels = [
            'efectivo_cop' => 'Efectivo COP',
            'efectivo_bs' => 'Efectivo BS',
            'efectivo_usd' => 'Efectivo USD',
            'transferencia_bs' => 'Transferencia BS',
            'pagomovil_bs' => 'Pago Móvil BS',
            'banco_cop' => 'Bancolombia COP',
        ];

        return $labels[$key] ?? $key;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['init' => 'required|numeric|min:0']);

        Box::create([
            'init' => $request->init,
            'final' => 0,
            'date' => date('Y-m-d'),
            'status' => 'open' // Te sugiero añadir esta columna a tu tabla box
        ]);

        return redirect()->back()->with('success', 'Caja abierta correctamente');
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Box $box)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Box $box)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Box $box)
    {
        //
    }
}
