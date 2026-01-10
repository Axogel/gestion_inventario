<!--aside open-->
<div class="app-sidebar app-sidebar2">
					<div class="app-sidebar__logo">
						<a class="header-brand" href="{{ route('index') }}">
							<img src="{{URL::asset('assets/images/brand/logo.png')}}" style="height: 5.8rem"  class="header-brand-img desktop-lgo" alt="Covido logo">

						</a>
					</div>
				</div>
				<aside class="app-sidebar app-sidebar3">
			
                    <ul class="side-menu">
						<li class="slide">
                            <a class="side-menu__item" href="{{ route('dashboardgrap') }}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hor-icon"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                <span class="side-menu__label">Inicio</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
                        </li>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Inventario</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{ route('inventario.index') }}"><span>Inventario</span></a></li>
								<li><a class="slide-item" href="{{ route('inventario.create') }}"><span>Agregar Producto</span></a></li>
								<li><a class="slide-item" href="{{ route('movement.index') }}"><span>Historial</span></a></li>
								<li><a class="slide-item" href="{{ route('donation') }}"><span>Uso de Oficina (Donaciones)</span></a></li>





							</ul>
						</li>
						
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Caja</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{ route('box.create') }}"><span>Caja de Hoy</span></a></li>
								<li><a class="slide-item" href="{{ route('orden.create') }}"><span>Crear Orden</span></a></li>
								<li><a class="slide-item" href="{{ route('orden.index') }}"><span>Devoluciones</span></a></li>

							</ul>
						</li>


								<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Administrativo</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
							<ul class="slide-menu">
								 @if( Auth::user()->isSuper())

									<li><a class="slide-item" href="{{ route('box.index') }}"><span>Historial administrativo</span></a></li>
									<li><a class="slide-item" href="{{ route('expenses.index') }}"><span>Gastos</span></a></li>
									<li><a class="slide-item" href="{{ route('deudores') }}"><span>Deudores</span></a></li>
								@endif
								<li><a class="slide-item" href="{{ route('divisas.create') }}"><span>Divisas</span></a></li>


							</ul>
						</li>

						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Servicios</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{ route('services.index') }}"><span>Lista de Servicios</span></a></li>
	
							</ul>
						</li>

						@if(Auth::user()->isSuper())

						<li class="slide">
							<a class="side-menu__item" href="{{ route('cliente.index') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Clientes</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
						</li>
						                        <li class="slide">
                            <a class="side-menu__item" href="{{ route('users.index') }}">
                                <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span class="side-menu__label">Usuarios</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
                        </li>
						@endif


				
				</aside>

