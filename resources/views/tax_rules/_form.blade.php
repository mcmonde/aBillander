
<div class="row">

      <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('country_id') ? 'has-error' : '' }}">
        {!! Form::label('country', l('Country')) !!}
        {!! Form::select('country_id', array('0' => l('-- All --')) + $countryList, null, array('class' => 'form-control', 'id' => 'country_id')) !!}
        {!! $errors->first('country_id', '<span class="help-block">:message</span>') !!}
      </div>
      <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('state_id') ? 'has-error' : '' }}">
        {!! Form::label('state', l('State')) !!}
        {!! Form::select('state_id', array('0' => l('-- All --')) + ( isset($stateList) ? $stateList : [] ), null, array('class' => 'form-control', 'id' => 'state_id')) !!}
        {!! $errors->first('state_id', '<span class="help-block">:message</span>') !!}
      </div>

</div>

<div class="row">
    <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('name') ? 'has-error' : '' }}">
        {!! Form::label('name', l('Tax Rule Name')) !!}
        {!! Form::text('name', null, array('class' => 'form-control')) !!}
        {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
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
    <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('percent') ? 'has-error' : '' }}">
        {!! Form::label('percent', l('Tax Rule Percent')) !!}
        {!! Form::text('percent', null, array('class' => 'form-control')) !!}
        {!! $errors->first('percent', '<span class="help-block">:message</span>') !!}
    </div>
    <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('amount') ? 'has-error' : '' }}">
        {!! Form::label('amount', l('Tax Rule Amount')) !!}
             <a href="javascript:void(0);" data-toggle="popover" data-placement="top" 
                                    data-content="{{ l('Use this field when tax is a fixed amount per item.') }}">
                      <i class="fa fa-question-circle abi-help"></i>
               </a>
        {!! Form::text('amount', null, array('class' => 'form-control')) !!}
        {!! $errors->first('amount', '<span class="help-block">:message</span>') !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('position') ? 'has-error' : '' }}">
        {!! Form::label('position', l('Position')) !!}
                 <a href="javascript:void(0);" data-toggle="popover" data-placement="top" 
                                    data-content="{{ l('Use multiples of 10. Use other values to interpolate.') }}">
                      <i class="fa fa-question-circle abi-help"></i>
               </a>
        {!! Form::text('position', null, array('class' => 'form-control')) !!}
        {!! $errors->first('position', '<span class="help-block">:message</span>') !!}
    </div>
</div>

	{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
	{!! link_to_route('taxes.taxrules.index', l('Cancel', [], 'layouts'), array($tax->id), array('class' => 'btn btn-warning')) !!}



@section('scripts')  @parent 

    <script type="text/javascript">
        $('select[name="country_id"]').change(function () {
            var countryID = $(this).val();
            
            $.get('{{ url('/') }}/countries/' + countryID + '/getstates', function (states) {
                

                $('select[name="state_id"]').empty();
                $('select[name="state_id"]').append('<option value=0>{{ l('-- All --') }}</option>');
                $.each(states, function (key, value) {
                    $('select[name="state_id"]').append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            });
        });

        // Select default country
        if ( !($('input[name="name"]').val().length > 0) ) {
            var def_countryID = {{ \App\Configuration::get('DEF_COUNTRY') }};

            $('select[name="country_id"]').val(def_countryID).change();
        }

    </script>

@endsection
