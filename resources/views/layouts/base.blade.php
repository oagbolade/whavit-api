<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ config('app.name') }} - @yield('title')</title>
        <!-- Check viewport -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="">
        <meta name="author" content="@sprinble">
        <meta name="twitter" content="@sprinble" />
        <meta name="instagram" content="@sprinble" />
        <meta name="github" content="@sprinble" />
        <meta name="dribble" content="@sprinble" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="{{ config('app.name') }} - " />
        <meta property="og:description" content="" />
        <meta property="og:url" content="https://.com/" />
        <meta property="og:site_name" content="{{ config('app.name') }}" />
        <meta property="og:image" content="{{ asset('assets/img/logo/logo.png') }}" />
        <meta property="og:image:secure_url" content="{{ asset('assets/img/logo/favicon.png') }}" />
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:image" content="twitter image url here" />
        <meta name="twitter:creator" content="@sprinble" />
        <meta name="twitter:description" content="" />
        <meta name="twitter:title" content="{{ config('app.name') }} - " />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <link rel="shortcut icon" href="{{ asset('assets/img/logo/favicon.png') }}" >

        @yield('stylesheets')

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:200,300,400,400i,500,600,700%7CMerriweather:300,300i" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script src="https://use.fontawesome.com/2832c7fb97.js"></script>   
    
        @yield('dashboard-stylesheets')

        <!-- Google Analytics code here -->

    </head>
    <body class="">
        
        @yield('body')

        <script type="text/javascript" href="{{ asset('dist/js/app.js') }}"></script>

        @yield('dashboard-scripts')

        <!-- Mailchimp here -->

        <!-- Drift here -->       
    </body>
</html>