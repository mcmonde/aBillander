
               <div class="panel-body">
                
        <div class="row">
                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('name') ? 'has-error' : '' }}">
                     {{ l('Customer Group Name') }}
                     {!! Form::text('name', null, array('class' => 'form-control', 'id' => 'name')) !!}
                     {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
                  </div>

                  <div class="form-group col-lg-4 col-md-4 col-sm-4 {{ $errors->has('price_list_id') ? 'has-error' : '' }}">
                     {{ l('Price List') }}
                     {!! Form::select('price_list_id', array('0' => l('-- Please, select --', [], 'layouts')) + $price_listList, Input::old('price_list_id', isset($customer) ? $customer->price_list_id : 0), array('class' => 'form-control')) !!}
                     {!! $errors->first('price_list_id', '<span class="help-block">:message</span>') !!}
                  </div>

                   <div class="form-group col-lg-4 col-md-4 col-sm-4" id="div-active">
                     {!! Form::label('active', l('Active?', [], 'layouts'), ['class' => 'control-label']) !!}
                     <div>
                       <div class="radio-inline">
                         <label>
                           {!! Form::radio('active', '1', true, ['id' => 'active_on']) !!}
                           {!! l('Yes', [], 'layouts') !!}
                         </label>
                       </div>
                       <div class="radio-inline">
                         <label>
                           {!! Form::radio('active', '0', false, ['id' => 'active_off']) !!}
                           {!! l('No', [], 'layouts') !!}
                         </label>
                       </div>
                     </div>
                   </div>
        </div>


               </div><!-- div class="panel-body" -->

               <div class="panel-footer text-right">
                  <a class="btn btn-link" data-dismiss="modal" href="{{{ URL::to('customergroups') }}}">{{l('Cancel', [], 'layouts')}}</a>
                  <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                     <span class="glyphicon glyphicon-floppy-disk"></span>
                     &nbsp; {{l('Save', [], 'layouts')}}
                  </button>
               </div>
