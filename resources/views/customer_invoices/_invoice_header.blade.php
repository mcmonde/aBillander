
   <div class="container-fluid" xstyle="margin-bottom: 20px;">
      <div class="row">

         @if ($invoice->draft)
         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('sequence_id') ? 'has-error' : '' }}">
            {{ l('Invoice Sequence') }}
            {!! Form::select('sequence_id', array('0' => l('-- Please, select --', [], 'layouts')) + $sequenceList, null, array('class' => 'form-control', 'id' => 'sequence_id')) !!}
            {!! $errors->first('sequence_id', '<span class="help-block">:message</span>') !!}
         </div>
         @else
         <div class="form-group col-lg-2 col-md-2 col-sm-2">
            {{ l('Invoice Number') }}
            {!! Form::text('document_reference', null, array('class' => 'form-control', 'id' => 'document_reference', 'disabled' => 'disabled')) !!}
         </div>
         @endif

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('reference') ? 'has-error' : '' }}">
            {{ l('Reference / Project') }}
            {!! Form::text('reference', null, array('class' => 'form-control', 'id' => 'reference')) !!}
            {!! $errors->first('reference', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="col-lg-2 col-md-2 col-sm-2 {{ $errors->has('document_date_form') ? 'has-error' : '' }}">
            <div class="form-group">
               {{ l('Invoice Date') }}
               {!! Form::text('document_date_form', null, array('class' => 'form-control', 'id' => 'document_date_form', 'autocomplete' => 'off')) !!}
               {!! $errors->first('document_date_form', '<span class="help-block">:message</span>') !!}
            </div>
         </div>

         <div class="col-lg-2 col-md-2 col-sm-2 {{ $errors->has('delivery_date_form') ? 'has-error' : '' }}">
            <div class="form-group">
               {{ l('Delivery Date') }}
               {!! Form::text('delivery_date_form', null, array('class' => 'form-control', 'id' => 'delivery_date_form', 'autocomplete' => 'off')) !!}
               {!! $errors->first('delivery_date_form', '<span class="help-block">:message</span>') !!}
            </div>
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('template_id') ? 'has-error' : '' }}">
            {{ l('Template for Printing') }}
            {!! Form::select('template_id', array('0' => l('-- Please, select --', [], 'layouts')) + $customerinvoicetemplateList, null, array('class' => 'form-control', 'id' => 'template_id')) !!}
            {!! $errors->first('template_id', '<span class="help-block">:message</span>') !!}
         </div>

         @if ($invoice->draft)
         <div class="form-group col-lg-2 col-md-2 col-sm-2">
                 {{ l('Save as Draft?') }}<!-- label class="control-label" - - >Guardar como Borrador?< ! - - /label -->
            <div>
              <div class="radio-inline">
                <label>
                  {!! Form::radio('draft', '1', true, ['id' => 'draft_on']) !!}
                  {!! l('Yes', [], 'layouts') !!}
                </label>
              </div>
              <div class="radio-inline">
                <label>
                  {!! Form::radio('draft', '0', false, ['id' => 'draft_off']) !!}
                  {!! l('No', [], 'layouts') !!}
                </label>
              </div>
            </div>
          </div>
          @endif

      </div>
      <div class="row">

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('payment_method_id') ? 'has-error' : '' }}">
            {{ l('Payment Method') }}
            {!! Form::select('payment_method_id', array('0' => l('-- Please, select --', [], 'layouts')) + $payment_methodList, null, array('class' => 'form-control', 'id' => 'payment_method_id')) !!}
            {!! $errors->first('payment_method_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('currency_id') ? 'has-error' : '' }}">
            {{ l('Currency') }}
            {!! Form::select('currency_id', array('0' => l('-- Please, select --', [], 'layouts')) + $currencyList, null, array('class' => 'form-control', 'id' => 'currency_id')) !!}
            {!! $errors->first('currency_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('currency_conversion_rate') ? 'has-error' : '' }}">
            {{ l('Conversion Rate') }}
            {!! Form::text('currency_conversion_rate', null, array('class' => 'form-control', 'id' => 'currency_conversion_rate')) !!}
            {!! $errors->first('currency_conversion_rate', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('sales_rep_id') ? 'has-error' : '' }}">
            {{ l('Sales Representative') }}
            {!! Form::select('sales_rep_id', array('0' => l('-- Please, select --', [], 'layouts')) + $salesrepList, null, array('class' => 'form-control', 'id' => 'sales_rep_id')) !!}
            {!! $errors->first('sales_rep_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('down_payment') ? 'has-error' : '' }}">
            {{ l('Down Payment') }}
            {!! Form::text('down_payment', null, array('class' => 'form-control', 'id' => 'down_payment')) !!}
            {!! $errors->first('down_payment', '<span class="help-block">:message</span>') !!}
         </div>

      </div>
      <div class="row">

         <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('shipping_address_id') ? 'has-error' : '' }}">
            {{ l('Shipping Address') }}
            @if ( count($addressbookList)==1 )
                {!! Form::select('shipping_address_id', $addressbookList, $invoicing_address->id, array('class' => 'form-control', 'id' => 'shipping_address_id', 'disabled '=> 'disabled')) !!}
            @else
                {!! Form::select('shipping_address_id', array('0' => l('-- Please, select --', [], 'layouts')) + $addressbookList, null, array('class' => 'form-control', 'id' => 'shipping_address_id')) !!}
            @endif
            {!! $errors->first('shipping_address_id', '<span class="help-block">:message</span>') !!}
         </div>
         
         <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('warehouse_id') ? 'has-error' : '' }}">
            {{ l('Warehouse') }}
            {!! Form::select('warehouse_id', array('0' => l('-- Please, select --', [], 'layouts')) + $warehouseList, null, array('class' => 'form-control', 'id' => 'warehouse_id')) !!}
            {!! $errors->first('warehouse_id', '<span class="help-block">:message</span>') !!}
         </div>
         
         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('carrier_id') ? 'has-error' : '' }}">
            {{ l('Carrier') }}
            {!! Form::select('carrier_id', array('0' => l('-- Please, select --', [], 'layouts')) + $carrierList, null, array('class' => 'form-control', 'id' => 'carrier_id')) !!}
            {!! $errors->first('carrier_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('tracking_number') ? 'has-error' : '' }}">
            {{ l('Tracking Number') }}
            {!! Form::text('tracking_number', null, array('class' => 'form-control', 'id' => 'tracking_number')) !!}
            {!! $errors->first('tracking_number', '<span class="help-block">:message</span>') !!}
         </div>

      </div>
      <div class="row">         

         <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('shipping_conditions') ? 'has-error' : '' }}">
            {{ l('Shipping Conditions') }}
            {!! Form::textarea('shipping_conditions', null, array('class' => 'form-control', 'id' => 'shipping_conditions', 'rows' => '3')) !!}
            {!! $errors->first('shipping_conditions', '<span class="help-block">:message</span>') !!}
         </div>

      </div>
      <div class="row">  

      </div>       

   </div>
