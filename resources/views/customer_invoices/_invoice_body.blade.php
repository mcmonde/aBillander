
      <table class="table table-condensed">
         <thead>
            <tr>
               <th class="text-left" style="width: 60px"></th>
               <th class="text-left">{{l('Reference')}}</th>
               <th class="text-left">{{l('Description')}}</th>
               <th class="text-right" width="90">{{l('Quantity')}}</th>
               <th></th>
               <th class="text-right">{{l('Price')}}</th>
               <th class="text-right">{{l('With Tax')}}</th>
               <th class="text-right" width="90">{{l('Disc. %')}}</th>
               <th class="text-center">{{l('Net')}}</th>
               <th class="text-right" width="115">{{l('Tax')}}</th>
               <th class="text-right">{{l('Total')}}</th>
               <th class="text-right">{{l('R.E.')}}</th>
            </tr>
         </thead>
         <tbody id="order_lines">
            @if ( count($invoice->customerInvoiceLines) > 0 )
               @foreach ( $invoice->customerInvoiceLines as $i => $line )



<tr id="line_{{ $i }}">
      <td><input type="text" id="line_sort_order_{{ $i }}" name="line_sort_order_{{ $i }}" value="{{ $line->line_sort_order }}" class="form-control" /></td>
 
      <td><input type="hidden" id="lineid_{{ $i }}"          name="lineid_{{ $i }}"          value="{{ $i }}"/>
          
          <input type="hidden" id="line_type_{{ $i }}"       name="line_type_{{ $i }}"       value="{{ $line->line_type }}"/>
          <input type="hidden" id="product_id_{{ $i }}"      name="product_id_{{ $i }}"      value="{{ $line->product_id }}"/>
          <input type="hidden" id="combination_id_{{ $i }}"  name="combination_id_{{ $i }}"  value="{{ $line->combination_id }}"/>
          <input type="hidden" id="reference_{{ $i }}"       name="reference_{{ $i }}"       value="{{ $line->reference }}"/>
 
          <input type="hidden" id="cost_price_{{ $i }}"          name="cost_price_{{ $i }}"          value="{{ $line->cost_price }}"/>
          <input type="hidden" id="unit_price_{{ $i }}"          name="unit_price_{{ $i }}"          value="{{ $line->unit_price }}"/>
          <input type="hidden" id="unit_customer_price_{{ $i }}" name="unit_customer_price_{{ $i }}" value="{{ $line->unit_customer_price }}"/>
          
          <input type="hidden" id="tax_percent_{{ $i }}"         name="tax_percent_{{ $i }}"         value=""/>
          <input type="hidden" id="total_tax_{{ $i }}"           name="total_tax_{{ $i }}"           value=""/>
 
          <input type="hidden" id="discount_amount_tax_incl_{{ $i }}" name="discount_amount_tax_incl_{{ $i }}" value=""/>
          <input type="hidden" id="discount_amount_tax_excl_{{ $i }}" name="discount_amount_tax_excl_{{ $i }}" value=""/>
 
         <div class="form-control"><a target="_blank" href="{{ URL::to('products') }}/{{ $line->product_id }}/edit">{{ $line->reference }}</a></div></td>
 
      <td><input type="text" class="form-control" name="name_{{ $i }}" value="{{ $line->name }}" onclick="this.select()"/></td>
 
      <td><input type="number" step="any" id="quantity_{{ $i }}" class="form-control text-right" name="quantity_{{ $i }}"
         onkeyup="calculate_line({{ $i }})" onchange="calculate_line({{ $i }})" autocomplete="off" value="{{ $line->quantity }}"/></td>
 
      <td><button class="btn btn-md btn-danger" type="button" onclick="$('#line_{{ $i }}').remove();calculate_order();">
         <i class="fa fa-trash"></i></button></td>
 
      <td><input type="text" id="unit_final_price_{{ $i }}" name="unit_final_price_{{ $i }}" value="{{ $line->unit_final_price }}"
         class="form-control text-right" onkeyup="calculate_line({{ $i }})" onclick="this.select()" autocomplete="off"/></td>
 
      <td><input type="text" id="discount_percent_{{ $i }}" name="discount_percent_{{ $i }}" value="{{ $line->discount_percent }}"
         class="form-control text-right" onkeyup="calculate_line({{ $i }})" onclick="this.select()" autocomplete="off"/></td>
 
      <td><input type="text" class="form-control text-right" id="total_tax_excl_{{ $i }}" name="total_tax_excl_{{ $i }}"
         onkeyup="calculate_line({{ $i }}, 'net')" onclick="this.select()" autocomplete="off"/></td>
 
      <td>  
        {!! Form::select('tax_id_'.$i, array('0' => l('-- Please, select --', [], 'layouts')) + $taxList, $line->tax_id, array('class' => 'form-control', 'id' => 'tax_id_'.$i,
                                      'onchange' => 'calculate_line('.$i.')')) !!}
            </td>
 
      <td><input type="text" class="form-control text-right" id="total_tax_incl_{{ $i }}" name="total_tax_incl_{{ $i }}"
         onkeyup="calculate_line({{ $i }}, 'total')" onclick="this.select()" autocomplete="off"/></td></tr>




               @endforeach
            @endif
         </tbody>
         <tbody>
            <tr class="bg-info">
               <td>
               </td>
               <td>
                     <!-- input id="i_new_line_x" class="form-control" type="text" placeholder="Buscar" autocomplete="off"/ -->
                     <button id="i_new_line" class="btn btn-sm btn-primary" type="button">
                        <i class="fa fa-plus"></i>
                        &nbsp; {{l('New line...')}}
                     </button>

               </td>
               <td colspan="5" class="text-right" style="vertical-align: middle;">{{l('Order Discount (%)')}}: </td>
               <td class="{{ $errors->has('document_discount') ? 'has-error' : '' }}" style="background-color: #fff;">
                     <input class="form-control" type="text" name="document_discount" id="document_discount" 
                           placeholder="" value="{{ old('document_discount', isset($invoice) ? $invoice->document_discount : null) }}" 
                           onchange="calculate_document()" onkeyup="calculate_document()" onclick="this.select()" />
                    {{ $errors->first('document_discount',  '<span class="help-block">:message</span>') }}

                    <input type="hidden" id="order_gross_tax_excl" name="order_gross_tax_excl" value=""/>
                    <input type="hidden" id="order_gross_taxes" name="order_gross_taxes" value=""/>
                    <input type="hidden" id="order_gross_tax_incl" name="order_gross_tax_incl" value=""/>
               </td>
               <td>
                  <!-- div name="order_total_tax_excl" id="order_total_tax_excl" class="form-control text-right" style="font-weight: bold;"> </div -->
                  <input type="text" name="order_total_tax_excl" id="order_total_tax_excl" class="form-control text-right" style="font-weight: bold;"
                         value="0" xonchange="recalcular()" onfocus="this.blur();" autocomplete="off"/>
               </td>
               <td>
                  <!-- div name="order_total_taxes" id="order_total_taxes" class="form-control text-right" style="font-weight: bold;"> </div -->
                  <input type="text" name="order_total_taxes" id="order_total_taxes" class="form-control text-right" style="font-weight: bold;"
                         value="0" xonchange="recalcular()" onfocus="this.blur();" autocomplete="off"/>
               </td>
               <td>
                  <input type="text" name="order_total_tax_incl" id="order_total_tax_incl" class="form-control text-right" style="font-weight: bold;"
                         value="0" xonchange="recalcular()" onfocus="this.blur();" autocomplete="off"/>
               </td>
               <td>
               </td>
            </tr>
         </tbody>
      </table>
