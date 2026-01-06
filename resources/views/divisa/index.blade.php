@extends('layouts.master')
@section('css')
    <!-- Select2 css -->
    <link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
    <!--Page header-->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Divisa</h4>
        </div>
        <div class="page-rightheader ml-auto d-lg-flex d-none">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="d-flex"><svg class="svg-icon"
                            xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3zm5 15h-2v-6H9v6H7v-7.81l5-4.5 5 4.5V18z" />
                            <path d="M7 10.19V18h2v-6h6v6h2v-7.81l-5-4.5z" opacity=".3" />
                        </svg><span class="breadcrumb-icon"> Dashboard</span></a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventario.index') }}">Site Panel</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Site</li>
            </ol>
        </div>
    </div>
    <!--End Page header-->
@endsection
@section('content')
    <!-- Row -->
    @if(Session::has('success'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{Session::get('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Actualizar tasa de divisas - Ref Cop</div>
                </div>
                <div class="card-body">
                    @foreach($divisas as $tasa)
                        @if($tasa->name != "COP")

                            <form class="p-5" method="POST" action="{{ route('divisas.update', $tasa->id) }}" role="form">
                                {{ csrf_field() }}
                                @method('PUT')

                                <div class="mb-5">
                                    <span class="card-title"> {{$tasa->name}}</span>
                                    <div class="w-20 my-2">
                                        <input type="numeric" name="tasa" id="tasa" class="form-control input-sm"
                                            placeholder="{{$tasa->name}}" value="{{$tasa->tasa}}">

                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <button type="submit" class="btn btn-lg btn-primary">Actualizar</button>
                                </div>
                            </form>
                        @endif


                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- End Row-->

    </div>
    </div><!-- end app-content-->
    </div>
@endsection
@section('js')
    <!--Select2 js -->

    <script src="{{URL::asset('assets/plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/select2.js')}}"></script>
@endsection