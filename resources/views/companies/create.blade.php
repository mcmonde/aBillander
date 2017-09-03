@extends('layouts.master')

@section('title') Empresa - Crear :: @parent @stop


@section('content') 
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <div class="pull-right">
                <a href="{{ URL::to('companies') }}" class="btn btn-default"><i class="fa fa-mail-reply"></i> Volver a Empresas</a>
            </div>
            <h2><a href="{{ URL::to('companies') }}">Empresas</a> <span style="color: #cccccc;">/</span> Crear Empresa</h2>
        </div>
    </div>
</div> 

<div class="container-fluid">
   <div class="row">
      <div class="col-lg-2 col-md-2 col-sm-3">
         <div class="list-group">
            <a id="b_generales" href="" class="list-group-item active" onClick="return false;">
               <span class="glyphicon glyphicon-user"></span>
               &nbsp; Datos Generales
            </a>
         </div>
      </div>
      
      <div class="col-lg-10 col-md-10 col-sm-9">
         {{ Form::open(array('url' => 'companies', 'id' => 'create_company', 'name' => 'create_company', 'class' => 'form')) }}
            <div class="panel panel-info" id="panel_generales">
               <div class="panel-heading">
                  <h3 class="panel-title">Datos Generales</h3>
               </div>
               <div class="panel-body">

        <div class="row">
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 {{{ $errors->has('name_fiscal') ? 'has-error' : '' }}}">
                     Nombre:
                     <input class="form-control" type="text" name="name_fiscal" id="name_fiscal" placeholder="Nombre fiscal" value="{{{ Input::old('name_fiscal', isset($company) ? $company->name_fiscal : null) }}}" />
                	 {{ $errors->first('name_fiscal', '<span class="help-block">:message</span>') }}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{{ $errors->has('name_commercial') ? 'has-error' : '' }}}">
                     Nombre Comercial:
                     <input class="form-control" type="text" name="name_commercial" id="name_commercial" placeholder="" value="{{{ Input::old('name_commercial', isset($company) ? $company->name_commercial : null) }}}" />
                    {{ $errors->first('name_commercial', '<span class="help-block">:message</span>') }}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{{ $errors->has('identification') ? 'has-error' : '' }}}">
                     DNI / CIF:
                     <input class="form-control" type="text" name="identification" id="identification" placeholder="" value="{{{ Input::old('identification', isset($company) ? $company->identification : null) }}}" />
                	 {{ $errors->first('identification', '<span class="help-block">:message</span>') }}
                  </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{{ $errors->has('firstname') ? 'has-error' : '' }}}">
                    Administrador Nombre:
                    <input class="form-control" type="text" name="firstname" id="firstname" placeholder="" value="{{{ Input::old('firstname', isset($company) ? $company->firstname : '') }}}" />
                    {{ $errors->first('firstname', '<span class="help-block">:message</span>') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{{ $errors->has('lastname') ? 'has-error' : '' }}}">
                    Administrador Apellidos:
                    <input class="form-control" type="text" name="lastname" id="lastname" placeholder="" value="{{{ Input::old('lastname', isset($company) ? $company->lastname : '') }}}" />
                    {{ $errors->first('lastname', '<span class="help-block">:message</span>') }}
                </div>
            </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-5 col-md-5 col-sm-5 {{{ $errors->has('address1') ? 'has-error' : '' }}}">
                     Dirección (calle, plaza, vía...):
                     <input class="form-control" type="text" name="address1" id="address1" placeholder="" value="{{{ Input::old('address1', isset($company) ? $company->address1 : null) }}}" />
                    {{ $errors->first('address1', '<span class="help-block">:message</span>') }}
                  </div>
                  <div class="form-group col-lg-5 col-md-5 col-sm-5 {{{ $errors->has('address2') ? 'has-error' : '' }}}">
                     Dirección 2 (barrio, edificio...):
                     <input class="form-control" type="text" name="address2" id="address2" placeholder="" value="{{{ Input::old('address2', isset($company) ? $company->address2 : null) }}}" />
                    {{ $errors->first('address2', '<span class="help-block">:message</span>') }}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{{ $errors->has('postcode') ? 'has-error' : '' }}}">
                     Código Postal:
                     <input class="form-control" type="text" name="postcode" id="postcode" placeholder="" value="{{{ Input::old('postcode', isset($company) ? $company->postcode : null) }}}" />
                    {{ $errors->first('postcode', '<span class="help-block">:message</span>') }}
                  </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{{ $errors->has('city') ? 'has-error' : '' }}}">
                     Ciudad:
                     <input class="form-control" type="text" name="city" id="city" placeholder="" value="{{{ Input::old('city', isset($company) ? $company->city : null) }}}" />
                    {{ $errors->first('city', '<span class="help-block">:message</span>') }}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{{ $errors->has('state') ? 'has-error' : '' }}}">
                     Provincia:
                     <input class="form-control" type="text" name="state" id="state" placeholder="" value="{{{ Input::old('state', isset($company) ? $company->state : null) }}}" />
                    {{ $errors->first('state', '<span class="help-block">:message</span>') }}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{{ $errors->has('country') ? 'has-error' : '' }}}">
                     País:
                     <input class="form-control" type="text" name="country" id="country" placeholder="" value="{{{ Input::old('country', isset($company) ? $company->country : Configuration::get('DEF_COUNTRY_NAME')) }}}" />
                    {{ $errors->first('country', '<span class="help-block">:message</span>') }}
                  </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{{ $errors->has('phone') ? 'has-error' : '' }}}">
                    Teléfono (fijo):
                    <input class="form-control" type="text" name="phone" id="phone" placeholder="" value="{{{ Input::old('phone', isset($company) ? $company->phone : '') }}}" />
                    {{ $errors->first('phone', '<span class="help-block">:message</span>') }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group {{{ $errors->has('fax') ? 'has-error' : '' }}}">
                    Fax:
                    <input class="form-control" type="text" name="fax" id="fax" placeholder="" value="{{{ Input::old('fax', isset($company) ? $company->fax : '') }}}" />
                    {{ $errors->first('fax', '<span class="help-block">:message</span>') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{{ $errors->has('email') ? 'has-error' : '' }}}">
                    Correo Electrónico:
                    <input class="form-control" type="text" name="email" id="customer_email" placeholder="" value="{{{ Input::old('email', isset($company) ? $company->email : '') }}}" />
                    {{ $errors->first('email', '<span class="help-block">:message</span>') }}
                </div>
            </div>
            <div class="col-md-3">
                  <div class="form-group {{{ $errors->has('website') ? 'has-error' : '' }}}">
                     Web:
                     <input class="form-control" type="text" name="website" id="website" placeholder="www." value="{{{ Input::old('website', isset($company) ? $company->website : null) }}}" />
                    {{ $errors->first('website', '<span class="help-block">:message</span>') }}
                  </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-2">
                 <div class="form-group {{{ $errors->has('currency_id') ? 'has-error' : '' }}}">
                    Divisa:
                    {{ Form::select('currency_id', array('0' => l('-- Please, select --', [], 'layouts')) + $currencyList, Input::old('currency_id', isset($company) ? $company->currency_id : 0), array('class' => 'form-control')) }}
                    {{ $errors->first('currency_id', '<span class="help-block">:message</span>') }}
                 </div>
            </div>

            <div class="col-md-2">
                 <div class="form-group">
                    &nbsp;
                 </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{{ $errors->has('company_logo') ? 'has-error' : '' }}}">
                    Logotipo:
                    <input class="form-control" type="text" name="company_logo" id="customer_email" placeholder="" value="{{{ Input::old('company_logo', isset($company) ? $company->company_logo : '') }}}" />
                    {{ $errors->first('company_logo', '<span class="help-block">:message</span>') }}
                </div>
            </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 {{{ $errors->has('notes') ? 'has-error' : '' }}}">
                     Notas:
                     <textarea id="notes" class="form-control" xcols="50" name="notes" rows="3" placeholder="">{{{ Input::old('notes', isset($company) ? $company->notes : null) }}}</textarea>
            		{{ $errors->first('notes', '<span class="help-block">:message</span>') }}
                  </div>
        </div>

               </div><!-- div class="panel-body" -->

               <div class="panel-footer text-right">
                  <a class="btn btn-link" data-dismiss="modal" href="{{{ URL::to('companies') }}}">Cancelar</a>
                  <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                     <span class="glyphicon glyphicon-floppy-disk"></span>
                     &nbsp; Guardar
                  </button>
               </div>
            </div>
          {{ Form::close() }}
      </div>
   </div>
</div>
@stop