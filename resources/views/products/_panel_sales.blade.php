
<div id="panel_sales"> 

{!! Form::model($product, array('route' => array('products.update', $product->id), 'method' => 'PUT', 'class' => 'form')) !!}
<input type="hidden" value="sales" name="tab_name" id="tab_name">

<div class="panel panel-primary">
   <div class="panel-heading">
      <h3 class="panel-title">{{ l('Sales') }}</h3>
   </div>
   <div class="panel-body">

<!-- Sales Prices -->

        <div class="row">
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('cost_price') ? 'has-error' : '' }}">
                     {{ l('Cost Price') }}
                     {!! Form::text('cost_price', null, array('class' => 'form-control', 'id' => 'cost_price', 'autocomplete' => 'off', 
                                      'onfocus' => 'this.blur()', 'onclick' => 'this.select()', 'onkeyup' => 'new_margin()', 'onchange' => 'new_margin()')) !!}
                     {!! $errors->first('cost_price', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('margin') ? 'has-error' : '' }}">
                     {{ l('Margin (%)') }}
                         <a href="javascript:void(0);">
                            <button type="button" xclass="btn btn-xs btn-white" data-toggle="popover" data-placement="top" 
                                    data-content="{{ \App\Configuration::get('MARGIN_METHOD') == 'CST' ?
                                        l('Margin calculation is based on Cost Price', [], 'layouts') :
                                        l('Margin calculation is based on Sales Price', [], 'layouts') }}">
                                <i class="fa fa-info-circle"></i>
                            </button>
                         </a>
                     {!! Form::text('margin', null, array('class' => 'form-control', 'id' => 'margin', 'autocomplete' => 'off', 
                                      'onclick' => 'this.select()', 'onkeyup' => 'new_price()', 'onchange' => 'new_price()')) !!}
                     {!! $errors->first('margin', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('price') ? 'has-error' : '' }}">
                     {{ l('Customer Price') }}
                     {!! Form::text('price', null, array('class' => 'form-control', 'id' => 'price', 'autocomplete' => 'off', 
                                      'onclick' => 'this.select()', 'onkeyup' => 'new_margin()', 'onchange' => 'new_margin()')) !!}
                     {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
                  </div>
                 <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('tax_id') ? 'has-error' : '' }}">
                    {{ l('Tax') }}
                    {!! Form::select('tax_id', array('0' => l('-- Please, select --', [], 'layouts')) + $taxList, null, array('class' => 'form-control', 'id' => 'tax_id',
                                      'onchange' => 'new_margin()')) !!}
                    {!! $errors->first('tax_id', '<span class="help-block">:message</span>') !!}
                 </div>
                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('price_tax_inc') ? 'has-error' : '' }}">
                     {{ l('Customer Price (with Tax)') }}
                     {!! Form::text('price_tax_inc', null, array('class' => 'form-control', 'id' => 'price_tax_inc', 'autocomplete' => 'off', 
                                      'onclick' => 'this.select()', 'onkeyup' => 'new_margin_price()', 'onchange' => 'new_margin_price()')) !!}
                     {!! $errors->first('price_tax_inc', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

        <div class="row">
        </div>

        <div class="row">
        </div>

        <div class="row">
        </div>

<!-- Sales Prices ENDS -->

   </div>

   <div class="panel-footer text-right">
      <button class="btn btn-sm btn-info" type="submit" onclick="this.disabled=true;this.form.submit();">
         <i class="fa fa-hdd-o"></i>
         &nbsp; {{l('Save', [], 'layouts')}}
      </button>
   </div>

</div>

{!! Form::close() !!}

<!-- Price List -->

<div id="panel_sales_detail">

    <div class="page-header">
        <h3>
            <span style="color: #dd4814;">{{ l('Price Lists') }}</span> <span style="color: #cccccc;">/</span> {{ $product->name }}
        </h3>        
    </div>

    <div id="div_aBook">
       <div class="table-responsive">

    @if ($pricelists->count())
    <table id="aBook" class="table table-hover">
        <thead>
            <tr>
                <th class="text-left">{{l('ID', [], 'layouts')}}</th>
                <th class="text-left">{{l('Price List Name')}}</th>
                <th class="text-left">{{l('Sales Price')}}</th>
                <th class="text-left">{{l('Discount (%)')}}</th>
                <th class="text-left">{{l('Margin (%)')}}</th>
                <th class="text-left">{{l('Price with Tax')}}</th>
                <th class="text-right"> </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pricelists as $pricelist)
            <?php $line_price = ( ( ($pricelist->type == 0) AND $pricelist->price_is_tax_inc ) 
                          ? $product->price_list($pricelist->id)->price/(1.0+($product->tax->percent/100.0))
                          : $product->price_list($pricelist->id)->price 
                                ); ?>
            <tr>
                <td>{{ $pricelist->id }}</td>
                <td>{{ $pricelist->name }}<br />
                    <span class="label label-success">{{ $pricelist->getType() }}</span>
                    <span class="label label-warning">{{ $pricelist->getExtra() }}</span></td>
                <td>{{ $line_price }} {{-- $product->price_list($pricelist->id)->price_tax_excl() --}}</td>
                <td>{{ \App\FP::percent(\App\Calculator::discount( $product->price, $line_price )) }}</td>
                <td>{{ \App\FP::percent(\App\Calculator::margin( $product->cost_price, $line_price )) }}</td>
                <td>{{ $line_price*(1.0+($product->tax->percent/100.0)) }}</td>
                <td class="text-right">
                    <a class="btn btn-sm btn-warning" href="{{ URL::to('prices/' . $product->price_list($pricelist->id)->id . '/edit?back_route=' . urlencode('products/' . $product->id . '/edit#sales')) }}" title="{{l('Edit', [], 'layouts')}}"><i class="fa fa-pencil"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="alert alert-warning alert-block">
        <i class="fa fa-warning"></i>
        {{l('No records found', [], 'layouts')}}
    </div>
    @endif

       </div>
    </div>



</div>
<!-- Price List ENDS -->

</div>

@section('scripts') 
 
@include('products._calculator_js')

<script type="text/javascript">

$(document).ready(function() {
   new_margin();
});

</script>

@append