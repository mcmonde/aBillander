@extends('layouts.master')

@section('title') {{ l('WooCommerce Orders') }} @parent @endsection


@section('content')

<div class="page-header">
    <div class="pull-right" style="padding-top: 4px;">

    <a href="{{ URL::route('worders.imported') }}" class="btn btn-grey" 
        title="{{l('Orders')}}"><i class="fa fa-shopping-cart"></i> {{l('Orders')}}</a>

    <div class="btn-group" style="margin-right: 152px">
        <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" title="{{l('Configuration', [], 'layouts')}}"><i class="fa fa-cog"></i> {{l('Configuration', [], 'layouts')}} &nbsp;<span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="{{ URL::route('wooconnect.configuration') }}">{{l('Shop Configuration')}}</a></li>
          <li><a href="{{ URL::route('wooconnect.configuration') }}">{{l('WooConnect Configuration')}}</a></li>
          <li><a href="{{ URL::route('wooconnect.configuration.taxes') }}">{{l('Taxes Dictionary')}}</a></li>
          <li><a href="{{ URL::route('wooconnect.configuration.paymentgateways') }}">{{l('Payment Gateways Dictionary')}}</a></li>
          <li class="divider"></li>
          <!-- li><a href="#">Separated link</a></li -->
        </ul>
    </div>

    </div>

    <h2>
        {{ l('WooCommerce Orders') }}
    </h2>        
</div>

<div name="search_filter" id="search_filter">
<div class="row" style="padding: 0 20px">
    <div class="col-md-12 xcol-md-offset-3">
        <div class="panel panel-info">
            <div class="panel-heading"><h3 class="panel-title">{{ l('Filter Records', [], 'layouts') }}</h3></div>
            <div class="panel-body">

                {!! Form::model(Request::all(), array('route' => 'worders.index', 'method' => 'GET')) !!}

