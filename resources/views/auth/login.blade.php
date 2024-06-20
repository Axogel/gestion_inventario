@extends('layouts.master2')
@section('css')
@endsection
@section('content')
		<div class="page">
			<div class="page-single">
				<div class="container">
					<div class="row">
						<div class="col mx-auto">
							<div class="row justify-content-center">
								<div class="col-md-8">
									<div class="card card-group mb-0">
										<div class="card p-4">
											<div class="card-body">
												<div class="text-center title-style mb-6">
													<h1 class="mb-2">{{ __('Login') }}</h1>
													<hr>
													<p class="text-muted">Sign In to your account</p>
                                                </div>
                                                <form method="POST" action="{{ route('login') }}" method="post">
                                                    @csrf
                                                    <!--
                                                    <div class="btn-list d-sm-flex">
                                                        <a href="https://www.google.com/gmail/" class="btn btn-google btn-block">Google</a>
                                                        <a href="https://twitter.com/" class="btn btn-twitter d-block d-sm-inline mr-0 mr-sm-2">Twitter</a>
                                                        <a href="https://www.facebook.com/" class="btn btn-facebook d-block d-sm-inline">Facebook</a>
                                                    </div>
                                                    <hr class="divider my-6">
                                                    -->
                                                    <div class="form-group row">
                                                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                                        <div class="col-md-6">
                                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                                            @error('email')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                                        <div class="col-md-6">
                                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                                            @error('password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                      

                                                    <div class="form-group row mb-0">
                                                        <div class="col-md-12 offset-md-4">
                                                            <button type="submit" class="btn btn-primary w-40">
                                                                {{ __('Login') }}
                                                            </button>

                       
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
										</div>
									</div>

								</div>
							</div>
						</div>
                    </div>
				</div>
			</div>
        </div>
        <style>
            .page{
                background-image: url('{{ asset('assets/images/brand/fondoCY.jpg') }}') !important;
                background-size: cover;
            }
        </style>
@endsection
@section('js')
@endsection
