<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $clientes = Cliente::all();
        return view('cliente.index', compact('clientes'));
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cliente.create');
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'apellido' => 'required|string',
            'telefono' => 'required|string',

        ]);
        $client = new Cliente;
        $client->name = $request->input("name") . " " . $request->input("apellido");
        $client->telefono = $request->input("telefono");
        $client->save();


        $success = array("message" => "Cliente registrado Satisfactoriamente", "alert" => "success");
        return redirect()->route('cliente.index')->with('success', $success);
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('cliente.edit', compact('cliente'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'name' => 'required|string',
            'telefono' => 'required|string'
        ]);
        $updated = Cliente::where("id", $cliente->id);
        $updated->update([
            'name' => $request->input("name"),
            'telefono' => $request->input("telefono")
        ]);
        $success = array("message" => "Cliente editado Satisfactoriamente", "alert" => "success");
        return redirect()->route('cliente.index')->with('success', $success);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('cliente.index')->with('success', 'Cliente Eliminado.');
    }
}
