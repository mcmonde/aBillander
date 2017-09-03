@extends('layouts.master')

@section('title') {{ l('Taxes Rules - Create') }} :: @parent @stop


@section('content')

<div class="row">
	<div class="col-md-6 col-md-offset-3" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading"><h3 class="panel-title"><strong>{{ $tax->name }}</strong> :: {{ l('New Tax Rule') }}</h3></div>
			<div class="panel-body">

				@include('errors.list')

				{!! Form::open(array('route' => array('taxes.taxrules.store', $tax->id))) !!}

					@include('tax_rules._form')

				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@stop