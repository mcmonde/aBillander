
<div class="panel-body">

        <div class="row">
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 {{ $errors->has('name_fiscal') ? 'has-error' : '' }}">
                    {{ l('Fiscal name') }}
                    {!! Form::text('name_fiscal', null, array('class' => 'form-control', 'id' => 'name_fiscal', 'placeholder' => l('Fiscal name'))) !!}
                    {!! $errors->first('name_fiscal', '<span class="help-block">:message</span>') !!}
                  </div>
                  <div class="form-group col-lg-2 col-md-2 col-sm-2 {{ $errors->has('identification') ? 'has-error' : '' }}">
                    {{ l('Fiscal code') }}
                    {!! Form::text('identification', null, array('class' => 'form-control', 'id' => 'identification')) !!}
                    {!! $errors->first('identification', '<span class="help-block">:message</span>') !!}
                  </div>
            <div class="col-md-3">
                  <div class="form-group {{ $errors->has('website') ? 'has-error' : '' }}">
                    {{ l('Web') }}
                    {!! Form::text('website', null, array('class' => 'form-control', 'id' => 'website')) !!}
                    {!! $errors->first('website', '<span class="help-block">:message</span>') !!}
                  </div>
            </div>
        </div>

          @include('addresses._form_fields_model_related')

        <hr>

        <div class="row">

            <div class="col-md-4">
                 <div class="form-group {{ $errors->has('currency_id') ? 'has-error' : '' }}">
                    {{ l('Currency') }} {{ l('(cannot be changed)') }}
                    @if(isset($company))
                      {!! Form::text("currency[name]", null, array('class' => 'form-control', 'onfocus' => 'this.blur()')) !!}
                    @else
                      {!! Form::select('currency_id', array('0' => l('-- Please, select --', [], 'layouts')) + $currencyList, null, array('class' => 'form-control')) !!}
                      {!! $errors->first('currency_id', '<span class="help-block">:message</span>') !!}
                    @endif
                 </div>
            </div>

           <div class="form-group col-lg-4 col-md-4 col-sm-4" id="div-apply_RE">
             {!! Form::label('apply_RE', l('Applies Equalization Tax?'), ['class' => 'control-label']) !!}
             <div>
               <div class="radio-inline">
                 <label>
                   {!! Form::radio('apply_RE', '1', false, ['id' => 'active_on']) !!}
                   {!! l('Yes', [], 'layouts') !!}
                 </label>
               </div>
               <div class="radio-inline">
                 <label>
                   {!! Form::radio('apply_RE', '0', true, ['id' => 'active_off']) !!}
                   {!! l('No', [], 'layouts') !!}
                 </label>
               </div>
             </div>
           </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('company_logo') ? 'has-error' : '' }}">
                    {{ l('Company logo') }}
                    {!! Form::text('company_logo', null, array('class' => 'form-control', 'id' => 'company_logo')) !!}
                    {!! $errors->first('company_logo', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

               </div>
               

               <div class="panel-footer text-right">
                  <!-- a class="btn btn-link" data-dismiss="modal" href="{{ URL::to('companies') }}">Cancelar</a -->
                  <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                     <i class="fa fa-floppy-o"></i>
                     &nbsp; {{ l('Save', [], 'layouts') }}
                  </button>
               </div>