<div class="row">
<div class="form-group col-lg-2 col-md-2 col-sm-2">
    {!! Form::label('after', l('Date from')) !!}
    {!! Form::text('after', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group col-lg-2 col-md-2 col-sm-2">
    {!! Form::label('before', l('Date to')) !!}
    {!! Form::text('before', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group col-lg-2 col-md-2 col-sm-2">
    {!! Form::label('status', l('Status')) !!}
    {!! Form::select('status', ['' => l('All', [], 'layouts')] + \aBillander\WooConnect\WooConnector::getOrderStatusList(), null, array('class' => 'form-control', 'id' => 'status')) !!}
</div>

<div class="form-group col-lg-2 col-md-2 col-sm-2" style="padding-top: 22px">
{!! Form::submit(l('Filter', [], 'layouts'), array('class' => 'btn btn-success')) !!} &nbsp; 
{!! link_to_route('worders.index', l('Reset', [], 'layouts'), null, array('xclass' => 'btn btn-warning')) !!}
</div>

</div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
</div>

<div id="div_orders">
   <div class="table-responsive">

@if ($orders->count())
<table id="orders" class="table table-hover">
	<thead>
		<tr>
			<th class="text-left">{{l('Order #', [], 'layouts')}}</th>
			<th>{{l('Customer')}}</th>
			<!-- th>{{l('Address')}}</th -->
			<th>{{l('Contact')}}</th>
      <th>{{l('Order Date')}}</th>
      <th>{{l('Payment Date')}}</th>
      <th>{{l('Download Date')}}</th>
      <th>{{l('Invoice Date')}}</th>
      <th>{{l('Status')}}</th>
      <th>{{l('Total')}}</th>
			<th> </th>
		</tr>
	</thead>
	<tbody>
	@foreach ($orders as $order)

@php

    $order = aBillander\WooConnect\WooOrder::viewIndexTransformer( $order );

@endphp

		<tr>
			<td>{{ $order["id"] }}</td>
			<td>{{ $order["billing"]["first_name"].' '.$order["billing"]["last_name"] }}<br />
			    {{ $order["shipping"]["address_1"] }}</td>
			<td>{{ $order["billing"]["phone"] }}</td>
      <td>{{ $order["date_created"] }}</td>
      @if ($order["date_paid"]) 
      <td>{{ $order["date_paid"] }}</td>
      @else
      <td class="danger"> </td>
      @endif
      @if ($order["date_abi_exported"]) 
      <td>{{ $order["date_abi_exported"] }}</td>
      @else
      <td class="warning"> </td>
      @endif
      <td>{{ $order["date_abi_invoiced"] }}</td>
      <td>{{ $order["status"] }}</td>
      <td>{{ $order['total'] }}</td>

			<td class="text-right">

                <a class='open-AddBookDialog btn btn-sm btn-warning' href="{{ URL::route('worders.update', [$order["id"]] + $query ) }}" data-target='#myModalOrder' data-id="{{ $order["id"] }}" data-status="{{ $order["status"] }}" data-statusname="{{ \aBillander\WooConnect\WooConnector::getOrderStatusList()[ $order["status"] ] }}" data-toggle="modal" onClick="return false;" title="{{l('Update', [], 'layouts')}}"><i class="fa fa-pencil-square-o"></i></a>

                <a class="btn btn-sm btn-success" href="{{ URL::to('wooc/worders/' . $order["id"]) }}" title="{{l('Show', [], 'layouts')}}"><i class="fa fa-eye"></i></a>

                <a class="btn btn-sm btn-grey" href="{{ URL::route('worders.import', [$order["id"]] ) }}" title="{{l('Import', [], 'layouts')}}"><i class="fa fa-download"></i></a>

                <a class="btn btn-sm btn-info" href="{{ URL::route('worders.invoice', [$order["id"]] ) }}" title="{{l('Invoice', [], 'layouts')}}"><i class="fa fa-file-text"></i></a>

                <!-- a class='open-deleteDialog btn btn-danger' data-target='#myModal1' data-id="{{ $order["id"] }}" data-toggle='modal'>{{l('Delete', [], 'layouts')}}</a -->

			</td>
		</tr>
	@endforeach
	</tbody>
</table>
{{ $orders->appends( Request::all() )->render() }}
<ul class="pagination"><li class="active"><span style="color:#333333;">{{l('Found :nbr record(s)', [ 'nbr' => $orders->total() ], 'layouts')}} </span></li></ul>
@else
<div class="alert alert-warning alert-block">
    <i class="fa fa-warning"></i>
    {{l('No records found', [], 'layouts')}}
</div>
@endif

   </div>
</div>

@endsection


{{-- *************************************** --}}


@section('modals')

@parent

<div class="modal fade" id="myModalOrder" role="dialog">

   <div class="modal-dialog">



       <!-- Modal content-->

       <div class="modal-content">

           <div class="modal-header">

               <button type="button" class="close" data-dismiss="modal">&times;</button>

               <h4 class="modal-title">{{l('Change WooCommerce Order Status')}}</h4>

           </div>

           <div class="modal-body">

               <!-- p>Some text in the modal.</p -->


{!! Form::open(array('url' => '', 'method' => 'PATCH', 'id' => 'change_woo_order_status', 'name' => 'change_woo_order_status', 'class' => 'form')) !!}

                  
<div class="row">
		<div class="form-group col-lg-6 col-md-6 col-sm-6">
		                       <label for="bookId" xclass="col-sm-3 control-label text-right">{{l('Order ID')}}</label>
		                       <input type="text" class="form-control" name="bookId" id="bookId" value="" onfocus="this.blur();">
		</div>

		<div class="form-group col-lg-6 col-md-6 col-sm-6">
		                       <label for="bookStatus" xclass="col-sm-3 control-label text-right">{{l('Order Status')}}</label>
		                       <input type="text" class="form-control" name="bookStatus" id="bookStatus" value="" onfocus="this.blur();">
		</div>
</div>

<div class="row">
  <div class="form-group">
                         <label for="order_status">{{l('New Order Status')}}</label>

                         {!! Form::select('order_status', \aBillander\WooConnect\WooConnector::getOrderStatusList(), null, array('class' => 'form-control', 'id' => 'order_status')) !!}
  </div>

  <div class="form-group col-lg-4 col-md-4 col-sm-4" id="div-order_set_paid">
   {!! Form::label('order_set_paid', l('Mark as Paid'), ['class' => 'control-label']) !!}
   <div>
     <div class="radio-inline">
       <label>
         {!! Form::radio('order_set_paid', '1', false, ['id' => 'order_set_paid_on']) !!}
         {!! l('Yes', [], 'layouts') !!}
       </label>
     </div>
     <div class="radio-inline">
       <label>
         {!! Form::radio('order_set_paid', '0', true, ['id' => 'order_set_paid_off']) !!}
         {!! l('No', [], 'layouts') !!}
       </label>
     </div>
   </div>
  </div>

</div>


                   



                   <div class="modal-footer">

                       <button type="button" class="btn xbtn-sm btn-warning" data-dismiss="modal">{{l('Cancel', [], 'layouts')}}</button>
                       <button type="submit" class="btn btn-success" name="btn-update" onclick="this.disabled=true;this.form.submit();">
                       	<i class="fa fa-thumbs-up"></i>
                  		&nbsp; {{l('Update', [], 'layouts')}}</button>

                   </div>

{!! Form::close() !!}

           </div>

       </div>

   </div>

</div>

@endsection


{{-- *************************************** --}}


@section('scripts') @parent 

<script>

    $(document).ready(function () {
          // $(document).on("click", ".open-AddBookDialog", function() {
            $('.open-AddBookDialog').click(function (evnt) {

               var href = $(this).attr('href');
               var myBookId = $(this).attr('data-id');
               var myBookStatus = $(this).attr('data-status');
               var myBookStatusname = $(this).attr('data-statusname');

               $('#change_woo_order_status').attr('action', href);
               $(".modal-body #bookId").val(myBookId);
               $(".modal-body #bookStatus").val(myBookStatusname);
               $(".modal-body #order_status").val(myBookStatus);

               // https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece
               // $('#btn-update').prop('disabled', false);

               $('#myModalOrder').modal({show: true});

               return false;

           });
    });

</script>

@endsection