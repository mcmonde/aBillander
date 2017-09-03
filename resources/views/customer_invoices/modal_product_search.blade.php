
<div class="modal" id="modal_product_search">
   <div class="modal-dialog" style="width: 99%; max-width: 1000px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">{{ l('Search Products') }}</h4>
         </div>

         <div class="modal-body">
            <ul class="nav nav-tabs" id="nav_product_search">
               <li id="li_product_search"><a href="javascript:void(0);" id="b_product_search">{{ l('Coded Product') }}</a></li>
               <li id="li_new_service"   ><a href="javascript:void(0);" id="b_new_service"   >{{ l('Service (not coded)') }}</a></li>
               <li id="li_new_discount"  ><a href="javascript:void(0);" id="b_new_discount"  >{{ l('Discount') }}</a></li>
               <!-- li id="li_new_text_line" ><a href="javascript:void(0);" id="b_new_text_line" >Línea de texto</a></li -->
            </ul>
         </div>

         <div id="product_search" class="modal-body">
            <form id="f_product_search" name="f_product_search" action="" method="post" class="form">
               <input type="hidden" name="codcliente" value="{$fsc->cliente_s->codcliente}"/>
               <div class="container-fluid" style="xpadding-top: 20px;">
                  <div class="row">
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="input-group">
                           <input class="form-control" type="text" name="query" autocomplete="off"/>
                           <span class="input-group-btn">
                              <button class="btn btn-primary" type="submit">
                                 <span class="glyphicon glyphicon-search"></span>
                              </button>
                           </span>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <label>
                           <input type="checkbox" name="onhand_only" value="1" onchange="product_search()"/>
                           {{ l('Only Products with Stock') }}
                        </label>
                     </div>
                  </div>
               </div>
            </form>

         <div id="search_results" style="padding-top: 20px;"></div>

         </div>

         <div id="new_service" class="modal-body" style="display: none;">
            <form id="f_new_service" name="f_new_service" action="" method="post" class="form">
               <div class="form-group col-lg-12 col-md-12 col-sm-12">
                  {{ l('Description') }}
                  {!! Form::text('name', null, array('class' => 'form-control', 'id' => 'name', 'autocomplete' => 'off')) !!}
                  {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
               </div>
               <div class="form-group col-lg-3 col-md-3 col-sm-3">
                  {{ l('Cost Price') }}
                  {!! Form::text('cost_price', null, array('class' => 'form-control', 'id' => 'cost_price', 'autocomplete' => 'off')) !!}
                  {!! $errors->first('cost_price', '<span class="help-block">:message</span>') !!}
               </div>
               <div class="form-group col-lg-3 col-md-3 col-sm-3">
                  {{ l('Price') }}
                  {!! Form::text('price', null, array('class' => 'form-control', 'id' => 'price', 'autocomplete' => 'off')) !!}
                  {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
               </div>
               <div class="form-group col-lg-3 col-md-3 col-sm-3">
                  {{ l('Tax') }}
                  {!! Form::select('tax_id', array('0' => l('-- Please, select --', [], 'layouts')) + $taxList, null, array('class' => 'form-control', 'id' => 'tax_id')) !!}
               </div>

            <div class="form-group col-lg-2 col-md-2 col-sm-2">
                    {{ l('Is Shipping Cost?') }}<!-- label class="control-label" - - >Guardar como Borrador?< ! - - /label -->
               <div>
                 <div class="radio-inline">
                   <label>
                     {!! Form::radio('is_shipping', '1', false, ['id' => 'is_shipping_on']) !!}
                     {!! l('Yes', [], 'layouts') !!}
                   </label>
                 </div>
                 <div class="radio-inline">
                   <label>
                     {!! Form::radio('is_shipping', '0', true, ['id' => 'is_shipping_off']) !!}
                     {!! l('No', [], 'layouts') !!}
                   </label>
                 </div>
               </div>
             </div>

               <div class="text-right">
                  <a href="javascript:void(0);" class="btn btn-sm btn-primary" onclick="add_service_to_order();return false;">
                     <span class="glyphicon glyphicon-shopping-cart"></span>
                     &nbsp; {{ l('Save', [], 'layouts') }}</a>
               </div>
            </form>
         </div>


         <div id="new_discount" class="modal-body" style="display: none;">
            <form id="f_new_discount" name="f_new_discount" action="" method="post" class="form">
               <div class="row">
               <div class="form-group col-lg-12 col-md-12 col-sm-12">
                  {{ l('Description') }}
                  {!! Form::text('discount_name', null, array('class' => 'form-control', 'id' => 'discount_name', 'autocomplete' => 'off')) !!}
                  {!! $errors->first('discount_name', '<span class="help-block">:message</span>') !!}
               </div>
               <div class="form-group col-lg-3 col-md-3 col-sm-3">
                  {{ l('Price') }}
                  {!! Form::text('discount_price', null, array('class' => 'form-control', 'id' => 'discount_price', 'autocomplete' => 'off')) !!}
                  {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
               </div>
               <div class="form-group col-lg-3 col-md-3 col-sm-3">
                  {{ l('Tax') }}
                  {!! Form::select('discount_tax_id', array('0' => l('-- Please, select --', [], 'layouts')) + $taxList, null, array('class' => 'form-control', 'id' => 'discount_tax_id')) !!}
               </div>
               </div>

               <div class="text-right">
                  <a href="javascript:void(0);" class="btn btn-sm btn-primary" onclick="add_discount_to_order();return false;">
                     <span class="glyphicon glyphicon-shopping-cart"></span>
                     &nbsp; {{ l('Save', [], 'layouts') }}</a>
               </div>
            </form>
         </div>


      </div>
   </div>
</div>
