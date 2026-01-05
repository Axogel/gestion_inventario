<?php
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

    Route::get('orden/today', [OrdenEntregaController::class, 'today'])->name('orden.today');

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