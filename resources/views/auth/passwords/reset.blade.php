@extends('layouts.auth')

@section('content')
                    
            <form class="sign-in-form" method="POST" action="{{ route('password.request') }}">
                @csrf
                <div class="card">
                    <div class="card-body">
                    @include('includes.alert')
                        <a href="{{ url('/') }}" class="brand text-center d-block m-b-20">
                            <img src="../assets/img/qt-logo%402x.png" alt="" />
                        </a>
                        <h5 class="sign-in-heading text-center m-b-20">Reset Password</h5>
                        <input type="hidden" name="token" value="{{ $token }}">
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

                        <div class="form-group">
                            <label for="inputPassword" class="sr-only">Password</label>
                            <input id="password" type="password" placeholder="Confirm Password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" required>

                            @if ($errors->has('password_confirmation'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-rounded btn-floating btn-lg btn-block" type="submit">Reset Password</button>
                    </div>
                </div>
		</form>
@endsection
