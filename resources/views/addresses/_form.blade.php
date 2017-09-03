
{!! Form::hidden('back_route', $back_route, array('id' => 'back_route')) !!}

<ul class="nav nav-tabs lead" style="font-size: 16px; margin-top: 10px;">
  <li class="active"><a href="#main_data" data-toggle="tab">Dirección</a></li>
  <li><a href="#contact_data" data-toggle="tab">Contacto</a></li>
  <li><a href="#extra_data" data-toggle="tab">Otros</a></li>
</ul>

<div id="myTabContent" class="tab-content" style="padding-top: 20px;">
  <div class="tab-pane fade active in" id="main_data">

        <div class="row">
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('alias') ? 'has-error' : '' }}">
                      {{ l('Alias', [],'addresses') }}
                      {!! Form::text('alias', null, array('class' => 'form-control', 'id' => 'alias')) !!}
                      {!! $errors->first('alias', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('name_commercial') ? 'has-error' : '' }}">
                      {{ l('Name', [],'addresses') }}
                      {!! Form::text('name_commercial', null, array('class' => 'form-control', 'id' => 'name_commercial')) !!}
                      {!! $errors->first('name_commercial', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-5 col-md-5 col-sm-5 {{ $errors->has('address1') ? 'has-error' : '' }}">
                      {{ l('Address (street, square, road...)', [],'addresses') }}
                      {!! Form::text('address1', null, array('class' => 'form-control', 'id' => 'address1')) !!}
                      {!! $errors->first('address1', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-5 col-md-5 col-sm-5 {{ $errors->has('address2') ? 'has-error' : '' }}">
                      {{ l('Address 2 (quarter, building...)', [],'addresses') }}
                      {!! Form::text('address2', null, array('class' => 'form-control', 'id' => 'address2')) !!}
                      {!! $errors->first('address2', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('postcode') ? 'has-error' : '' }}">
                      {{ l('Postal code', [],'addresses') }}
                      {!! Form::text('postcode', null, array('class' => 'form-control', 'id' => 'postcode')) !!}
                      {!! $errors->first('postcode', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('city') ? 'has-error' : '' }}">
                      {{  l('City', [],'addresses') }}
                      {!! Form::text('city', null, array('class' => 'form-control', 'id' => 'city')) !!}
                      {!! $errors->first('city', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('state') ? 'has-error' : '' }}">
                      {{ l('State', [],'addresses') }}
                      {!! Form::text('state', null, array('class' => 'form-control', 'id' => 'state')) !!}
                      {!! $errors->first('state', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('country') ? 'has-error' : '' }}">
                      {{ l('Country', [],'addresses') }}
                      {!! Form::text('country', null, array('class' => 'form-control', 'id' => 'country')) !!}
                      {!! $errors->first('country', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <!-- div class="row">
            <div class="form-group col-lg-3 col-md-3 col-sm-3">
            <div class="well well-sm">
               <b>Contacto</b>
            </div>
            </div>
        </div -->

  </div>
  <div class="tab-pane fade in" id="contact_data">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : '' }}">
                    {{ l('Contact name', [],'addresses') }}
                    {!! Form::text('firstname', null, array('class' => 'form-control', 'id' => 'firstname')) !!}
                    {!! $errors->first('firstname', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                    {{ l('Contact surname', [],'addresses') }}
                    {!! Form::text('lastname', null, array('class' => 'form-control', 'id' => 'lastname')) !!}
                    {!! $errors->first('lastname', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    {{ l('Email', [],'addresses') }}
                    {!! Form::text('email', null, array('class' => 'form-control', 'id' => 'email')) !!}
                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                    {{ l('Phone (regular)', [],'addresses') }}
                    {!! Form::text('phone', null, array('class' => 'form-control', 'id' => 'phone')) !!}
                    {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('phone_mobile') ? 'has-error' : '' }}">
                    {{ l('Phone (mobile)', [],'addresses') }}
                    {!! Form::text('phone_mobile', null, array('class' => 'form-control', 'id' => 'phone_mobile')) !!}
                    {!! $errors->first('phone_mobile', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('fax') ? 'has-error' : '' }}">
                    {{ l('Fax', [],'addresses') }}
                    {!! Form::text('fax', null, array('class' => 'form-control', 'id' => 'fax')) !!}
                    {!! $errors->first('fax', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

  </div>
  <div class="tab-pane fade" id="extra_data">

        <!-- div class="row">
            <div class="form-group col-lg-3 col-md-3 col-sm-3">
            <div class="well well-sm">
               <b>Otros</b>
            </div>
            </div>
        </div -->

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('longitude') ? 'has-error' : '' }}">
                      {{ l('Longitude') }}
                     {!! Form::text('longitude', null, array('class' => 'form-control', 'id' => 'longitude')) !!}
                     {!! $errors->first('longitude', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('latitude') ? 'has-error' : '' }}">
                      {{ l('Latitude') }}
                     {!! Form::text('latitude', null, array('class' => 'form-control', 'id' => 'latitude')) !!}
                     {!! $errors->first('latitude', '<span class="help-block">:message</span>') !!}
                </div>
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
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 {{ $errors->has('notes') ? 'has-error' : '' }}">
                     {{ l('Notes', [], 'layouts') }}
                     {!! Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '3')) !!}
                     {!! $errors->first('notes', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

  </div>

               <!-- div class="panel-footer text-right">
                  <input type="hidden" value="" name="tab_name" id="tab_name">
                  <button class="btn xbtn-sm btn-lightblue" xstyle="background-color: #008cba;" type="submit" onclick="this.disabled=true;$('#tab_name').val('addressbook');this.form.submit();">
                     <span class="glyphicon glyphicon-hdd"></span>
                     &nbsp; Guardar
                  </button>
               </div -->

               </div><!-- div id="myTabContent" -->

        <?php if (!isset($back_route)) $back_route = ''; ?>
        <input type="hidden" name="back_route" value="{{$back_route}}"/>

		{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
        {!! link_to( ($back_route != '' ? $back_route : 'addresses.index'), l('Cancel', [], 'layouts'), array('class' => 'btn btn-warning')) !!}

        {!! link_to_route('addresses.index', 'Todas las Direcciones', null, array('class' => 'btn btn-warning pull-right')) !!}