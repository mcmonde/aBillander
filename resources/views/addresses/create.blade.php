@extends('layouts.master')

@section('title') {{ l('Addresses - Create') }} @parent @stop


@section('content')

<div class="row">
	<div class="col-md-6 col-md-offset-3" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">{{ l('New Address') }}</h3>
          		<h3 class="panel-title" style="margin-top:10px;">Pertenece a: ({{ $model_name }} {{$customer->id}}) {{$customer->name_fiscal}}</h3>
			</div>
			<div class="panel-body">

				@include('errors.list')

				{!! Form::open(array('route' => 'addresses.store')) !!}

					{!! Form::hidden('model_name', $model_name, array('id' => 'model_name')) !!}
					{!! Form::hidden('owner_id', $owner_id, array('id' => 'owner_id')) !!}

					@include('addresses._form')

				{!! Form::close() !!}
				
			</div>
		</div>
	</div>
</div>

@stop