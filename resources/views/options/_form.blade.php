
<div class="row">
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('name', l('Option Name')) !!}
        {!! Form::text('name', null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('position', l('Position')) !!}
                 <a href="javascript:void(0);">
                    <button type="button" xclass="btn btn-xs btn-success" data-toggle="popover" data-placement="top" 
                            data-content="{{ l('Use multiples of 10. Use other values to interpolate.') }}">
                        <span class="glyphicon glyphicon-info-sign"></span>
                    </button>
                 </a>
        {!! Form::text('position', null, array('class' => 'form-control')) !!}
    </div>
</div>

  {!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
  {!! link_to_route('optiongroups.options.index', l('Cancel', [], 'layouts'), array($optiongroup->id), array('class' => 'btn btn-warning')) !!}
