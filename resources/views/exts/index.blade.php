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
			<th>{{{ trans('ext.extension') }}}</th>
            @if( $currentOffice->isVendor() )
            <th>{{{ trans('offices.office') }}}</th>
            @endif
            <th>{{{ trans('ext.mac') }}}</th>
            <th>{{{ trans('admin.created_at') }}}</th>
			<th class="no-sort">{{{ trans('admin.action') }}}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
@stop
{{-- Scripts --}}
@section('scripts')
@include('partials.scripts.oTable', ['source' => sub_route('exts.index')])
@stop
