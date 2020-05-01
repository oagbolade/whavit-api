@extends('layouts.auth')

@section('content')
            <form class="sign-in-form" method="POST" action="{{ route('password.email') }}">
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

                        <button class="btn btn-primary btn-rounded btn-floating btn-lg btn-block" type="submit">{{ __('Send Password Reset Link') }}</button>
                        <p class="text-muted text-center m-t-25 m-b-0 p-0">Don't have an account yet?<a href="{{ url('register') }}"> Register</a></p>                    
                    </div>
                </div>
		</form>   
                
@endsection
