
      <div class="row">
         <div class="col-lg-6 col-md-6 col-sm-6">
            <!-- button class="btn btn-sm btn-warning" type="button" onclick="window.location.href='{$fsc->url()}';">
               <span class="glyphicon glyphicon-refresh"></span>
               &nbsp; Reset
            </button -->
            <button type="button" class="btn btn-sm btn-primary xdisabled" data-toggle="tooltip" data-placement="top" title="" data-original-title=" Desactivado hasta que se guarde el documento " xonclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-print"></span>
               &nbsp; {{l('Print', [], 'layouts')}}
            </button>
            <button class="btn btn-sm btn-primary disabled" type="button" onclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-send"></span>
               &nbsp; {{l('Send', [], 'layouts')}}
            </button>
         </div>
         <div class="col-lg-6 col-md-6 col-sm-6 text-right">
            <!-- button class="btn btn-sm btn-info" type="button" onclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-file"></span>
               &nbsp; Guardar Borrador
            </button -->
            <input type="hidden" id="nextAction" name="nextAction" value="" />
            <button class="btn btn-sm btn-primary" type="button" onclick="this.disabled=true;$('#nextAction').val('completeInvoiceData');this.form.submit();">
               <span class="glyphicon glyphicon-save"></span>
               &nbsp; {{l('Save & Complete', [], 'layouts')}}
            </button>
            <button class="btn btn-sm btn-primary" type="button" onclick="this.disabled=true;this.form.submit();">
               <span class="glyphicon glyphicon-hdd"></span>
               &nbsp; {{l('Save', [], 'layouts')}}
            </button>
         </div>
      </div>
      <div class="row">

         <div class="form-group col-lg-12 col-md-12 col-sm-12 {{{ $errors->has('notes') ? 'has-error' : '' }}}" style="margin-top: 20px;">
            {{ l('Notes', [], 'layouts') }}
            {!! Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'notes', 'rows' => '3')) !!}
            {{ $errors->first('notes', '<span class="help-block">:message</span>') }}
         </div>

      </div>
