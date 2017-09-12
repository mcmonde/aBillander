
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <div class="pull-right">
                <a href="{{ URL::to('customerinvoices') }}" class="btn btn-default"><i class="fa fa-mail-reply"></i> {{l('Back to Customer Invoices')}}</a>
            </div>
            
              <h2><a title=" Ir a la Ficha del Cliente " href="{{ URL::to('customerinvoices') }}">{{l('Customer Invoices')}}</a> <span style="color: #cccccc;">/</span> 
                  {{l('Invoice to')}} <span class="lead well well-sm"><a href="{{ URL::to('customers/' . $customer->id . '/edit') }}" xtarget="_blank">{{ $customer->name_fiscal }}</a>
               <a title=" Ver dirección de Facturación! " href="javascript:void(0);">
                  <button type="button" class="btn btn-xs btn-success" data-toggle="popover" data-placement="right" 
                          title="{{l('Invoicing Address')}}" data-content="
                                {{$customer->name_fiscal}}<br />
                                {{l('VAT ID')}}: {{$customer->identification}}<br />
                                {{ $invoicing_address->address1 }} {{ $invoicing_address->address2 }}<br />
                                {{ $invoicing_address->postcode }} {{ $invoicing_address->city }}, {{ $invoicing_address->state }}<br />
                                {{ $invoicing_address->country }}
                                <br />
                          ">
                      <i class="fa fa-info-circle"></i>
                  </button>
               </a></span>
             </h2>

        </div>
    </div>
</div> 

   @include('customer_invoices.modal_product_search')

<!-- Invoice Menu -->   
   <ul class="nav nav-tabs" role="tablist">
      <li class="lead" id="tab_header"   ><a href="javascript:void(0);" id="b_header"   >{{l('Header')}}</a></li>
      <li class="lead" id="tab_lines"    ><a href="javascript:void(0);" id="b_lines"    >{{l('Lines')}}</a></li>
      <li class="lead" id="tab_profit"   ><a href="javascript:void(0);" id="b_profit"   >{{l('Profitability')}}</a></li>
      <li class="lead" id="tab_payments" ><a href="javascript:void(0);" id="b_payments" >{{l('Payments')}}</a></li>
      @if ( $customer->einvoice )
        <li class="pull-right" id="tab_tlights" ><a href="javascript:void(0);" id="b_tlights" ><span class="label label-success">{{l('Accepts eInvoice')}}</span></a></li>
      @else
        <li class="pull-right" id="tab_tlights" ><a href="javascript:void(0);" id="b_tlights" ><span class="label label-warning">{{l('Does NOT accept eInvoice')}}</span></a></li>
      @endif
      <li class="pull-right" id="tab_tlights" ><a href="javascript:void(0);" id="b_tlights" ><span class="label label-danger">{{l('NOT Saved', [], 'layouts')}}</span></a></li>
      <li class="pull-right" id="tab_tlights" ><a href="javascript:void(0);" id="b_tlights" ><span class="label label-info"> {{l('DRAFT', [], 'layouts')}} </span></a></li>
      @if ( $customer->sales_equalization )
        <li class="pull-right" id="tab_tlights" ><a href="javascript:void(0);" id="b_tlights" ><span class="label label-primary"> {{l('Equalization Tax')}} </span></a></li>
      @endif
   </ul>
 

{{-- Form::open(array('url' => 'customerinvoices', 'id' => 'f_new_order', 'name' => 'f_new_order', 'class' => 'form')) --}}
{!! Form::model($invoice, array('method' => 'POST', 'route' => array('customerinvoices.store'), 'id' => 'f_new_order', 'name' => 'f_new_order', 'class' => 'form')) !!}

   <input type="hidden" id="nbrlines" name="nbrlines" value="{{ count($invoice->customerInvoiceLines) }}"/>
   <input type="hidden" name="customer_id" value="{{$customer->id}}"/>
   <input type="hidden" name="einvoice" value="{{$customer->accept_einvoice}}"/>
   <input type="hidden" name="invoicing_address_id" value="{{$customer->invoicing_address_id}}"/>

<!-- id="div_header" -->  
   <div class="container-fluid">
      <div class="row" id="div_header" style="padding-top: 20px;">

      {{-- @include('customer_invoices.create_header') --}}
      @include('customer_invoices._invoice_header')

      </div>

   </div>


<!-- id="div_lines" -->
   <div class="table-responsive" id="div_lines" style="padding-top: 20px;">

   {{-- @include('customer_invoices.create_lines') --}}
   @include('customer_invoices._invoice_lines')

   </div>


<!-- div id="div_footer" -->
  <div id="div_footer">

  @include('customer_invoices._invoice_footer')

  </div>

{!! Form::close() !!}


<!-- id="div_profit" -->
   <div class="table-responsive" id="div_profit" style="padding-top: 20px;">

      @include('customer_invoices.create_profit')

   </div>


<!-- id="div_payments" -->
   <div class="table-responsive" id="div_payments" style="padding-top: 20px;">

      @include('customer_invoices.create_payments')

   </div>
   