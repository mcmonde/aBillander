
   <div class="container-fluid" xstyle="margin-bottom: 20px;">
      <div class="row">

         @if ($invoice->draft)
         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('sequence_id') ? 'has-error' : '' }}">
            Serie de Facturas:
            {!! Form::select('sequence_id', array('0' => '-- Seleccione--') + $sequenceList, Input::old('sequence_id', isset($invoice) ? $invoice->sequence_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('sequence_id', '<span class="help-block">:message</span>') !!}
         </div>
         @else
         @endif

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('reference') ? 'has-error' : '' }}">
            Referencia / Proyecto:
            <input class="form-control" type="text" name="reference" id="reference" placeholder="" value="{{ Input::old('reference', '') }}" />
           {!! $errors->first('reference', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="col-lg-2 col-md-2 col-sm-2" {{ $errors->has('document_date') ? 'has-error' : '' }}">
            <div class="form-group">
               Fecha:
               <input type="text" id="document_date" name="document_date" class="form-control" value="{{ Input::old('document_date', ($invoice->document_date)) }}" autocomplete="off"/>
               {!! $errors->first('document_date', '<span class="help-block">:message</span>') !!}
            </div>
         </div>

         <div class="col-lg-2 col-md-2 col-sm-2" {{ $errors->has('delivery_date') ? 'has-error' : '' }}">
            <div class="form-group">
               Fecha Entrega:
               <input type="text" id="delivery_date" name="delivery_date" class="form-control" value="{{ Input::old('delivery_date', ($invoice->delivery_date)) }}" autocomplete="off"/>
               {!! $errors->first('delivery_date', '<span class="help-block">:message</span>') !!}
            </div>
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('template_id') ? 'has-error' : '' }}">
            Plantilla para Imprimir:
            {!! Form::select('template_id', array('0' => '-- Seleccione --') + $customerinvoicetemplateList, Input::old('template_id', isset($invoice) ? $invoice->template_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('template_id', '<span class="help-block">:message</span>') !!}
         </div>

          <div class="form-group col-lg-2 col-md-2 col-sm-2">
            <!-- label class="control-label" -->Guardar como Borrador?<!-- /label -->
            <div class="">
              <div class="radio-inline">
                <label>
                  <input name="draft" id="draft_on"  value="1" @if (  Input::old('draft', isset($invoice) ? $invoice->draft : 1) )checked="checked"@endif type="radio">
                  Sí
                </label>
              </div>
              <div class="radio-inline">
                <label>
                  <input name="draft" id="draft_off" value="0" @if ( !Input::old('draft', isset($invoice) ? $invoice->draft : 1) )checked="checked"@endif type="radio">
                  No
                </label>
              </div>
            </div>
          </div>

         <!-- div class="form-group col-lg-2 col-md-2 col-sm-2">
            El Cliente<br />
            @if ($customer->einvoice > 0)
               <span class="label label-success">Acepta Factura Electrónica</span>
            @else
               <span class="label label-danger">No acepta Factura Electrónica</span>
            @endif
         </div -->

      </div>
      <div class="row">

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('payment_method_id') ? 'has-error' : '' }}">
            Forma de Pago:
            {!! Form::select('payment_method_id', array('0' => '-- Seleccione --') + $payment_methodList, Input::old('payment_method_id', isset($invoice) ? $invoice->payment_method_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('payment_method_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('currency_id') ? 'has-error' : '' }}">
            Divisa de Pago:
            {!! Form::select('currency_id', array('0' => '-- Seleccione --') + $currencyList, Input::old('currency_id', isset($invoice) ? $invoice->currency_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('currency_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('currency_conversion_rate') ? 'has-error' : '' }}">
            Conversión:
            <input class="form-control" type="text" name="currency_conversion_rate" id="currency_conversion_rate" placeholder="" value="{{ Input::old('currency_conversion_rate', isset($invoice) ? $invoice->currency_conversion_rate : '') }}" />
           {!! $errors->first('currency_conversion_rate', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('sales_rep_id') ? 'has-error' : '' }}">
            Agente Comercial:
            {!! Form::select('sales_rep_id', array('0' => '-- Seleccione --') + $salesrepList, Input::old('sales_rep_id', isset($invoice) ? $invoice->sales_rep_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('sales_rep_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('down_payment') ? 'has-error' : '' }}">
            Anticipo:
            <input class="form-control" type="text" name="down_payment" id="down_payment" placeholder="" value="{{ Input::old('down_payment', isset($invoice) ? $invoice->down_payment : null) }}" />
           {!! $errors->first('down_payment', '<span class="help-block">:message</span>') !!}
         </div>

      </div>
      <div class="row">

         <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('shipping_address_id') ? 'has-error' : '' }}">
            Dirección de Envío:
           <select class="form-control" name="shipping_address_id" id="shipping_address_id" @if ( count($aBook)==1 ) disabled="disabled" @endif>
               <option {{ $customer->shipping_address_id <= 0 ? 'selected="selected"' : '' }} value="0">-- Seleccione--</option>
               @foreach ($aBook as $country)
               <option {{ $customer->shipping_address_id == $country->id ? 'selected="selected"' : '' }} value="{{ $country->id }}">{{ $country->alias }}</option>
               @endforeach
           </select>
           {!! $errors->first('shipping_address_id', '<span class="help-block">:message</span>') !!}
         </div>
         
         <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('warehouse_id') ? 'has-error' : '' }}">
            Almacén:
            {!! Form::select('warehouse_id', array('0' => '-- Seleccione --') + $warehouseList, Input::old('warehouse_id', isset($invoice) ? $invoice->warehouse_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('warehouse_id', '<span class="help-block">:message</span>') !!}
         </div>
         
         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('carrier_id') ? 'has-error' : '' }}">
            Transportista:
            {!! Form::select('carrier_id', array('0' => '-- Seleccione--') + $carrierList, Input::old('carrier_id', isset($invoice) ? $invoice->carrier_id : 0), array('class' => 'form-control')) !!}
            {!! $errors->first('carrier_id', '<span class="help-block">:message</span>') !!}
         </div>

         <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('tracking_number') ? 'has-error' : '' }}">
            Número de Seguimiento (tracking):
            <input class="form-control" type="text" name="tracking_number" id="tracking_number" placeholder="" value="{{ Input::old('tracking_number', isset($invoice) ? $invoice->tracking_number : '') }}" />
           {!! $errors->first('tracking_number', '<span class="help-block">:message</span>') !!}
         </div>

      </div>
      <div class="row">         

         <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('shipping_conditions') ? 'has-error' : '' }}">
            Condiciones de Entrega:
            <textarea id="shipping_conditions" class="form-control" xcols="50" name="shipping_conditions" rows="3" placeholder="">{{ Input::old('shipping_conditions', isset($invoice) ? $invoice->tracking_number : '') }}</textarea>
         {!! $errors->first('shipping_conditions', '<span class="help-block">:message</span>') !!}
         </div>

      </div>
      <div class="row">  

      </div>       

   </div>
