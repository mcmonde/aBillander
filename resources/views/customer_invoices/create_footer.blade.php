
      <div class="row">
         <div class="col-lg-6 col-md-6 col-sm-6">
            <button class="btn btn-sm btn-warning" type="button" onclick="window.location.href='{$fsc->url()}';">
               <span class="glyphicon glyphicon-refresh"></span>
               &nbsp; Reiniciar
            </button>
            <button type="button" class="btn btn-sm btn-primary xdisabled" data-toggle="tooltip" data-placement="top" title="" data-original-title=" Desactivado hasta que se guarde el documento " xonclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-print"></span>
               &nbsp; Imprimir
            </button>
            <button class="btn btn-sm btn-primary disabled" type="button" onclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-send"></span>
               &nbsp; Enviar
            </button>
         </div>
         <div class="col-lg-6 col-md-6 col-sm-6 text-right">
            <!-- button class="btn btn-sm btn-info" type="button" onclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-file"></span>
               &nbsp; Guardar Borrador
            </button -->
            <button class="btn btn-sm btn-primary" type="button" onclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-save"></span>
               &nbsp; Guardar y Permanecer
            </button>
            <button class="btn btn-sm btn-primary" type="button" onclick="this.disabled=true;this.form.submit();">
               <span class="glyphicon glyphicon-hdd"></span>
               &nbsp; Guardar...
            </button>
         </div>
      </div>
      <div class="row">

         <div class="form-group col-lg-12 col-md-12 col-sm-12 {{{ $errors->has('notes') ? 'has-error' : '' }}}" style="margin-top: 20px;">
            Notas:
            <textarea id="notes" class="form-control" xcols="50" name="notes" rows="3" placeholder="">{{{ Input::old('notes', '') }}}</textarea>
         {{ $errors->first('notes', '<span class="help-block">:message</span>') }}
         </div>

      </div>
