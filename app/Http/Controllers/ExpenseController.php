<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Expense;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::all();
        return view("expense.index", compact("expenses"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'moneda' => 'required|string',
            'categoria' => 'required|string',
            'fecha' => 'required|date',
        ]);

        // 1. Verificar si la caja de hoy está abierta
        $today = date('Y-m-d');
        $actuallyBox = Box::whereDate('date', $today)->first();

        if (!$actuallyBox || $actuallyBox->status !== 'open') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'No se puede registrar el gasto: La caja de hoy no ha sido abierta.']);
        }

        // 2. Calcular el dinero disponible actualmente en caja (Neto)
        $totalCollected = Payment::whereDate('created_at', $today)->sum('monto_base');
        $totalExpenses = Expense::whereDate('fecha', $today)->sum('monto');

        // Dinero real disponible = Inicial + Ventas - Gastos anteriores
        $disponible = $actuallyBox->init + $totalCollected - $totalExpenses;

        // 3. Verificar si el monto del nuevo gasto supera lo que hay en caja
        if ($request->monto > $disponible) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => "Fondos insuficientes. Solo hay $" . number_format($disponible, 2) . " en caja."]);
        }

        // 4. Si todo está bien, crear el gasto
        Expense::create($request->all());

        // 5. Opcional: Actualizar el valor 'final' de la caja automáticamente
        $actuallyBox->decrement('final', $request->monto);

        return redirect()->back()->with('success', 'Gasto registrado y restado de caja correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }
}
