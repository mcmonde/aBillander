@extends('layouts.master')

@section('title') {{ l('Companies - Edit') }} @parent @stop


@section('content') 
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <div class="pull-right">
                <!-- a href="{{ URL::to('companies') }}" class="btn btn-default"><i class="fa fa-mail-reply"></i> Back to Companies</a -->
            </div>
            <h2><a href="{{ URL::to('#') }}">{{ l('Companies') }}</a> <span style="color: #cccccc;">/</span> {{ $company->name_fiscal }}</h2>
        </div>
    </div>
</div> 

<div class="container-fluid">
   <div class="row">
      <div class="col-lg-2 col-md-2 col-sm-3">
         <div class="list-group">
            <a id="b_generales" href="" class="list-group-item active" onClick="return false;">
               <i class="fa fa-user"></i>
               &nbsp; {{ l('Main Data') }}
            </a>
         </div>
      </div>
      
      <div class="col-lg-10 col-md-10 col-sm-9">
         {!! Form::model($company->toArray(), array('method' => 'PATCH', 'route' => array('companies.update', $company->id))) !!}
            <div class="panel panel-info" id="panel_generales">
               <div class="panel-heading">
                  <h3 class="panel-title">{{ l('Main Data') }}</h3>
               </div>
               <div class="panel-body">

        <div class="row">
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('name_fiscal') ? 'has-error' : '' }}">
                    {{ l('Name') }}
                    {!! Form::text('name_fiscal', null, array('class' => 'form-control', 'id' => 'name_fiscal', 'placeholder' => l('Fiscal name'))) !!}
                    {!! $errors->first('name_fiscal', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('name_commercial') ? 'has-error' : '' }}">
                    {{ l('Commercial name') }}
                    {!! Form::text('name_commercial', null, array('class' => 'form-control', 'id' => 'name_commercial')) !!}
                    {!! $errors->first('name_commercial', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('identification') ? 'has-error' : '' }}">
                    {{ l('Fiscal code') }}
                    {!! Form::text('identification', null, array('class' => 'form-control', 'id' => 'identification')) !!}
                    {!! $errors->first('identification', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : '' }}">
                    {{ l('Administrator\'s name') }}
                    {!! Form::text('address[firstname]', null, array('class' => 'form-control', 'id' => 'firstname')) !!}
                    {!! $errors->first('firstname', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                    {{ l('Administrator\'s surname') }}
                    {!! Form::text('address[lastname]', null, array('class' => 'form-control', 'id' => 'lastname')) !!}
                    {!! $errors->first('lastname', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-5 col-md-5 col-sm-5 {{ $errors->has('address1') ? 'has-error' : '' }}">
                    {{ l('Address (street, square, road...)') }}
                    {!! Form::text('address[address1]', null, array('class' => 'form-control', 'id' => 'address1')) !!}
                    {!! $errors->first('address1', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-5 col-md-5 col-sm-5 {{ $errors->has('address2') ? 'has-error' : '' }}">
                    {{ l('Address 2 (quarter, building...)') }}
                    {!! Form::text('address[address2]', null, array('class' => 'form-control', 'id' => 'address2')) !!}
                    {!! $errors->first('address2', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('postcode') ? 'has-error' : '' }}">
                    {{ l('Postal code') }}
                    {!! Form::text('address[postcode]', null, array('class' => 'form-control', 'id' => 'postcode')) !!}
                    {!! $errors->first('postcode', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('city') ? 'has-error' : '' }}">
                    {{ l('City') }}
                    {!! Form::text('address[city]', null, array('class' => 'form-control', 'id' => 'city')) !!}
                    {!! $errors->first('city', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('state') ? 'has-error' : '' }}">
                    {{ l('State') }}
                    {!! Form::text('address[state]', null, array('class' => 'form-control', 'id' => 'state')) !!}
                    {!! $errors->first('state', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('country') ? 'has-error' : '' }}">
                    {{ l('Country') }}
                    {!! Form::text('address[country]', null, array('class' => 'form-control', 'id' => 'country')) !!}
                    {!! $errors->first('country', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                    {{ l('Phone (regular)') }}
                    {!! Form::text('address[phone]', null, array('class' => 'form-control', 'id' => 'phone')) !!}
                    {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group {{ $errors->has('fax') ? 'has-error' : '' }}">
                    {{ l('Fax') }}
                    {!! Form::text('address[fax]', null, array('class' => 'form-control', 'id' => 'fax')) !!}
                    {!! $errors->first('fax', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    {{ l('Email') }}
                    {!! Form::text('address[email]', null, array('class' => 'form-control', 'id' => 'email')) !!}
                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-3">
                  <div class="form-group {{ $errors->has('website') ? 'has-error' : '' }}">
                    {{ l('Web') }}
                    {!! Form::text('website', null, array('class' => 'form-control', 'id' => 'website')) !!}
                    {!! $errors->first('website', '<span class="help-block">:message</span>') !!}
                  </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-4">
                 <div class="form-group {{ $errors->has('currency_id') ? 'has-error' : '' }}">
                    {{ l('Currency') }} {{ l('(cannot be changed)') }}
                    @if(isset($company))
                      {!! Form::text("currency[name]", null, array('class' => 'form-control', 'onfocus' => 'this.blur()')) !!}
                    @else
                      {!! Form::select('currency_id', array('0' => l('-- Please, select --', [], 'layouts')) + $currencyList, null, array('class' => 'form-control')) !!}
                      {!! $errors->first('currency_id', '<span class="help-block">:message</span>') !!}
                    @endif
                 </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('company_logo') ? 'has-error' : '' }}">
                    {{ l('Company logo') }}
                    {!! Form::text('company_logo', null, array('class' => 'form-control', 'id' => 'company_logo')) !!}
                    {!! $errors->first('company_logo', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="row">
                <div class="form-group col-lg-12 col-md-12 col-sm-12 {{ $errors->has('notes') ? 'has-error' : '' }}">
                  {{ l('Notes', [], 'layouts') }}
                  {!! Form::text('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '3')) !!}
                  {!! $errors->first('notes', '<span class="help-block">:message</span>') !!}
                </div>
        </div>

               </div><!-- div class="panel-body" -->

               <div class="panel-footer text-right">
                  <!-- a class="btn btn-link" data-dismiss="modal" href="{{ URL::to('companies') }}">Cancelar</a -->
                  <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                     <i class="fa fa-floppy-o"></i>
                     &nbsp; {{ l('Save', [], 'layouts') }}
                  </button>
               </div>
            </div>
          {!! Form::close() !!}
      </div>
   </div>
</div>
@stop