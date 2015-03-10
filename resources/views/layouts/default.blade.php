<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>@section( 'title' ) Iome @show</title>
    @section( 'meta_keywords' )
        <meta name="keywords" content="">
    @show @section( 'meta_author' )
        <meta name="author" content="David Cochrum, Macate Group, Inc.">
    @show @section( 'meta_description' )
        <meta name="description" content="">
        @show
                <!-- Mobile Specific Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @yield( 'before-styles' )
        {!! HTML::style( 'assets/css/base.css' ) !!}
        {!! HTML::style( 'assets/css/layout.css' ) !!}
        @yield( 'after-styles' )

        <link rel="shortcut icon" href="{{{ asset( 'favicon.ico' ) }}}">
</head>

<body>
<div id="wrap">
    @include( 'layouts.navbar' )

    <div class="container">
        @include( 'flash::message' )
        @yield( 'content' )
    </div>
    <div id="push"></div>
</div>
@include( 'layouts.footer' )

<!-- Javascript -->
@include( 'partials.scripts' )
{!! HTML::script( 'assets/js/app.js' ) !!}

@yield( 'scripts' )
</body>
</html>
