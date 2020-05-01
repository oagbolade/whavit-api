@extends('layouts.email')

@section('content')
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
        <h3>Hello Whavit Rep,</h3>
        <b>{{ $data['name'] }}</b> has a request for {{ $data['type'] }}. Kindly find the content of the request below.
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
        <h3>Name:</h3> {{ $data['name'] }} <br>
        <h3>Email:</h3> {{ $data['email'] }} <br>
        <h3>Phone:</h3> {{ $data['phone'] }} <br>
        <h3>Message:</h3> {{ $data['message'] }} <br><br>
    </p>
@endsection
