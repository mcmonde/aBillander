@extends('layouts.master')

@section('title') {{ l('Price Lists - Create') }} @parent @stop


@section('content')

<div class="row">
	<div class="col-md-4 col-md-offset-4" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading"><h3 class="panel-title">{{ l('New Price List') }}</h3></div>
			<div class="panel-body">

        @include('errors.list')

        {!! Form::open(array('route' => 'pricelists.store')) !!}

          @include('price_lists._form')

        {!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')

  @include('price_lists.js.pricelist_js')

@stop