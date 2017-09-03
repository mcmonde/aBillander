
      <table class="table table-condensed">
         <thead>
            <tr>
               <th class="text-left" style="width: 60px"></th>
               <th class="text-left">Referencia</th>
               <th class="text-left">Descripción</th>
               <th class="text-right" width="90">Cantidad</th>
               <th></th>
               <th class="text-right">PVP</th>
               <th class="text-right" width="90">Dto. %</th>
               <th class="text-right">Neto</th>
               <th class="text-right" width="115">Impuestos</th>
               <th class="text-right">Total</th>
            </tr>
         </thead>
         <tbody id="order_lines">
            @if ( isset($invoice->customerInvoiceLines) )
               @foreach ( $invoice->customerInvoiceLines as $line )
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
                        <span class="glyphicon glyphicon-plus"></span>
                        &nbsp; Nueva línea...
                     </button>

               </td>
               <td colspan="4" class="text-right" style="vertical-align: middle;">Descuento Especial (%): </td>
               <td class="{{{ $errors->has('document_discount') ? 'has-error' : '' }}}" style="background-color: #fff;">
                     <input class="form-control" type="text" name="document_discount" id="document_discount" 
                           placeholder="" value="{{{ Input::old('document_discount', isset($invoice) ? $invoice->document_discount : null) }}}" 
                           onchange="calculate_order()" onkeyup="calculate_order()" onclick="this.select()" />
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
            </tr>
         </tbody>
      </table>
