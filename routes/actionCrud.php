<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{FacturaController, UserController, InventarioController};

Route::get('inventario/{id}/edit', [InventarioController::class, 'edit'])->name('inventario.edit');
Route::delete('inventario/{id}/destroy', [InventarioController::class, 'destroy'])->name('inventario.destroy');
Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::get('user/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
Route::get('donwloadFactura', [FacturaController::class, 'donwloadFactura'])->name('donwloadFactura');
