
<div class="form-group">
		{!! Form::label('name', l('Price List Name')) !!}
		{!! Form::text('name', null, array('placeholder' => '', 'class' => 'form-control')) !!}
</div>

<div class="form-group">
		{!! Form::label('type', l('Price List Type')) !!}
		{!! Form::select('type', array(	
											0 => l('Fixed price', [], 'appmultilang'),
											1 => l('Discount percentage', [], 'appmultilang'),
											2 => l('Margin percentage', [], 'appmultilang')
									), null, array('class' => 'form-control')) !!}
</div>
<div class="form-group" id="div-amount">
		{!! Form::label('amount', l('Amount')) !!}
		{!! Form::text('amount', null, array('placeholder' => '0.0', 'class' => 'form-control')) !!}
</div>

   <div class="form-group" id="div-price_is_tax_inc">
     {!! Form::label('price_is_tax_inc', l('Price is Tax Included?'), ['class' => 'control-label']) !!}
     <div class="">
       <div class="radio-inline">
         <label>
           {!! Form::radio('price_is_tax_inc', '1', false, ['id' => 'price_is_tax_inc_on']) !!}
           {!! l('Yes', [], 'layouts') !!}
         </label>
       </div>
       <div class="radio-inline">
         <label>
           {!! Form::radio('price_is_tax_inc', '0', true, ['id' => 'price_is_tax_inc_off']) !!}
           {!! l('No', [], 'layouts') !!}
         </label>
       </div>
     </div>
   </div>

{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
{!! link_to_route('pricelists.index', l('Cancel', [], 'layouts'), null, array('class' => 'btn btn-warning')) !!}
