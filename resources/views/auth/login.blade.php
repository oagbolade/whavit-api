@extends('layouts.auth')

@section('content')
            <form class="sign-in-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="card">
                    <div class="card-body">
                    @include('includes.alert')
                        <a href="{{ url('/') }}" class="brand text-center d-block m-b-20">
                            <img src="../assets/img/qt-logo%402x.png" alt="" />
                        </a>
                        <h5 class="sign-in-heading text-center m-b-20">Sign in to your account</h5>
                        <div class="form-group">
                            <label for="inputEmail" class="sr-only">Email address</label>
                            <input id="email" type="email" placeholder="Email address" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

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
                        <div class="checkbox m-b-10 m-t-20">
                            <div class="custom-control custom-checkbox checkbox-primary form-check">
                                <input type="checkbox" class="custom-control-input" id="stateCheck1" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="stateCheck1">	Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="float-right">Forgot Password?</a>
                        </div>
                        <button class="btn btn-primary btn-rounded btn-floating btn-lg btn-block" type="submit">Sign In</button>
                        <p class="text-muted text-center m-t-25 m-b-0 p-0">Don't have an account yet?<a href="{{ url('register') }}"> Register</a></p>                    
                    </div>
                    <div>
                        <div class="col-md-6 col-md-offset-4">
                            <a href="{{ url('/auth/github') }}" class="btn btn-github"><i class="fa fa-github"></i> Github</a>
                            <a href="{{ url('/auth/twitter') }}" class="btn btn-twitter"><i class="fa fa-twitter"></i> Twitter</a>
                            <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook"><i class="fa fa-facebook"></i> Facebook</a>
                        </div>
                    </div>
                </div>
		</form>
@endsection
