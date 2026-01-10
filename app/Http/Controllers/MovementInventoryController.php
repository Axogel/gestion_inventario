<?php

namespace App\Http\Controllers;

use App\Models\movementInventory;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MovementInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MovementInventory::with('product');

        if ($request->filled('from')) {
            $from = Carbon::createFromFormat(
                'Y-m-d',
                $request->from,
                config('app.timezone')
            )->startOfDay()->utc();

            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('to')) {
            $to = Carbon::createFromFormat(
                'Y-m-d',
                $request->to,
                config('app.timezone')
            )->endOfDay()->utc();

            $query->where('created_at', '<=', $to);
        }

        $movements = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('movement.index', compact('movements'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(movementInventory $movementInventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(movementInventory $movementInventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, movementInventory $movementInventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(movementInventory $movementInventory)
    {
        //
    }
}
