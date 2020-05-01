@extends('layouts.email')

@section('content')

    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Hi {{ $first_name }} {{ $last_name }},
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        You just accepted an order for the day!<br>
        Please ensure you are in your complete outfit before going for the cleaning. Your complete outfit includes:<br>
        - Nose Mask<br>
        - Whav Tee<br>
        - Gloves<br>
        - Hygiene Swag Bag<br>
        - Crocs<br>
    </p>
    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
        Please ensure to check for all these before going to clean as requested, without a
        the complete outfit, you will not be allowed to work by the customer, and you'll be rated based on your appearance.
        Ensure you get there on time, and request for the basics like buckets, bailers and water.<br><br>

        If you encounter any issues, kindly call our helpline 09092434742 or send a mail to admin@whavit.com.
        Thank you for choosing Whavit.
    </p>
@endsection
