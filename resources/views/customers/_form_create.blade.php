
         <div class="panel-body">

  <div class="row">
            <div class="form-group col-lg-6 col-md-6 col-sm-6 {!! $errors->has('name_fiscal') ? 'has-error' : '' !!}">
              {{ l('Fiscal Name') }}
              {!! Form::text('name_fiscal', null, array('class' => 'form-control', 'id' => 'name_fiscal')) !!}
              {!! $errors->first('name_fiscal', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group col-lg-4 col-md-4 col-sm-4 {!! $errors->has('name_commercial') ? 'has-error' : '' !!}">
              {{ l('Name', [],'addresses') }}
              {!! Form::text('name_commercial', null, array('class' => 'form-control', 'id' => 'name_commercial')) !!}
              {!! $errors->first('name_commercial', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group col-lg-2 col-md-2 col-sm-2 {!! $errors->has('identification') ? 'has-error' : '' !!}">
              {{ l('Identification') }}
              {!! Form::text('identification', null, array('class' => 'form-control', 'id' => 'identification')) !!}
              {!! $errors->first('identification', '<span class="help-block">:message</span>') !!}
            </div>
  </div>

@include('addresses._form_fields_customers')

  <div class="row">
    <div class="form-group col-lg-12 col-md-12 col-sm-12 {{ $errors->has('notes') ? 'has-error' : '' }}">
      {{ l('Notes', [], 'layouts') }}
      {!! Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '3')) !!}
      {!! $errors->first('notes', '<span class="help-block">:message</span>') !!}
    </div>
  </div>

         </div><!-- div class="panel-body" -->

         <div class="panel-footer text-right">
            <a class="btn btn-link" data-dismiss="modal" href="{!! URL::to('customers') !!}">{{l('Cancel', [], 'layouts')}}</a>
            <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
               <span class="glyphicon glyphicon-floppy-disk"></span>
               &nbsp; {{l('Save', [], 'layouts')}}
            </button>
            <input type="hidden" id="nextAction" name="nextAction" value="" />
            <button class="btn btn-info" type="submit" onclick="this.disabled=true;$('#nextAction').val('completeCustomerData');this.form.submit();">
               <span class="glyphicon glyphicon-hdd"></span>
               &nbsp; {{l('Save & Complete', [], 'layouts')}}
            </button>
         </div>
