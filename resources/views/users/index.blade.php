@extends( 'layouts.default' )
{{-- Web site Title --}}
@section( 'title' ) {{ trans( 'users.users' ) }} :: @parent @stop
{{-- Content --}}
@section( 'content' )
<div class="page-header">
	<h3>
		{{ trans( 'users.users' ) }}
		<div class="pull-right">
			<div class="pull-right">
				<a href="{{{ sub_route( 'users.create' ) }}}" class="btn btn-sm  btn-primary iframe">
					<span class="fa fa-plus-circle"></span>
					{{ trans( 'modal.new' ) }}
				</a>
			</div>
		</div>
	</h3>
</div>

<table id="table" class="table table-striped table-hover">
	<thead>
		<tr>
			<th>{{ trans( 'users.first_name' ) }}</th>
			<th>{{ trans( 'users.last_name' ) }}</th>
            @if( $currentOrg->isVendor() )
			<th>{{ trans( 'orgs.organization' ) }}</th>
            @endif
			<th>{{ trans( 'users.email' ) }}</th>
			<th>{{ trans( 'users.active' ) }}</th>
			<th>{{ trans( 'admin.admin' ) }}</th>
			<th>{{ trans( 'admin.created_at' ) }}</th>
			<th class="no-sort">{{ trans( 'admin.action' ) }}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
@stop
{{-- Scripts --}}
@section( 'scripts' )
@include( 'partials.scripts.oTable', [ 'source' => sub_route( 'users.index' ) ] )
@stop
