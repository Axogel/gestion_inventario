<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Services::all();
        return view('services.index', compact('services'));
    }

    // Eliminamos create() y edit() ya que usarás modales en el index

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Services::create($request->all());

        // Usamos back() para recargar la misma página con el mensaje
        return redirect()->route('services.index')->with('success', 'Servicio creado con éxito.');
    }

    public function update(Request $request, $id) // Cambiado para recibir el ID directamente del modal
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $service = Services::findOrFail($id);
        $service->update($request->all());

        return redirect()->route('services.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy($id)
    {
        $service = Services::findOrFail($id);
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Servicio eliminado.');
    }
}