
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{InventarioController,HomeController,UserController, SaleController,PromoController,PaymentController,CompController,MixController,RegionController,VoidxController,SiteController};

Route::post('/export/inventario', [InventarioController::class , 'Exportacion'])->name('exportInventario');
Route::get('/export/alquilado', [InventarioController::class , 'ExportacionAlquilado'])->name('alquilado.export');
Route::get('/export/disponible', [InventarioController::class , 'ExportacionDisponible'])->name('disponible.export');
Route::post('/import/inventario', [InventarioController::class , 'Importacion'])->name('inventario.import');
