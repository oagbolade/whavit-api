@extends('layouts.email')

@section('content')
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Hi {{ $data['name'] }},
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Thank you for reaching out to us on the {{ $data['type'] }}.
        We understand how tight your schedule is, and we are glad to
        take the burden of cleaning off you.<br><br>

        The {{ $data['type'] }} is an elite subscription for a selected few,
        and the services are customized to your needs. You will be
        given premium treatment and have exclusive access to some
        of our finest home products.<br><br>

        Your assigned Whav Gardener will call you to get all the
        details needed to start your subscription. You can please 
        reply to this email if you have a preferred time we should call.<br><br>

        WhavPremium From Whavit
    </p>
@endsection
