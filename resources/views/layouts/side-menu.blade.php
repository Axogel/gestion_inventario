<!--aside open-->
<div class="app-sidebar app-sidebar2">
					<div class="app-sidebar__logo">
						<a class="header-brand" href="{{ route('index') }}">
							<img src="{{URL::asset('assets/images/brand/logo.png')}}" style="height: 3.8rem"  class="header-brand-img desktop-lgo" alt="Covido logo">
							<img src="{{URL::asset('assets/images/brand/logo1.png')}}" class="header-brand-img dark-logo" alt="Covido logo">
							<img src="{{URL::asset('assets/images/brand/favicon.png')}}" class="header-brand-img mobile-logo" alt="Covido logo">
							<img src="{{URL::asset('assets/images/brand/favicon1.png')}}" class="header-brand-img darkmobile-logo" alt="Covido logo">
						</a>
					</div>
				</div>
				<aside class="app-sidebar app-sidebar3">
					<div class="app-sidebar__user">
						<div class="dropdown user-pro-body text-center">
							<div class="user-pic">
								@if (Auth::user())
								<img src="../img/profile/{{ Auth::user()->profile_photo }}" alt="user-img" class="avatar-xl rounded-circle mb-1">

								@endif
							</div>
							<div class="user-info">
								@if (Auth::user())
								<h5 class=" mb-1 font-weight-bold">{{ Auth::user()->name }} 
								</h5>
								@endif
							
								{{-- <span class="text-muted app-sidebar__user-name text-sm">App de Inventario</span> --}}
							</div>
						</div>
					</div>
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
								<li><a class="slide-item" href="{{ route('alquilado.index') }}"><span>Alquilado</span></a></li>
								<li><a class="slide-item" href="{{ route('disponible.index') }}"><span>Disponible</span></a></li>
								<li><a class="slide-item" href="{{ route('inventario.gift') }}"><span>Regalos o Prestamos</span></a></li>

								<li><a class="slide-item" href="{{ route('cliente.index') }}"><span>Clientes</span></a></li>

								<li><a class="slide-item" href="{{ route('orden.index') }}"><span>Ordenes</span></a></li>
								<li><a class="slide-item" href="{{ route('factura.index') }}"><span>Facturas</span></a></li>



							</ul>
						</li>
						
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Caja</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{route('gastos.index')}}"><span>Gastos</span></a></li>
								<li><a class="slide-item" href="{{ route('libros-por-fecha', ['fecha' => now()->format('Y-m-d')]) }}"><span>Caja de Hoy</span></a></li>

							</ul>
						</li>
                        @if( Auth::user()->isSuper())
                        <li class="slide">
                            <a class="side-menu__item" href="{{ route('users.index') }}">
                                <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span class="side-menu__label">Usuarios</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
                        </li>
						@endif

				
						@if(Auth::user()->isAdmin())

					
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Administracion</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{route('divisas.create')}}"><span>Divisas</span></a></li>
								<li><a class="slide-item" href="{{route('libroMayor.index')}}"><span>Libro Mayor</span></a></li>

								<li><a class="slide-item" href="{{route('libroDiario.index')}}"><span>Libro Diario</span></a></li>


							</ul>
						</li>



                        @endif
						@if(Auth::user()->isSuper())

						<li class="slide">
							<a class="side-menu__item" href="{{ route('oferta.index') }}">
							    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                                <span class="side-menu__label">Promociones</span><i class="side-menu__icon angle fa fa-angle-right"></i>
                            </a>
						</li>
						@endif


				
				</aside>

