
            <div class="panel panel-primary" id="panel_commercial">
               <div class="panel-heading">
                  <h3 class="panel-title">{{ l('Commercial') }}</h3>
               </div>
               <div class="panel-body">

<!-- Comercial -->

        <div class="row">
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('sequence_id') ? 'has-error' : '' }}">
                     {{ l('Sequence for Invoices') }}
                     {!! Form::select('sequence_id', array('0' => l('-- Please, select --', [], 'layouts')) + $sequenceList, Input::old('sequence_id', isset($customer) ? $customer->sequence_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('sequence_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('invoice_template_id') ? 'has-error' : '' }}">
                     {{ l('Template for Invoices') }}
                     {!! Form::select('invoice_template_id', array('0' => l('-- Please, select --', [], 'layouts')) + $customerinvoicetemplateList, Input::old('invoice_template_id', isset($customer) ? $customer->invoice_template_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('invoice_template_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('payment_method_id') ? 'has-error' : '' }}">
                     {{ l('Payment Method') }}
                     {!! Form::select('payment_method_id', array('0' => l('-- Please, select --', [], 'layouts')) + $payment_methodList, Input::old('payment_method_id', isset($customer) ? $customer->payment_method_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('payment_method_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('currency_id') ? 'has-error' : '' }}">
                     {{ l('Payment Currency') }}
                     {!! Form::select('currency_id', array('0' => l('-- Please, select --', [], 'layouts')) + $currencyList, Input::old('currency_id', isset($customer) ? $customer->currency_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('currency_id', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">

                   <div class="form-group col-lg-3 col-md-3 col-sm-3" id="div-active">
                     {!! Form::label('accept_einvoice', l('Accept e-Invoice?'), ['class' => 'control-label']) !!}
                     <div>
                       <div class="radio-inline">
                         <label>
                           {!! Form::radio('accept_einvoice', '1', true, ['id' => 'accept_einvoice_on']) !!}
                           {!! l('Yes', [], 'layouts') !!}
                         </label>
                       </div>
                       <div class="radio-inline">
                         <label>
                           {!! Form::radio('accept_einvoice', '0', false, ['id' => 'accept_einvoice_off']) !!}
                           {!! l('No', [], 'layouts') !!}
                         </label>
                       </div>
                     </div>
                   </div>

                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('outstanding_amount_allowed') ? 'has-error' : '' }}">
                     {{ l('Outstanding Amount Allowed') }}
                     <input class="form-control" type="text" name="outstanding_amount_allowed" id="outstanding_amount_allowed" placeholder="" value="{{ Input::old('outstanding_amount_allowed', isset($customer) ? $customer->outstanding_amount_allowed : null) }}" />
                    {!! $errors->first('outstanding_amount_allowed', '<span class="help-block">:message</span>') !!}
                  </div>

                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('outstanding_amount') ? 'has-error' : '' }}">
                     {{ l('Outstanding Amount') }}
                     <input class="form-control" type="text" name="outstanding_amount" id="outstanding_amount" disabled="disabled" value="{{ isset($customer) ? $customer->outstanding_amount : 0 }}" />
                    {!! $errors->first('outstanding_amount', '<span class="help-block">:message</span>') !!}
                  </div>

                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('unresolved_amount') ? 'has-error' : '' }}">
                     {{ l('Unresolved Amount') }}
                     <input class="form-control" type="text" name="unresolved_amount" id="unresolved_amount" disabled="disabled" value="{{ isset($customer) ? $customer->unresolved_amount : 0 }}" />
                    {!! $errors->first('unresolved_amount', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">

                   <div class="form-group col-lg-3 col-md-3 col-sm-3" id="div-active">
                     {!! Form::label('sales_equalization', l('Sales Equalization'), ['class' => 'control-label']) !!}
                     <div>
                       <div class="radio-inline">
                         <label>
                           {!! Form::radio('sales_equalization', '1', true, ['id' => 'sales_equalization_on']) !!}
                           {!! l('Yes', [], 'layouts') !!}
                         </label>
                       </div>
                       <div class="radio-inline">
                         <label>
                           {!! Form::radio('sales_equalization', '0', false, ['id' => 'sales_equalization_off']) !!}
                           {!! l('No', [], 'layouts') !!}
                         </label>
                       </div>
                     </div>
                   </div>

                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('customer_group_id') ? 'has-error' : '' }}">
                     {{ l('Customer Group') }}
                     {!! Form::select('customer_group_id', array('0' => l('-- Please, select --', [], 'layouts')) + $customer_groupList, Input::old('customer_group_id', isset($customer) ? $customer->customer_group_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('customer_group_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('price_list_id') ? 'has-error' : '' }}">
                     {{ l('Price List') }}
                     {!! Form::select('price_list_id', array('0' => l('-- Please, select --', [], 'layouts')) + $price_listList, Input::old('price_list_id', isset($customer) ? $customer->price_list_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('price_list_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('sales_rep_id') ? 'has-error' : '' }}">
                     {{ l('Sales Representative') }}
                     {!! Form::select('sales_rep_id', array('0' => l('-- Please, select --', [], 'layouts')) + $salesrepList, Input::old('sales_rep_id', isset($customer) ? $customer->sales_rep_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('sales_rep_id', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('shipping_address_id') ? 'has-error' : '' }}">
                     {{ l('Shipping Address') }}
                    <select class="form-control" name="shipping_address_id" id="shipping_address_id" @if ( count($aBook)==1 ) disabled="disabled" @endif>
                        <option {{ $customer->shipping_address_id <= 0 ? 'selected="selected"' : '' }} value="0">{{ l('-- Please, select --', [], 'layouts') }}</option>
                        @foreach ($aBook as $country)
                        <option {{ $customer->shipping_address_id == $country->id ? 'selected="selected"' : '' }} value="{{ $country->id }}">{{ $country->alias }}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('shipping_address_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('carrier_id') ? 'has-error' : '' }}">
                     {{ l('Carrier') }}
                     {!! Form::select('carrier_id', array('0' => l('-- Please, select --', [], 'layouts')) + $carrierList, Input::old('carrier_id', isset($customer) ? $customer->carrier_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('carrier_id', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="col-md-4">
                      <div class="form-group {{ $errors->has('webshop_id') ? 'has-error' : '' }}">
                          {{ l('Webshop ID') }}
                          <input class="form-control" type="text" name="webshop_id" id="webshop_id" placeholder="" value="{{ Input::old('webshop_id', isset($customer) ? $customer->webshop_id : null) }}" />
                          {!! $errors->first('webshop_id', '<span class="help-block">:message</span>') !!}
                      </div>
                  </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('payment_day') ? 'has-error' : '' }}">
                     {{ l('Payment Day') }}
                     <input class="form-control" type="text" name="payment_day" id="payment_day" placeholder="" value="{{ Input::old('payment_day', isset($customer) ? $customer->payment_day : null) }}" />
                    {!! $errors->first('payment_day', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

<!-- Comercial ENDS -->

               </div>
               <div class="panel-footer text-right">
                  <input type="hidden" value="" name="tab_name" id="tab_name">
                  <button class="btn btn-sm btn-info" type="submit" onclick="this.disabled=true;$('#tab_name').val('commercial');this.form.submit();">
                     <span class="glyphicon glyphicon-hdd"></span>
                     &nbsp; {{ l('Save', [], 'layouts') }}
                  </button>
               </div>
            </div>
