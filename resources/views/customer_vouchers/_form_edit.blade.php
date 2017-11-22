
        <input type="hidden" name="action" value="edit"/>

<div class="row">
<div class="form-group col-lg-8 col-md-8 col-sm-8">
    {!! Form::label('name', l('Subject')) !!}
    {!! Form::text('name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group col-lg-4 col-md-4 col-sm-4">
    {!! Form::label('status_name', l('Status', [], 'layouts')) !!}
    {!! Form::text('status_name', l($payment->status, [], 'appmultilang'), array('class' => 'form-control', 'onfocus' => 'this.blur()')) !!}
    {!! Form::hidden('status', null, array('id' => 'status')) !!}
</div>
</div>

<div class="row">
<div class="form-group col-lg-4 col-md-4 col-sm-4">
    {!! Form::label('due_date', l('Due Date')) !!}
    {!! Form::text('due_date', null, array('class' => 'form-control')) !!}
</div>
<!-- div class="form-group col-lg-4 col-md-4 col-sm-4">
    {!! Form::label('payment_date', l('Payment Date')) !!}
    {!! Form::text('payment_date', null, array('class' => 'form-control')) !!}
</div -->
<div class="form-group col-lg-4 col-md-4 col-sm-4">
    {!! Form::label('amount', l('Amount')) !!}
    {!! Form::text('amount', null, array('id' => 'amount', 'class' => 'form-control', 'onclick' => 'this.select()', 'onkeyup' => 'checkFields()', 'onchange' => 'checkFields()')) !!}
    {!! Form::hidden('amount_initial', $payment->amount, array('id' => 'amount_initial')) !!}
</div>
</div>

<div class="row" name="voucher_next" id="voucher_next" style="display: none;">
<div class="form-group col-lg-4 col-md-4 col-sm-4">
    {!! Form::label('due_date_next', l('Next Due Date')) !!}
    {!! Form::text('due_date_next', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group col-lg-4 col-md-4 col-sm-4">
    {!! Form::label('amount_next', l('Amount')) !!}
    {!! Form::text('amount_next', 0.0, array('id' => 'amount_next', 'class' => 'form-control', 'onfocus' => 'this.blur()')) !!}
</div>
</div>

<div class="alert alert-danger alert-block" name="amount_check" id="amount_check" style="display: none;">
  <strong>{!! l('Error', [], 'layouts') !!}: </strong>
    {!! l('Amount must be greater than 0 and not greater than :value', ['value' => $payment->amount]) !!}
</div>

<div class="row" @if( $payment->currency_id == \App\Context::getContext()->currency->id ) style="display: none;" @endif>
<div class="form-group col-lg-4 col-md-4 col-sm-4">
    {{ l('Currency') }}
    {!! Form::text('currency[name]', null, array('class' => 'form-control', 'onfocus' => 'this.blur()')) !!}
</div>

<!-- div class="form-group col-lg-4 col-md-4 col-sm-4">
    {{ l('Conversion Rate') }}
    {!! Form::text('currency_conversion_rate', null, array('class' => 'form-control', 'id' => 'currency_conversion_rate')) !!}
</div -->
</div>

<div class="row">

 <div class="form-group col-lg-12 col-md-12 col-sm-12 {{{ $errors->has('notes') ? 'has-error' : '' }}}">
    {{ l('Notes', [], 'layouts') }}
    {!! Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '2')) !!}
    {{ $errors->first('notes', '<span class="help-block">:message</span>') }}
 </div>

</div>


        <?php if (!isset($back_route)) $back_route = ''; ?>
        <input type="hidden" name="back_route" value="{{$back_route}}"/>

@if($payment->status == 'paid')
<a href="#" class="btn btn-danger btn-sm">{{ l('This Voucher is paid and cannot be modified') }}</a>
@else
{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
@endif

{{-- !! link_to_route('customervouchers.index', l('Cancel', [], 'layouts'), null, array('class' => 'btn btn-warning')) !! --}}
{!! link_to( ($back_route != '' ? $back_route : 'customervouchers'), l('Cancel', [], 'layouts'), array('class' => 'btn btn-warning')) !!}
