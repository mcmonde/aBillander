@extends('layouts.master')

@section('title') {{ l('Customer Invoices - Edit') }} @parent @stop


@section('content')
 
            @include('customer_invoices.edit_invoice')

@stop

{{-- ***************************************************************************************************** --}}

@section('styles')

   {!! HTML::style('../../aBillander/public/assets/lib/autocomplete/content/styles.css') !!}

{{-- Date Picker --}}

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

@stop

{{-- ***************************************************************************************************** --}}


@section('scripts')

            @include('customer_invoices.js.create_invoice_js')

@stop