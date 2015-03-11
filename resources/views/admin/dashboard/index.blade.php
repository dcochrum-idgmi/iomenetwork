@extends( 'layouts.default' ) {{-- Web site Title --}}
@section( 'title' ) {{{ $title }}} :: @parent @stop {{-- Content --}}
@section( 'content' )
@include( 'layouts.page_header', [ 'page_header' => $title ] )
<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-sitemap fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">{{ $offices }}</div>
						<div>{{ trans( 'offices.offices' ) }}!</div>
					</div>
				</div>
			</div>
			<a href="{{ route( 'offices.index' )}}">
				<div class="panel-footer">
					<span class="pull-left">{{ trans( 'admin.view_detail' ) }}</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-success">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-phone fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">{{ $exts }}</div>
						<div>{{ trans( 'ext.extensions' ) }}!</div>
					</div>
				</div>
			</div>
			<a href="{{ sub_route( 'exts.index' ) }}">
				<div class="panel-footer">
					<span class="pull-left">{{ trans( 'admin.view_detail' ) }}</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-user fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">{{ $users }}</div>
						<div>{{ trans( 'users.users' ) }}!</div>
					</div>
				</div>
			</div>
			<a href="{{ sub_route( 'users.index' ) }}">
				<div class="panel-footer">
					<span class="pull-left">{{ trans( 'admin.view_detail' ) }}</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
</div>
@stop
