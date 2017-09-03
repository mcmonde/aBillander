@section('modals')

@parent

   <div class="modal" id="mailConfirmModal" style="display:none">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               <h4 class="modal-title">{{l('Send an Email', [], 'layouts')}}</h4>
            </div>

<form name="f_sendEmail" id="f_sendEmail" class="form" role="form">
<input type="hidden" name="feedback_info" value="{system_info()}"/>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="modal-body" id="modal-body-mail">
               <div id="error-mail"></div>

      <div class="row">
         <div class="col-md-6">
               <div class="form-group">
                  <label for="name">{{l('To (name)', [], 'layouts')}}</label>
                  <input class="form-control" id="to_name" name="to_name" value=""/>
               </div>
         </div>
         <div class="col-md-6">
               <div class="form-group">
                  <label for="email">{{l('To (email)', [], 'layouts')}}</label>
                  <input type="email" class="form-control" id="to_email" name="to_email" value=""/>
               </div>
         </div>
      </div>

               <div class="form-group">
                  <label for="subject">{{l('Subject', [], 'layouts')}}</label>
                  <input class="form-control" id="subject" name="subject" value=""/>
               </div>
               <div class="form-group">
                  <label for="message">{{l('Your Message', [], 'layouts')}}</label>
                  <textarea id="message" class="form-control" name="message" rows="6"></textarea>
               </div>

      <div class="row">
         <div class="col-md-6">
               <div class="form-group">
                  <label for="name">{{l('From (name)', [], 'layouts')}}</label>
                  <input class="form-control" id="from_name" name="from_name" value=""/>
               </div>
         </div>
         <div class="col-md-6">
               <div class="form-group">
                  <label for="email">{{l('From (email)', [], 'layouts')}}</label>
                  <input type="email" class="form-control" id="from_email" name="from_email" value=""/>
               </div>
         </div>
      </div>

            </div>
            <div class="modal-footer" id="modal-footer-mail">
               <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{l('Cancel', [], 'layouts')}}</button>
               <button type="submit" class="btn btn-sm btn-primary">
                  <span class="glyphicon glyphicon-send"></span>
                  &nbsp; {{l('Send', [], 'layouts')}}
               </button>
            </div>
</form>
            <div class="modal-body" id="modal-body-mail_success" style="display:none">
                <div class="alert alert-success">{{ l('Your email has been sent!', [], 'layouts') }}</div>
            </div>
            <div class="modal-footer" id="modal-footer-mail_success" style="display:none">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ l('Continue', [], 'layouts') }}</button>
            </div>

         </div>
      </div>
   </div>


@stop
@section('scripts')

@parent

<script type="text/javascript">
    $(document).ready(function () {
        $('.mail-item').click(function (evnt) {
            var href = $(this).attr('href');
            var message = $(this).attr('data-content');
            var to_email = $(this).attr('data-to_email');
            var to_name  = $(this).attr('data-to_name');
            var from_email = $(this).attr('data-from_email');
            var from_name  = $(this).attr('data-from_name');
            
            $('#to_email').val(to_email);
            $('#to_name').val(to_name);
            $('#subject').val('');
            $('#message').val('');
            $('#from_email').val(from_email);
            $('#from_name').val(from_name);
            $('#f_sendEmail').attr('action', href);

            $("#subject").removeClass('loading');
            
            $("#error-mail").removeClass("alert alert-danger");
            $("#error-mail").html('');
            $("#modal-body-mail_success").hide();
            $("#modal-footer-mail_success").hide();
            $("#modal-body-mail").show();
            $("#modal-footer-mail").show();

            $('#mailConfirmModal').modal({show: true});
            document.f_sendEmail.subject.focus();
            return false;
        });
    });


        $(function(){
           $("#f_sendEmail").on('submit', function(e){  // ToDo: check fields before submit
              e.preventDefault();
              $("#subject").addClass('loading');
              $.post("{{ URL::to('mail') }}", $(this).serialize(), function(data){
                 $("#subject").removeClass('loading');
                 if (data == 0) {
                    $("#error-mail").addClass("alert alert-danger");
                    $("#error-mail").html('<a class="close" data-dismiss="alert" href="#">×</a><li class="error">{{ l('There was an error. Your message could not be sent.', [], 'layouts') }}</li>');
                 } else {
                    if (isNaN(data)) {
                       $("#error-mail").addClass("alert alert-danger");
                       $("#error-mail").html('<a class="close" data-dismiss="alert" href="#">×</a>' + data + '');
                    } else {
                       $("#modal-body-mail").hide();
                       $("#modal-footer-mail").hide();
                       $("#modal-body-mail_success").show();
                       $("#modal-footer-mail_success").show();
                    }
                 }
              });
           });
        });
</script>

@stop

@section('styles')
@parent

<style>
  .loading{
    background: white url("img/ui-anim_basic_16x16.gif") left center no-repeat;
  }
</style>

@stop