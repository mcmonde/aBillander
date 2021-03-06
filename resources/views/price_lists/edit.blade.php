@extends('layouts.master')

@section('title') {{ l('Price Lists - Edit') }} @parent @stop


@section('content')

<div class="row">
	<div class="col-md-6 col-md-offset-3" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading"><h3 class="panel-title">{{ l('Edit Price List') }} :: ({{$pricelist->id}}) {{$pricelist->name}}</h3></div>
			<div class="panel-body">

        @include('errors.list')

        {!! Form::model($pricelist, array('method' => 'PATCH', 'route' => array('pricelists.update', $pricelist->id))) !!}

          @include('price_lists._form')

        {!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@stop
