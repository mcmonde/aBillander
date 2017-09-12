<div class="table-responsive">
	<table class="table table-hover">
		<thead>
			<tr>
                <th class="text-left">Referencia</th>
                <th class="text-left">Nombre</th>
                <th class="text-left">Notas</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Pendiente</th>
                <th class="text-right">Reservado</th>
                <th class="text-right">Disponible</th>
                <th></th>
			</tr>
		</thead>
		<tbody>
			<tr>  
        <td class="text-left">
            <a onclick="add_product_to_order( {{ $product_string }}, {} )" href="javascript:void(0);">{{$product->reference}}</a>
        </td>
        <td class="text-left">
            {{$product->name}}
        </td>
				<td class="text-left">
                     {{$product->notes}}
                </td>
                <td class="text-right">{{$product->quantity_onhand}}</td>
                <td class="text-right">{{$product->quantity_onorder}}</td>
                <td class="text-right">{{$product->quantity_allocated}}</td>
                <td class="text-right" id="quantity_available"><strong>{{$product->quantity_onhand+$product->quantity_onorder-$product->quantity_allocated}}</strong></td>
                <script type="text/javascript">
                	if ( parseFloat($("#quantity_available").text()) <= 0 ) $("#quantity_available").addClass('alert-danger');
                </script>
                <td>
                  @if (!$product->combinations->count())
                  <a title=" Añadir al Pedido " onclick="add_product_to_order( {{ $product_string }}, '{}' )" href="javascript:void(0);">
                		<button type="button" class="btn btn-xs btn-success">
                			<i class="fa fa-shopping-cart"></i>
                		</button>
                	</a>
                  @endif
                </td>
            </tr>                          
		</tbody>
	</table>
</div>

<!-- Combination List -->

@if ($product->combinations->count())

<div class="panel panel-info" style="margin-bottom: 0px;">
  <div class="panel-heading">
    <h3 class="panel-title"><b>{{l('Combinations')}}</b></h3>
  </div>
</div>

<!-- Combination List -->
<div id="panel_combination_list">

    <!-- div class="page-header">
        <div class="pull-right" style="padding-top: 4px;">
            <a href="{{ URL::to('products/create') }}" class="btn btn-sm btn-success" 
                    title=" Añadir Nuevo Producto "><i class="fa fa-plus"></i> Nuevo</a> 
        </div>
        <h2>
            {{ l('Combinations') }}
        </h2>        
    </div -->

    <div id="div_combinations">
       <div class="table-responsive">

    <table id="products" class="table table-hover">
        <thead>
            <tr>
          <th>{{l('ID', [], 'layouts')}}</th>
          <th>{{l('Reference')}}</th>
          <th>{{l('Options')}}</th>
          <th>{{l('Stock')}}</th>
          <th class="text-right">Pendiente</th>
          <th class="text-right">Reservado</th>
          <th class="text-right">Disponible</th>
          <th class="text-center">{{l('Active', [], 'layouts')}}</th>
          <th class="text-right"> </th>
        </tr>
      </thead>
      <tbody>
      @foreach ($product->combinations as $combination)
        <tr>
          <td>{{ $combination->id }}</td>
          <td>{{ $combination->reference }}</td>
          <td>
              {!! $combination->name() !!}
              {{-- json_encode( array_add( $combination, 'combination_name', $combination->name() ) )  --}}
          </td>
          <td>{{ $combination->quantity_onhand }}</td>
          <td class="text-right">{{$combination->quantity_onorder}}</td>
          <td class="text-right">{{$combination->quantity_allocated}}</td>
          <td class="text-right"><strong>{{$combination->quantity_onhand+$combination->quantity_onorder-$combination->quantity_allocated}}</strong></td>
          <td class="text-center">@if ($combination->active) <i class="fa fa-check-square" style="color: #38b44a;"></i> @else <i class="fa fa-square-o" style="color: #df382c;"></i> @endif</td>
               <td class="text-right">
                    
                  <a title=" Añadir al Pedido " onclick="add_product_to_order( {{ $product_string }}, {{ json_encode( array_add( $combination, 'combination_name', $combination->name() ) ) }} )" href="javascript:void(0);">
                    <button type="button" class="btn btn-xs btn-success">
                      <i class="fa fa-shopping-cart"></i>
                    </button>
                  </a>

                </td>
        </tr>
      @endforeach
        </tbody>
    </table>

       </div>
    </div>

</div>

@endif

<!-- Combination List ENDS -->



<div class="panel panel-info" style="margin-bottom: 0px;">
  <div class="panel-heading">
    <h3 class="panel-title"><b>Cliente</b>: {{ $customer->name_fiscal }}</a></h3>
  </div>
  <div class="panel-body">
    <b>Tarifa</b>: @if ($customer->currentpricelist()) 
                        {{ $customer->currentpricelist()->name }}
                   @else {{ l('None') }} @endif</a>
  </div>
</div>

<div class="modal-body">
   <span id="detalle">
      <table class="table table-condensed">
        <thead>
          <tr>
            <th class="text-left">PVP</th>
            <th class="text-left">Coste</th>
            <th class="text-left">Margen</th>
            <th class="text-right">PVP Cliente</th>
            <th class="text-right">Descuento</th>
            <th class="text-right">Margen Cliente</th>
            <th class="text-right">PVP+IVA Cliente</th>
          </tr>
        </thead>
        <tbody id="lineas_detalle">
          
          <tr>
            <td>{{$product->price}}</td>
            <td>{{$product->cost_price}}</td>
            <td>{{\App\Calculator::margin($product->cost_price, $product->price)}}</td>
            <td class="text-right">{{$product->price_customer}}</td>
            <td class="text-right">{{$product->price-$product->price_customer}} ({{100*($product->price-$product->price_customer)/$product->price}}%)</td>
            <td class="text-right">{{\App\Calculator::margin($product->cost_price, $product->price_customer)}}</td>
            <td class="text-right">{{$product->price_customer_with_tax}}</td>
          </tr>
          
        </tbody>
      </table>
   </span>

   <br><br>
   
   <b>Margen</b>: 
   @if ( \App\Configuration::get('MARGIN_METHOD') == 'CST' )  
      se calcula sobre el <i>Precio de Coste</i>.
   @else
      se calcula sobre el <i>Precio de Venta</i>.
   @endif
   <br>
</div>