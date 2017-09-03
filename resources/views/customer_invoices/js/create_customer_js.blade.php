

{!! HTML::script('../../aBillander5/public/assets/lib/autocomplete/dist/jquery.autocomplete.min.js') !!}

<script type="text/javascript">

    $(window).load(function(){
        $('#modal_customer_search').modal('show');
    });


    $(function() {

        // $('#create-invoice').modal();

        $('#modal_customer_search').on('shown.bs.modal', function() {
            $("#customer_name").focus();
        });

        // https://github.com/devbridge/jQuery-Autocomplete
        $('#customer_name').autocomplete({
            lookupLimit: 20,
            minChars: 1,
            maxHeight: 300,
            serviceUrl: '{{ route('customers.ajax.nameLookup') }}',
            params: {name_commercial: function () { return $("input[name=name_commercial]:checked").val(); } },
            xonSearchStart: function (query) { 
     //       params: {span: function () { return $("input[name='recurring']").val(); } },
     //       params: {span: 69},  <- Si activo esto, setOptions({params: { q: qw } }) no asigna el parametro!!!
                var qwariane;
                // return query + '&zx=' + $("input[name='recurring']").val()
                // return $(this).autocomplete('setOptions', {params: {q: 'All'} });
                // alert($("input[name='recurring1']").val()+' culo');
                qwariane = $("#recurring1").val()+'-culo'; 
                // alert(qwariane);  
                // http://localhost/aBillander/public/customers/ajax/name_lookup?q=2etrhje+culo&query=c
                // Siguiente línea no va... ;-(   ???????????????  -> NO VA LA PRIMERA VEZ, PORQUE AUN EL OBJETO NO SE HA CREADO
                return $(this).autocomplete().setOptions({params: { q: qwariane, span: 69 } });
            },
            onSelect: function (suggestion) {
                // alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
                if(suggestion)
                {
                    document.create_customer_invoice.customer_id.value = suggestion.data;
                }
            }
        });
    });

</script>
