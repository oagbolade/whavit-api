@extends('layouts.base')

@section('body')
    <div class="container">
		
		@yield('content')
	</div>
@endsection


@section('dashboard-stylesheets')
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/vendor/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendor/metismenu/dist/metisMenu.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendor/switchery-npm/index.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/icons/line-awesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/icons/dripicons.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/icons/material-design-iconic-font.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/common/main.bundle.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/layouts/vertical/core/main.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/layouts/vertical/menu-type/default.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/layouts/vertical/themes/theme-a.css') }}">
@endsection


@section('dashboard-scripts')
<script src="{{ asset('assets/vendor/modernizr/modernizr.custom.js') }}"></script>
	<script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/js-storage/js.storage.js') }}"></script>
	<script src="{{ asset('assets/vendor/js-cookie/src/js.cookie.js') }}"></script>
	<script src="{{ asset('assets/vendor/pace/pace.js') }}"></script>
	<script src="{{ asset('assets/vendor/metismenu/dist/metisMenu.js') }}"></script>
	<script src="{{ asset('assets/vendor/switchery-npm/index.js') }}"></script>
	<script src="{{ asset('assets/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
	<script src="{{ asset('assets/js/global/app.js') }}"></script>
@endsection