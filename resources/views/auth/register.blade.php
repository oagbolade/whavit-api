@extends('layouts.auth')

@section('content')

<form class="sign-in-form" method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
        @csrf
			<div class="card">
				<div class="card-body">
				@include('includes.alert')
					<a href="{{ url('/') }}" class="brand text-center d-block m-b-20">
						<img src="../assets/img/qt-logo%402x.png" alt="" />
					</a>
					<h5 class="sign-in-heading text-center m-b-20">Create an account</h5>
					<div class="form-group">
						<label for="inputEmail" class="sr-only">First Name</label>
                        <input id="name" type="text" placeholder="First Name" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" required autofocus>
                        @if ($errors->has('first_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="form-group">
						<label for="inputEmail" class="sr-only">Email address</label>
                        <input id="email" type="email" placeholder="Email address" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="form-group">
						<label for="inputPassword" class="sr-only">Password</label>
                        <input id="password" type="password" placeholder="Password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="checkbox m-b-10 m-t-15">
						<div class="custom-control custom-checkbox checkbox-primary form-check">
							<input type="checkbox" class="custom-control-input" id="stateCheck1" checked="">
							<label class="custom-control-label" for="stateCheck1">	I accept the <a href="javascript:void(0)">terms and conditions</a></label>
						</div>
					</div>
					<button class="btn btn-primary btn-rounded btn-floating btn-lg btn-block" type="submit">Create My Account</button>
					 <p class="text-muted text-center m-t-25 m-b-0 p-0">Already have an account?<a href="{{ url('login') }}"> Sign In</a></p>
				</div>

			</div>
		</form>
                
@endsection
