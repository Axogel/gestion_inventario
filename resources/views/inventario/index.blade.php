@extends('layouts.master')
@section('css')
<!-- Data table css -->
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}"  rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<!-- Slect2 css -->
<link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
						<!--Page header-->
						<div class="page-header">
							<div class="page-leftheader">
								<h4 class="page-title">Inventario</h4>
							</div>
						</div>
						<!--End Page header-->
@endsection
@section('content')
                        <!--Row-->
                        @if(Session::has('success'))
                            <div class="alert alert-{{ session('success.alert') }} alert-dismissible fade show" role="alert">
                                {{ session('success.message') }}

                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

						<div class="row row-deck">
							<div class="col-xl-12 col-lg-12 col-md-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title"></h3>
                                        <div class="form-group">
                                                <label for="fecha">Buscar:</label>
                                                <input type="text" id="search" name="search" class="form-control" placeholder="buscador">
                                            </div>
                                            <div class="card-options d-flex flex-wrap  flex-column flex-sm-row">
                                                <div class="btn-group ml-5 mb-2">
                                                <form id="searchForm" action="{{ route('exportInventario') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="searchTerm" id="searchTerm">
                                                    <input  class="btn btn-primary btn-responsive" type="submit" value="Descargar Excel">
                                                </form>
                                                </div>
                                                <div class="btn-group ml-5 mb-2">
                                                    <a class="btn btn-primary  btn-responsive" data-target="#modaldemo1" data-toggle="modal" href="">
                                                        <i class="fa fa-plus mr-2"></i>Añadir Excel
                                                    </a>
                                                </div>
                                                <div class="btn-group ml-5 mb-2">
                                                    <a class="btn btn-primary  btn-responsive" href="{{ route('inventario.create') }}">
                                                        <i class="fa fa-plus mr-2"></i>Añadir un Producto
                                                    </a>
                                                </div>
                                            </div>
                                            
									</div>
									<div class="card-body">
										<div class="table-responsive">
                                            <table id="table" class=" table table-bordered text-nowrap key-buttons" style="border-color:#eff0f6;">
                                                <thead style="border-color:#eff0f6;">
                                                    <th class="border-bottom-0">Codigo</th>
                                                    <th class="border-bottom-0">Producto</th>
                                                    <th class="border-bottom-0">marca</th>
                                                    <th class="border-bottom-0">talla</th>
                                                    <th class="border-bottom-0">tipo</th>
                                                    <th class="border-bottom-0">precio</th>
                                                    <th class="border-bottom-0">almacen</th>

                                                    <th class="border-bottom-0">color</th>
                                                    <th class="border-bottom-0">estado</th>
                                                    <th class="border-bottom-0">alquiler</th>
                                                    <th style="border-color:#eff0f6;"></th>
                                                    <th style="border-color:#eff0f6;"></th>
                                                    <th style="border-color:#eff0f6;"></th>

                                                    @if(Auth::user()->isSuper())

                                                    <th style="border-color:#eff0f6;"></th>
                                                    <th style="border-color:#eff0f6;"></th>
                                                    @endif
                                                </thead>
                                                <tbody id="contentTable">
                                                    @if($inventario->isNotEmpty())
                                                        @foreach($inventario as $producto)
                                                            <tr class="bold producto-row">
                                                                <td>{{$producto->codigo}}</td>
                                                                <td>{{$producto->producto}}</td>
                                                                <td>{{$producto->marca}}</td>
                                                                <td>{{$producto->talla}}</td>
                                                                <td>{{$producto->tipo}}</td>
                                                                <td>{{$producto->precio}}</td>
                                                                <td>{{$producto->almacen}}</td>

                                                                <td>{{$producto->color}}</td>
                                                                <td>
                                                                    @if($producto->disponibilidad == 1)
                                                                        Disponible
                                                                    @elseif($producto->disponibilidad == 2)
                                                                        Vendido
                                                                    @elseif($producto->disponibilidad == 3)
                                                                        Prestado o regalado
                                                                    @else
                                                                        Alquilado
                                                                    @endif
                                                                </td>


                                                                <td @if($producto->alquiler && strtotime($producto->alquiler) < strtotime(now()->subDays(3))) class="bg-danger text-white" @endif>
                                                                    {{$producto->alquiler}}
                                                                </td>


                                                                <td>
                                                                @if($producto->disponibilidad ==1)
                                                                    <a class="btn btn-success btn-xs" href="{{ route('orden.crear', ['id' => $producto->codigo]) }}" ><span class=" fa fa-money"></span> </a>
                                                                    @else 
                                                                    <a class="btn btn-dark btn-xs" href="#"  style="cursor: not-allowed"><span class=" fa fa-money"></span> </a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                <form id="giftForm" action="{{ route('inventario.sendGift', ['id' => $producto->codigo]) }}" method="post">
                                                                    {{ csrf_field() }}
                                                                    {{ method_field('POST') }}
                                                                    <button class="btn btn-info btn-xs" onclick="gift()" type="button"><span class=" fa fa-gift"></span></button>
                                                                </form>
                                                                </td>
                                                                @if(Auth::user()->isSuper())


                                                                <td><a class="btn btn-primary btn-xs" href="{{ route('inventario.edit', ['id' => $producto->codigo]) }}" ><span class="fa fa-pencil"></span></a></td>
                                                                <td>
                                                                <form id="deleteForm" action="{{ route('inventario.destroy', ['id' => $producto->codigo]) }}" method="delete">
                                                                    {{ csrf_field() }}
                                                                    {{ method_field('DELETE') }}
                                                                    <button class="btn btn-danger btn-xs" onclick="confirmDelete()" type="button"><span class="fa fa-trash"></span></button>
                                                                </form>
                                                                </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="8">No one there!</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
						<!--End row-->
					</div>
				</div><!-- end app-content-->
            </div>
            <div class="modal" id="modaldemo1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-content-demo">
                        <div class="modal-header">
                            <h6 class="modal-title">Importar Archivo</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('inventario.import') }}" role="form" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="custom-file">
                                            <input type="file" id="excel" name="excel" class="custom-file-input p-2" data-height="250" accept=".xlsx, .xls, .csv" onchange='openFile(event)'/>
                                            <label class="custom-file-label">Elegir Archivo Excel, Csv</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 p-3">
                                    <button type="submit" class="btn btn-lg btn-primary">Importar</button>
                                </div>
                            </form>
    
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-light" data-dismiss="modal" type="button">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
@endsection
@section('js')
<!-- Data tables -->
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/js/datatables.js')}}"></script>
<!-- Select2 js -->
<script src="{{URL::asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<script>
    function confirmDelete() {
        if (confirm("¿Está seguro de que desea eliminar este elemento?")) {
            document.getElementById('deleteForm').submit();
        }
    }
   async function gift() {
    if (confirm("¿Está seguro de que desea regalar o prestar este elemento?")) {  
        document.getElementById('giftForm').submit();
    }
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const productoRows = document.querySelectorAll('.producto-row');

        searchInput.addEventListener('input', function () {
            const searchTerm = searchInput.value.toLowerCase();

            document.getElementById("searchTerm").value =  searchTerm;

            productoRows.forEach(function (row) {
                const textoFila = row.innerText.toLowerCase();

                if (textoFila.includes(searchTerm)) {
                    row.style.display = ''; 
                } else {
                    row.style.display = 'none'; 
                }
            });
        });
    });
</script>


<script type="text/javascript">
    var openFile = function(event) {
        var input = event.target;

        var reader = new FileReader();
        reader.onload = function(){
            var dataURL = reader.result;
            console.log(dataURL);
            xmlDoc = $.parseXML(dataURL),
            $xml = $(xmlDoc),
            $('#xmltext').val(dataURL);
        };
        reader.readAsText(input.files[0]);
    };
</script>

<style>
    .btn-responsive {
        width: 200px;
    }
</style>
@endsection
