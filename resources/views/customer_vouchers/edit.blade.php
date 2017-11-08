@extends('layouts.master')

@section('title') {{ l('Customer Vouchers - Edit') }} @parent @stop


@section('content')

<div class="row">
	<div class="col-md-6 col-md-offset-3" style="margin-top: 50px">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">{{ l('Edit Customer Voucher') }} :: {{ l('Invoice') }}: {{ $payment->paymentable->document_reference }} . {{ l('Due Date') }}: {{ $payment->due_date }}</h3>
		        <h3 class="panel-title" style="margin-top:10px;">{{ l('Amount') }}: {{ $payment->amount }} . {{ l('Currency') }}: {{ $payment->currency->name }}</h3>
		    </div>
			<div class="panel-body">

				@include('errors.list')

				{!! Form::model($payment, array('method' => 'PATCH', 'route' => array('customervouchers.update', $payment->id), 'onsubmit' => 'return checkFields();')) !!}

					@include('customer_vouchers._form')

				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@stop

@section('styles')

{{-- Date Picker --}}

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

@stop

@section('scripts')

<script>

function checkFields() 
{
  var amount = parseFloat($("#amount").val());
  var amount_initial = parseFloat($("#amount_initial").val());

   if ( (amount<=0.0) || (amount>amount_initial) ) 
   {
      $("#amount_check").show();
      return false;
   } else {
      $("#amount_check").hide();
      return true;
   }
}
  
</script>


 {{-- Date Picker --}}

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
{!! HTML::script('assets/jquery-ui/datepicker/datepicker-'.\App\Context::getContext()->language->iso_code.'.js'); !!}

<script>

  $(function() {
    $( "#due_date" ).datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
    });
  });

  $(function() {
    $( "#payment_date" ).datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
    });
  });
  
</script>


@stop