@extends('layouts.master')

@section('title') {{ l('Addresses - Edit') }} @parent @stop


@section('content')

<div class="row">
	<div class="col-md-8 col-md-offset-2" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading">
		          <h3 class="panel-title">Modificar Dirección: ({{$address->id}}) {{$address->alias}}</h3>
		          <h3 class="panel-title" style="margin-top:10px;">Pertenece a: ({{ $model_name }} {{$customer->id}}) {{$customer->name_fiscal}}</h3>
	      	</div>
			<div class="panel-body"> 

        		@include('errors.list')

				{!! Form::model($address, array('method' => 'PATCH', 'route' => array('addresses.update', $address->id))) !!}

					{{-- !! Form::hidden('model_name', $model_name, array('id' => 'model_name')) !! --}}

         			@include('addresses._form')

				{!! Form::close() !!}

			</div>
		</div>
	</div>
</div>

@stop