@extends('layouts.email')

@section('content')
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Hello {{ $first_name }} {{ $last_name }},
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Your payment is received and WE’RE GLAD YOU BOOKED 😊
        By the time our Professional is done, your home will not only be clean,
        but it will also be hygienic.
        We are big on hygiene and the small details.
        For us, a spotless house is just the side effect.
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Your WhavPro will arrive shortly and we wanted to let you know what to expect.<br>
        Our WhavPros complete outfit<br>
        - Nose Mask <br>
        - Whav Tee <br>
        - Gloves <br>
        - Hygiene Swag Bag <br>
        - Crocs <br>
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Please ensure to check for all these before allowing the WhavPro into your home, anyone without
        the complete outfit is not from us, and ensure to always rate your pros.<br><br>

        We’ll like that basics like buckets, bailers and water be available for the WhavPro to work with.<br><br>

        While you wait, you can take a look at our <a href="https://whavit.com/faqs">Frequently Asked Questions</a>, or, if your request is more urgent, feel free to give us a call at (+234)9092434742.
        <br><br>
        Other than that, it’s cleaning time 😊!<br>
        Team Whavit.
    </p>
@endsection
