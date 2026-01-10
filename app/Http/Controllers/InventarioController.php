<?php

namespace App\Http\Controllers;

use App\Exports\AlquiladoExport;
use App\Exports\disponibleExport;
use App\Exports\InventarioExport;
use App\Imports\InventarioImport;
use App\Models\Inventario;
use App\Models\movementInventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Artisan::call('verificar:productos_vencidos');

        $inventario = Inventario::all();
        return view("inventario.index", compact("inventario"));
        //
    }
    public function disponible()
    {
        $inventario = Inventario::where("disponibilidad", 1)->get();
        return view("disponible.index", compact("inventario"));
    }
    public function donation()
    {
        $products = Inventario::all();

        return view("inventario.donation", compact("products"));
    }


    public function donationStore(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:inventarios,id',
            'items.*.cantidad' => 'required|numeric|min:1',
        ]);

        $items = $request->input('items');
        $total = 0;

        foreach ($items as $item) {
            $product = Inventario::findOrFail($item['product_id']);
            $afterQty = $product->stock;
            if ($product->stock < $item['cantidad']) {
                return back()->with('error', 'No hay suficiente stock para el producto: ' . $product->producto);
            }

            $product->stock -= $item['cantidad'];
            $product->save();

            $total += $product->precio * $item['cantidad'];

            $movement = new movementInventory();
            $movement->product_id = $item['product_id'];
            $movement->quantity = $item['cantidad'];
            $movement->type = 'output';
            $movement->reason = 'donacion';
            $movement->description = $product->producto;
            $movement->balance_after = $afterQty;

            $movement->save();
        }


        return redirect()->route('inventario.index')->with('success', 'Donación creada exitosamente');
    }
    public function alquilado()
    {
        $inventario = Inventario::where("disponibilidad", 0)->get();
        return view("alquilado.index", compact("inventario"));
    }
    public function gifts()
    {
        $inventario = Inventario::where("disponibilidad", 3)->get();
        return view("gift.index", compact("inventario"));
    }
    public function sendGift($id)
    {
        $gift = Inventario::findOrFail($id);
        $gift->disponibilidad = 3;
        $gift->save();
        $success = array("message" => "Producto actualizado satisfactoriamente", "alert" => "success");
        return redirect()->route('inventario.gift')->with('success', $success);
    }
    public function comeBackGift($id)
    {
        $gift = Inventario::findOrFail($id);
        $gift->disponibilidad = 1;
        $gift->save();
        $success = array("message" => "Producto actualizado satisfactoriamente", "alert" => "success");
        return redirect()->route('inventario.gift')->with('success', $success);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventario.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string| unique:inventarios,codigo',
            'producto' => 'required|string',
            'precio' => 'required|numeric',
            'stock' => 'required|numeric',
            'stock_min' => 'required|numeric',
        ]);

        $producto = new Inventario;
        $producto->producto = $request->input('producto');
        $producto->codigo = $request->input('codigo');
        $producto->precio = $request->input('precio');
        $producto->stock = $request->input('stock');
        $producto->stock_min = $request->input('stock_min');


        $producto->save();
        $success = array("message" => "Producto creado Satisfactoriamente", "alert" => "success");
        return redirect()->route('inventario.index')->with('success', $success);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventario $inventario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Inventario::find($id);
        return view('inventario.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'producto' => 'required|string',
            'codigo' => 'required|string',
            'precio' => 'required|numeric',
            'stock' => 'required|numeric',
            'stock_min' => 'required|numeric',

        ]);

        $producto = Inventario::findOrFail($id);

        $producto->producto = $request->input('producto');
        $producto->codigo = $request->input('codigo');
        $producto->precio = $request->input('precio');
        $producto->stock = $request->input('stock');
        $producto->stock_min = $request->input('stock_min');
        $producto->update();

        $success = array("message" => "Producto actualizado satisfactoriamente", "alert" => "success");
        return redirect()->route('inventario.index')->with('success', $success);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Inventario::find($id)->delete();
        return redirect()->route('inventario.index')->with('success', 'Producto Eliminado.');
    }
    public function Exportacion(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $query = Inventario::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('codigo', 'like', '%' . $searchTerm . '%')
                    ->orWhere('producto', 'like', '%' . $searchTerm . '%')
                    ->orWhere('precio', 'like', '%' . $searchTerm . '%');
            });
        }

        $data = $query->get();

        // Devuelve una instancia de la clase InventarioExport con los datos filtrados
        return Excel::download(new InventarioExport($data), 'Productos.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function ExportacionAlquilado()
    {
        return Excel::download(new AlquiladoExport(), 'Productos.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    public function ExportacionDisponible()
    {
        return Excel::download(new disponibleExport(), 'Productos.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    public function Importacion(Request $request)
    {
        $archivo = $request->file('excel');

        if ($archivo) {

            try {
                Excel::import(new InventarioImport, $archivo);
            } catch (\Throwable $th) {
                $success = array("message" => "Productos no Importados" . $th, "alert" => "danger");
                return redirect()->back()->with('success', $success);
            }

            $success = array("message" => "Productos importados satisfactoriamente", "alert" => "success");
            return redirect()->back()->with('success', $success);
        } else {
            return back()->with('error', 'Error al subir el archivo');

        }

    }

}
