<?php
use App\Http\Controllers\BoxController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MovementInventoryController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;
use App\Mail\Transfer;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\{BackupController, ClienteController, DivisaController, FacturaController, HomeController, UserController, InventarioController, LibroDiarioController, LibroMayorController, NotificacionController, OfertasController, OrdenEntregaController};
use App\Models\LibroDiario;

Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboardgrap', [HomeController::class, 'dashboardgrap'])->name('dashboardgrap');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::patch('credentials', [UserController::class, 'postCredentials'])->name('credentials');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/tours', function () {
        return view('jumbotron');
    });
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('oferta/index', [OfertasController::class, 'index'])->name('oferta.index');
    Route::post('oferta/send', [OfertasController::class, 'send'])->name('oferta.send');


});
Route::middleware([Authenticate::class])->group(function () {


    //new home
    Route::get('metricas', [HomeController::class, 'metricas'])->name('metricas');
    Route::get('donation', [InventarioController::class, 'donation'])->name('donation');
    route::post('donation', [InventarioController::class, 'donationStore'])->name('donation.store');
    Route::get('orden/today', [OrdenEntregaController::class, 'today'])->name('orden.today');
    Route::resource('box', BoxController::class);
    Route::post('/box/close/{id}', [BoxController::class, 'closeBox'])->name('box.close');
    Route::get('movement', [MovementInventoryController::class, 'index'])->name('movement.index');
    Route::resource('services', ServicesController::class);
    Route::get('orden/{orden}/print', [OrdenEntregaController::class, 'show'])
        ->name('orden.print');
    Route::post('/orden/{id}/return', [OrdenEntregaController::class, 'processReturn'])->name('orden.return');

    //deudas
    Route::get('deudores', [OrdenEntregaController::class, 'deudores'])->name('deudores');
    Route::put('orden/deuda/{id}/pagar', [OrdenEntregaController::class, 'paidDebit'])
        ->name('orden.paidDebit');
    Route::post('/orden/{id}/update-payments', [OrdenEntregaController::class, 'updatePayments'])->name('orden.updatePayments');
    Route::resource('expenses', ExpenseController::class);
    Route::resource('factura', FacturaController::class);
    Route::resource('notificacion', NotificacionController::class);
    Route::get('factura/create/new', [FacturaController::class, 'new'])->name('factura.new');
    Route::post('factura/storeNew/new', [FacturaController::class, 'storeNew'])->name('factura.storeNew');

    Route::resource('libroMayor', LibroMayorController::class);
    Route::get('/librosDiarioMayor/{id}', [LibroMayorController::class, 'showLibroDiario'])->name('showLibroDiario');
    Route::resource('libroDiario', LibroDiarioController::class);
    Route::get('/libros-diarios/{fecha}', [LibroDiarioController::class, 'librosPorFecha'])->name('libros-por-fecha');

    Route::resource('cliente', ClienteController::class);


    Route::get('factura/create/{id}', [FacturaController::class, 'create'])->name('factura.crear');
    Route::get('factura/createnew', [FacturaController::class, 'createNew'])->name('factura.crearnew');

    Route::resource('divisas', DivisaController::class);


    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('inventario/create', [InventarioController::class, 'create'])->name('inventario.create');
    Route::post('inventario', [InventarioController::class, 'store'])->name('inventario.store');
    Route::put('inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
    Route::get('/gift', [InventarioController::class, 'gifts'])->name('inventario.gift');
    Route::post('/gift/{id}', [InventarioController::class, 'sendGift'])->name('inventario.sendGift');
    Route::post('/comegift/{id}', [InventarioController::class, 'comeBackGift'])->name('inventario.comeBackGift');
    Route::get('/gastos', [LibroDiarioController::class, 'gastos'])->name('gastos.index');


    Route::resource('orden', OrdenEntregaController::class);
    Route::get('orden/create/{id}', [OrdenEntregaController::class, 'create'])->name('orden.crear');
    Route::get('alquilado', [InventarioController::class, 'alquilado'])->name('alquilado.index');
    Route::get('disponible', [InventarioController::class, 'disponible'])->name('disponible.index');




});