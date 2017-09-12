
<div class="row">
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('country', l('Country')) !!}
        {!! Form::text('country', null, array('class' => 'form-control')) !!}
    </div>
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('state', l('State')) !!}
        {!! Form::text('state', null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('name', l('Tax Rule Name')) !!}
        {!! Form::text('name', null, array('class' => 'form-control')) !!}
    </div>
    <div class="form-group col-lg-6 col-md-6 col-sm-6" id="div-sales_equalization">
     {!! Form::label('sales_equalization', l('Sales Equalization'), ['class' => 'control-label']) !!}
     <div>
       <div class="radio-inline">
         <label>
           {!! Form::radio('sales_equalization', '1', false, ['id' => 'sales_equalization_on']) !!}
           {!! l('Yes', [], 'layouts') !!}
         </label>
       </div>
       <div class="radio-inline">
         <label>
           {!! Form::radio('sales_equalization', '0', true, ['id' => 'sales_equalization_off']) !!}
           {!! l('No', [], 'layouts') !!}
         </label>
       </div>
     </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('percent', l('Tax Rule Percent')) !!}
        {!! Form::text('percent', null, array('class' => 'form-control')) !!}
    </div>
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('amount', l('Tax Rule Amount')) !!}
             <a href="javascript:void(0);">
                <button type="button" xclass="btn btn-xs btn-success" data-toggle="popover" data-placement="top" 
                        data-content="{{ l('Use this field when tax is a fixed amount per item.') }}">
                    <i class="fa fa-info-circle"></i>
                </button>
             </a>
        {!! Form::text('amount', null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('position', l('Position')) !!}
                 <a href="javascript:void(0);">
                    <button type="button" xclass="btn btn-xs btn-success" data-toggle="popover" data-placement="top" 
                            data-content="{{ l('Use multiples of 10. Use other values to interpolate.') }}">
                        <i class="fa fa-info-circle"></i>
                    </button>
                 </a>
        {!! Form::text('position', null, array('class' => 'form-control')) !!}
    </div>
</div>

	{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
	{!! link_to_route('taxes.taxrules.index', l('Cancel', [], 'layouts'), array($tax->id), array('class' => 'btn btn-warning')) !!}
