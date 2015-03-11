<!DOCTYPE html>
<html lang="en">
<head id="Starter-Site">
    <meta charset="UTF-8">
    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Administration</title>
    <meta name="keywords" content="@yield('keywords')"/>
    <meta name="author" content="@yield('author')"/>
    <!-- Google will often use this as its description of your page/site. Make it good. -->
    <meta name="description" content="@yield('description')"/>
    <!-- Speaking of Google, don't fofficeet to set your site up: http://google.com/webmasters -->
    <meta name="google-site-verification" content="">
    <!-- Dublin Core Metadata : http://dublincore.office/ -->
    <meta name="DC.title" content="Administration">
    <meta name="DC.subject" content="@yield('description')">
    <meta name="DC.creator" content="@yield('author')">
    <!--  Mobile Viewport Fix -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    {{--@include('partials.styles')
    {!! HTML::style('assets/css/style_modal.min.css') !!}--}}
    @yield('before-styles')
    {!! HTML::style('assets/css/base.css') !!}
    @yield('after-styles')

    <!-- start: Favicon and Touch Icons -->
    <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">
    <!-- end: Favicon and Touch Icons -->
</head>
<body class="iframe">
<!-- Container -->
<div class="container">
    @include('flash::message')
    <!-- Content -->
    @yield('content')
    <!-- ./ content -->
</div>
<!-- ./ container -->

<!-- Javascript -->
@include('partials.scripts')
{!! HTML::script('assets/js/modal.js') !!}
@yield('scripts')
</body>
</html>