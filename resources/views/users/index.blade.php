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
			<th data-name="fname">{{ trans( 'users.first_name' ) }}</th>
			<th data-name="lname">{{ trans( 'users.last_name' ) }}</th>
            @if( $currentOrg->isMaster() )
			<th data-name="organizationName">{{ trans( 'orgs.org' ) }}</th>
            @endif
			<th data-name="username">{{ trans( 'users.email' ) }}</th>
			<th data-name="role">{{ trans( 'users.role' ) }}</th>
			<th data-name="dateEntered">{{ trans( 'site.created' ) }}</th>
			<th data-name="dateModified">{{ trans( 'site.modified' ) }}</th>
			<th class="no-sort" data-name="actions">{{ trans( 'admin.action' ) }}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
@stop
{{-- Scripts --}}
@section( 'scripts' )
@include( 'partials.scripts.oTable', [ 'source' => sub_route( 'users.index' ) ] )
@stop
