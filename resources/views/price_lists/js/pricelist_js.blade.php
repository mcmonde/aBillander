<script type="text/javascript">

$(function() {

    if ($('#type').val() > 0) {
 //           $('#div-price_is_tax_inc').hide();
 //           $('#div-amount').show();
        }
        else {
            $('#div-amount').hide();
            $('#amount').val( 0.0 );
    };

    $('#type').change(function () {
        if ($(this).val() > 0) {
//            $('#div-price_is_tax_inc').hide();
            $('#div-amount').show();
        }
        else {
            $('#div-amount').hide();
            $('#amount').val( 0.0 );
//            $('#div-price_is_tax_inc').show();
        }
    });

/*
    $('input[name=recurring]:radio').change(function () {
        if ($(this).val() == 1) {
            $('#div-recurring-options').show();
        }
        else {
            $('#div-recurring-options').hide();
        }
    });
*/

});
</script>