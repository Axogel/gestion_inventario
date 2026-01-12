<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Divisa;
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

        $today = date('Y-m-d');
        $actuallyBox = Box::whereDate('date', $today)->first();

        if (!$actuallyBox || $actuallyBox->status !== 'open') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'No se puede registrar el gasto: La caja de hoy no ha sido abierta.']);
        }

        $totalCollected = Payment::whereDate('created_at', $today)->sum('monto_base');
        $totalExpenses = Expense::whereDate('fecha', $today)->sum('monto');

        $disponible = $actuallyBox->init + $totalCollected - $totalExpenses;

        if ($request->monto > $disponible) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => "Fondos insuficientes. Solo hay $" . number_format($disponible, 2) . " en caja."]);
        }

        $expense = [
            "fecha" => $request->fecha,
            "categoria" => $request->categoria,
            "descripcion" => $request->descripcion,
            "monto" => $request->monto,
            "moneda" => $request->moneda,
        ];
        Expense::create($expense);

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
