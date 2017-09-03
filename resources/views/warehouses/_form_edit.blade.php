
<div class="panel-body">

	@include('addresses._form_fields_warehouse_related')

	<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-sm-12 {{ $errors->has('notes') ? 'has-error' : '' }}">
	          {{ l('Notes', [], 'layouts') }}
	          {!! Form::text('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '3')) !!}
	          {!! $errors->first('notes', '<span class="help-block">:message</span>') !!}
	        </div>
	</div>

</div><!-- div class="panel-body" -->

<div class="panel-footer text-right">
  <a class="btn btn-link" data-dismiss="modal" href="{{ URL::to('warehouses') }}">{{l('Cancel', [], 'layouts')}}</a>
  <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
     <span class="glyphicon glyphicon-floppy-disk"></span>
     &nbsp; {{ l('Save', [], 'layouts') }}
  </button>
</div>
