@extends('layouts.master')

@section('title') {{ l('Combinations - Edit') }} @parent @stop


@section('content')

<div class="row">
	<div class="col-md-8 col-md-offset-2" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading">
          <h3 class="panel-title">Producto: ({{$product->id}}) {{$product->name}}</h3>
          <h3 class="panel-title" style="margin-top:12px;">{{ l('Edit Combination') }}: ({{$combination->id}}) {{$combination->name()}}</h3>
      </div>
			<div class="panel-body"> 

        @include('errors.list')

				{!! Form::model($combination, array('method' => 'PATCH', 'route' => array('combinations.update', $combination->id))) !!}

          <div class="row">
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('reference', l('Reference')) !!}
              {!! Form::text('reference', null, array('class' => 'form-control')) !!}
          </div>
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('ean13', l('Ean13')) !!}
              {!! Form::text('ean13', null, array('class' => 'form-control')) !!}
          </div>
          </div>

          <div class="row">
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('location', l('Location')) !!}
              {!! Form::text('location', null, array('class' => 'form-control')) !!}
          </div>

          <div class="form-group col-lg-2 col-md-2 col-sm-2">
          </div>

          <div class="form-group col-lg-4 col-md-4 col-sm-4" id="div-active">
           {!! Form::label('active', l('Active?', [], 'layouts'), ['class' => 'control-label']) !!}
           <div>
             <div class="radio-inline">
               <label>
                 {!! Form::radio('active', '1', true, ['id' => 'active_on']) !!}
                 {!! l('Yes', [], 'layouts') !!}
               </label>
             </div>
             <div class="radio-inline">
               <label>
                 {!! Form::radio('active', '0', false, ['id' => 'active_off']) !!}
                 {!! l('No', [], 'layouts') !!}
               </label>
             </div>
           </div>
          </div>

          </div>



        <div class="row">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12">
                      {{ l('Notes', [], 'layouts') }}
                      {!! Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '3')) !!}
                  </div>
        </div>

        {!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
        {!! link_to( ('products/' . $product->id . '/edit#combinations'), l('Cancel', [], 'layouts'), array('class' => 'btn btn-warning')) !!}
	

        {!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@stop