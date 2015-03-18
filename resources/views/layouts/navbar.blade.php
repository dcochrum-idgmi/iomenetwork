<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            {!! HTML::link( url(), 'Iome', [ 'class' => 'navbar-brand' ] ) !!}
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav">
                <span class="sr-only">Toggle navigation</span> <span
                        class="icon-bar"></span> <span class="icon-bar"></span> <span
                        class="icon-bar"></span>
            </button>
        </div>
        <div id="main-nav" class="navbar-collapse collapse">
            <ul class="nav navbar-nav pull-right">
                @if (Auth::check())
                    @if (Auth::user()->isAdmin())
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="{!! admin_route('dashboard') !!}" data-toggle="dropdown"
                               role="button" aria-expanded="false">
                                {!! trans('site.admin_panel') !!}
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                @if (Auth::user()->isVendorAdmin())
                                    <li>{!! HTML::link(admin_route('dashboard'), trans('admin.dashboard')) !!}</li>
                                    <li>{!! HTML::link(admin_route('offices.index'), trans('offices.offices')) !!}</li>
                                @else
                                    <li>{!! HTML::link(sub_route('settings'), trans('site.settings')) !!}</li>
                                    <li>{!! HTML::link(sub_route('exts.index'), trans('exts.extensions')) !!}</li>
                                @endif
                                <li>{!! HTML::link(sub_route('users.index'), trans('users.users')) !!}</li>
                                <li>{!! HTML::link(sub_route('exts.index'), trans('ext.extensions')) !!}</li>
                            </ul>
                        </li>
                        @if (isset($currentOffice) && Auth::user()->isVendorAdmin())
                            <li>{!! HTML::link(sub_route('settings'), trans('site.settings')) !!}</li>
                        @endif
                    @endif
                    <li>{!! HTML::link((Auth::user()->isVendorAdmin() ? sub_route('users.edit', Auth::user()->getRouteKey() ) :
                        sub_route('profile.edit')), trans('site.login_as') . ' ' . Auth::user()->fname ) !!}
                    </li>
                    <li><a href="{{{ route('logout') }}}">{{{ trans('site.logout') }}}</a></li>
                @else
                    <li>{!! HTML::link(route('login'), trans('site.login'), ['class' => (Request::is('login') ? 'active'
                        : '')]) !!}
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>