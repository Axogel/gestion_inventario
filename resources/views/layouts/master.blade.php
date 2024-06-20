<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
	<head>
		<!-- Meta data -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        @include('layouts.head')
	</head>

	<body class="app sidebar-mini light-mode default-sidebar">
		<!---Global-loader-->
		<div id="global-loader" >
			<img src="{{URL::asset('assets/images/svgs/loader.svg')}}" alt="loader">
		</div>

		<div class="page">
			<div class="page-main">
				@include('layouts.side-menu')
				<div class="app-content main-content">
					<div class="side-app">
						@include('layouts.header')
						@yield('page-header')
						@yield('content')
            			@include('layouts.footer')
		</div><!-- End Page -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
			@include('layouts.footer-scripts')
		<style>
			.main-content{
				background-image: url('{{ asset('assets/images/brand/fondoCY.jpg') }}') !important;
				background-size: cover;

			}
		</style>
		</body>
</html>
