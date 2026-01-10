<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Http\Controllers\Controller;
use App\Models\Expense;
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

        $payments = Payment::whereDate('created_at', $today)->get();
        $actuallyBox = Box::whereDate('date', $today)->first();

        // 1. Calcular ingresos y gastos
        $totalCollected = $payments->sum('monto_base');
        $totalExpenses = Expense::whereDate('fecha', $today)->sum('monto'); // <--- Sumar gastos

        // El neto es lo que debería haber físicamente
        $netTotal = ($actuallyBox ? $actuallyBox->init : 0) + $totalCollected - $totalExpenses;

        return view('box.today', compact('payments', 'actuallyBox', 'totalCollected', 'totalExpenses', 'netTotal'));
    }

    public function closeBox(Request $request, $id)
    {
        $box = Box::findOrFail($id);
        $today = date('Y-m-d');

        // 1. Obtener ingresos (Pagos)
        $totalPayments = Payment::whereDate('created_at', $today)->sum('monto_base');

        // 2. Obtener egresos (Gastos)
        $totalExpenses = Expense::whereDate('fecha', $today)->sum('monto');

        // 3. Calcular el total esperado (Fórmula: Inicial + Entradas - Salidas)
        $expectedAmount = ($box->init + $totalPayments) - $totalExpenses;

        // 4. El monto que el usuario dice que hay físicamente
        $actualAmount = $request->final;

        // 5. Calcular la diferencia (Descuadre)
        $difference = $actualAmount - $expectedAmount;

        $box->update([
            'final' => $actualAmount,
            'status' => 'closed',
        ]);

        // Mensaje de feedback
        if ($difference == 0) {
            $msg = "Caja cerrada correctamente. ¡Todo cuadra!";
            $type = 'success';
        } elseif ($difference > 0) {
            $msg = "Caja cerrada. Sobrante de $" . number_format($difference, 2);
            $type = 'warning';
        } else {
            $msg = "Caja cerrada. Faltante de $" . number_format(abs($difference), 2);
            $type = 'danger';
        }

        return redirect()->back()->with($type, $msg);
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
