@extends('layouts.default')
{{-- Web site Title --}}
@section('title') {{{ trans('ext.extensions') }}} :: @parent @stop
{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		{{{ trans('ext.extensions') }}}
		<div class="pull-right">
			<div class="pull-right">
				<a href="{{{ sub_route('exts.create') }}}" class="btn btn-sm  btn-primary iframe">
					<span class="fa fa-plus-circle"></span>
					{{ trans('modal.new') }}
				</a>
			</div>
		</div>
	</h3>
</div>

<table id="table" class="table table-striped table-hover">
	<thead>
		<tr>
			<th data-name="extension">{{{ trans('ext.extension') }}}</th>
            @if( $currentOrg->isMaster() )
            <th data-name="organizationName">{{{ trans('orgs.org') }}}</th>
            @endif
            <th data-name="fullname">{{{ trans('user.name') }}}</th>
            <th data-name="dateEntered">{{{ trans('site.created') }}}</th>
            <th data-name="dateModified">{{{ trans('site.modified') }}}</th>
			<th class="no-sort" data-name="actions">{{{ trans('admin.action') }}}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
@stop
{{-- Scripts --}}
@section('scripts')
@include('partials.scripts.oTable', ['source' => sub_route('exts.index')])
@stop
