@extends('layouts.master')

@section('title') {{ l('WooCommerce Orders') }} @parent @endsection


@section('content')

<div class="page-header">
    <!-- div class="pull-right" style="padding-top: 4px;">
        <a href="{{ URL::to('orders/create') }}" class="btn btn-sm btn-success" 
        		title="{{l('Add New Item', [], 'layouts')}}"><i class="fa fa-plus"></i> {{l('Add New', [], 'layouts')}}</a>
    </div -->
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

                {!! Form::model(Request::all(), array('route' => 'worders', 'method' => 'GET')) !!}

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
{!! link_to_route('worders', l('Reset', [], 'layouts'), null, array('xclass' => 'btn btn-warning')) !!}
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
			<th>{{l('Address')}}</th>
			<th>{{l('Contact')}}</th>
			<th>{{l('Order Date')}}</th>
			<th>{{l('Status')}}</th>
			<th> </th>
		</tr>
	</thead>
	<tbody>
	@foreach ($orders as $order)
		<tr>
			<td>{{ $order["id"] }}</td>
			<td>{{ $order["billing"]["first_name"].' '.$order["billing"]["last_name"] }}</td>
			<td>{{ $order["shipping"]["address_1"] }}</td>
			<td>{{ $order["billing"]["phone"] }}</td>
			<td>{{ $order["date_created"] }}</td>
			<td>{{ $order["status"] }}</td>

			<td class="text-right">

                <a class='open-AddBookDialog btn btn-sm btn-warning' href="{{ URL::route('wostatus', [$order["id"]] + $query ) }}" data-target='#myModal' data-id="{{ $order["id"] }}" data-status="{{ $order["status"] }}" data-statusname="{{ \aBillander\WooConnect\WooConnector::getOrderStatusList()[ $order["status"] ] }}" data-toggle='modal'><i class="fa fa-pencil-square-o"></i> &nbsp; {{l('Update', [], 'layouts')}}</a>

                <a class="btn btn-sm btn-success" href="{{ URL::to('wooc/orders/' . $order["id"]) }}" title="{{l('Show', [], 'layouts')}}"><i class="fa fa-eye"></i></a>

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

<div class="modal fade" id="myModal" role="dialog">

   <div class="modal-dialog">



       <!-- Modal content-->

       <div class="modal-content">

           <div class="modal-header">

               <button type="button" class="close" data-dismiss="modal">&times;</button>

               <h4 class="modal-title">{{l('Change WooCommerce Order Status')}}</h4>

           </div>

           <div class="modal-body">

               <!-- p>Some text in the modal.</p -->


{!! Form::open(array('url' => '', 'id' => 'change_woo_order_status', 'name' => 'change_woo_order_status', 'class' => 'form')) !!}

                  
<div class="row">
		<div class="form-group col-lg-6 col-md-6 col-sm-6">
		                       <label for="bookId" xclass="col-sm-3 control-label text-right">{{l('Order ID')}}</label>
		                       <input type="text" class="form-control" name="bookId" id="bookId" value="">
		</div>

		<div class="form-group col-lg-6 col-md-6 col-sm-6">
		                       <label for="bookStatus" xclass="col-sm-3 control-label text-right">{{l('Order Status')}}</label>
		                       <input type="text" class="form-control" name="bookStatus" id="bookStatus" value="">
		</div>
</div>

<div class="form-group">
                       <label for="order_status">{{l('New Order Status')}}</label>

                       {!! Form::select('order_status', \aBillander\WooConnect\WooConnector::getOrderStatusList(), null, array('class' => 'form-control', 'id' => 'order_status')) !!}
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

           $(document).on("click", ".open-AddBookDialog", function() {

               var href = $(this).attr('href');
               var myBookId = $(this).data('id');
               var myBookStatus = $(this).data('status');
               var myBookStatusname = $(this).data('statusname');

               $('#change_woo_order_status').attr('action', href);
               $(".modal-body #bookId").val(myBookId);
               $(".modal-body #bookStatus").val(myBookStatusname);
               $(".modal-body #order_status").val(myBookStatus);

           });

</script>

@endsection