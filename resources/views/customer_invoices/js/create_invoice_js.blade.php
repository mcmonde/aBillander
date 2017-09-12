
{!! \App\Calculator::marginJSCode( true ) !!}

<script type="text/javascript">

   function tab_hide_all() {
         $("#tab_header").removeClass('active');
         $("#tab_lines").removeClass('active');
         $("#tab_profit").removeClass('active');
         $("#tab_payments").removeClass('active');

         $("#div_header").hide();
         $("#div_lines").hide();
         $("#div_footer").hide();
         $("#div_profit").hide();
         $("#div_payments").hide();
   }

   function formatForDB( dateString ) {
         // dString format is defined within datepicker configuration
         return dateString.replace( /(\d{2})[-/](\d{2})[-/](\d+)/, "$3-$2-$1");;
   }

   $(document).ready(function() {
      
     tab_hide_all();
     $("#tab_header").addClass('active');
     $("#div_header").show();
     $("#div_footer").show();


      $("#b_lines").click(function(event) {
         event.preventDefault();
         tab_hide_all();
         $("#tab_lines").addClass('active');
         $("#div_lines").show();
         $("#div_footer").show();
      });
      
      $("#b_header").click(function(event) {
         event.preventDefault();
         tab_hide_all();
         $("#tab_header").addClass('active');
         $("#div_header").show();
         $("#div_footer").show();
      });
      
      $("#b_profit").click(function(event) {
         event.preventDefault();
         tab_hide_all();
         $("#tab_profit").addClass('active');
            calculate_profit();
         $("#div_profit").show();
      });
      
      $("#b_payments").click(function(event) {
         event.preventDefault();
         tab_hide_all();
         $("#tab_payments").addClass('active');
         $("#div_payments").show();
      });

      // Calculate Document
      initialize_order();

   });


/* ************************************************************************************* */


var nbrlines = {{ count($invoice->customerInvoiceLines) }};         // Number of Invoice Lines

var fin_busqueda1 = true;
var fin_busqueda2 = true;

   function modal_search_tab_hide_all() {
    //     $("#li_product_search").removeClass('active');
    //     $("#li_new_service").removeClass('active');
    //     $("#li_new_text_line").removeClass('active');

      $("#nav_product_search li").each(function() {
         $(this).removeClass("active");
      });

         $("#product_search").hide();
         $("#new_service").hide();
         $("#new_discount").hide();
         $("#new_text_line").hide();
   }

$(document).ready(function() {
   $("#i_new_line").click(function() {
      $("#i_new_line").val("");
      document.f_product_search.query.value = "";
      document.f_product_search.onhand_only.checked = false;
      document.f_new_service.name.value = "";
      document.f_new_service.cost_price.value = "";
      document.f_new_service.price.value = "";
      document.f_new_service.tax_id.value = "0";
      $("#nav_product_search li").each(function() {
         $(this).removeClass("active");
      });
      $("#li_product_search").addClass('active');
      $("#product_search").show();
      $("#search_results").html('');
      $("#search_results").show('');
      $("#new_service").hide();
      $("#modal_product_search").modal('show');
      document.f_product_search.query.focus();
   });
   
//      document.f_product_search.query.value = $("#i_new_line").val();
//      product_search();
//   });
   
   $("#f_product_search").keyup(function() {
      product_search();
   });
   
   $("#f_product_search").submit(function(event) {
      event.preventDefault();
      product_search();
   });
   
   $("#b_product_search").click(function(event) {
      event.preventDefault();
      modal_search_tab_hide_all()
      $("#li_product_search").addClass('active');
      $("#product_search").show();
      document.f_product_search.query.focus();
   });
   
   $("#b_new_service").click(function(event) {
      event.preventDefault();
      modal_search_tab_hide_all()
      $("#li_new_service").addClass('active');
      $("#new_service").show();
      document.f_new_service.name.select();
   });
   
   $("#b_new_discount").click(function(event) {
      event.preventDefault();
      modal_search_tab_hide_all()
      $("#li_new_discount").addClass('active');
      $("#new_discount").show();
      document.f_new_discount.discount_name.select();
   });
   
   $("#b_new_text_line").click(function(event) {
      event.preventDefault();
      modal_search_tab_hide_all()
      $("#li_new_text_line").addClass('active');
      $("#new_text_line").show();
 //     document.f_new_text_line.referencia.select();
   });
});

function product_search()
{
   if(document.f_product_search.query.value === '')
   {
//      $("#nav_product_search").hide();
      $("#search_results").html('');
      $("#new_service").hide();
      
      fin_busqueda1 = true;
      fin_busqueda2 = true;
   }
   else
   {
//      $("#nav_product_search").show();
      
      fin_busqueda1 = false;
      $.getJSON( '{{ route('products.ajax.nameLookup') }}', $("form[name=f_product_search]").serialize(), function(json) {
         var items = [];
         var insertar = false;
         // console.log(json.suggestions);
         $.each(json.suggestions, function(key, val) {
            // console.log(key, val);
            // console.log('onclick="add_product_to_order(\''+JSON.stringify(val)+'\')">');

            // http://stackoverflow.com/questions/18749591/encode-html-entities-in-javascript
            //var val_str = JSON.stringify(val).replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
            //      return '&#'+i.charCodeAt(0)+';';
            //    });

            var val_str = JSON.stringify(val).replace(new RegExp('"', 'g'), '&quot;');
            // console.log(val_str);

            var tr_aux = '<tr>';
            if(val.blocked || !val.active)
            {
               tr_aux = "<tr class=\"danger\">";
            }
            else if(val.quantity_onhand < val.reorder_point)
            {
               tr_aux = "<tr class=\"warning\">";
            }
            else if((val.quantity_onhand+val.quantity_onorder-val.quantity_allocated)<0)
            {
               tr_aux = "<tr class=\"info\">";
            }

            if(val.has_combinations) {
               tr_aux += '  <td>  \
                                    <span class="label label-info">Combinaciones</span>  ';
            }
            else {
               tr_aux += '  <td>  \
                                    <a href="javascript:void(0);" onclick="xadd_product_to_order('+val_str+', {})">'+val.reference+'</a></td>  ';
            }

            items.push(tr_aux+'  <td class="text-left" >'+val.product_name+'</td>  \
                                 <td class="text-right">'+jsp_money(val.price)+'</td>  \
                                 <td class="text-right">'+jsp_money(val.price*(1+val.percent)/100)+'</td>  \
                                 <td class="text-right">'+val.quantity_onhand+'</td>  \
                                 <td class="text-right">'+val.quantity_onorder+'</td>  \
                                 <td class="text-right">'+val.quantity_allocated+'</td>  \
                                 <td class="text-right">'+(val.quantity_onhand+val.quantity_onorder-val.quantity_allocated)+'</td>  \
                                 <td>  \
                                    <a href="javascript:void(0);" onclick="get_customer_price(\''+val.product_id+'\', \''+{{$customer->id}}+'\', \''+val_str+'\')" title=" Más detalles ">  \
                                       <button class="btn btn-xs btn-success" type="button"><i class="fa fa-search-plus"></i></button></a></td>  \
                               </tr>  \
                        ');
            
            if(json.query == document.f_product_search.query.value)
            {
               insertar = true;
               fin_busqueda1 = true;
            }
         });
         
         if(items && items.length == 0 && !fin_busqueda1)
         {
            items.push('<tr><td colspan="5" class="alert alert-warning alert-block"><i class="fa fa-warning"></i> No se han encontrado registros.</td></tr>');
            // document.f_new_service.referencia.value = document.f_product_search.query.value;
            insertar = true;
         }
         
         if(insertar)
         {
            $("#search_results").html("<div class='table-responsive'><table class='table table-hover'><thead><tr> \
               <th class='text-left'>Referencia</th><th class='text-left'>Nombre</th><th class='text-right'>PVP</th><th class='text-right'>PVP+IVA</th>   \
               <th class='text-right'>Stock</th><th class='text-right'>Pendiente</th><th class='text-right'>Reservado</th><th class='text-right'>Disponible</th><th></th></tr></thead>"+items.join('')+'</table></div>');
         }
      });

   }
}

function get_customer_price(p_id, c_id, j_str)
{
   $.ajax({
      type: 'POST',
      url: '{{ route('products.ajax.priceLookup') }}',
      dataType: 'html',
      data: "product_id="+p_id+"&customer_id="+c_id+"&product_string="+j_str+"&_token="+$('[name="_token"]').val(),
      success: function(data) {
    //     $("#nav_articulos").hide();
         $("#search_results").html(data);
      }
   });
}

function add_service_to_order()
{
    var text;
    var type = 'service';

    if ($("#name").val() == '')
    {
        $("#name").parent().addClass('has-error');
        return ;
    }

    if ( isNaN( $("#cost_price").val() ) || !( $("#cost_price").val() > 0 ) ) 
    {
        $("#cost_price").val( $("#price").val() );
    }

    if ($("#tax_id").val() == '0')
    {
        $("#tax_id").parent().addClass('has-error');
        return ;
    }
    // ToDo: more error checking...

    if ($("input:radio[name='is_shipping']:checked").val() == '1')
    {
        type = 'shipping';
        // Reset selector
        $("input[name='is_shipping'][value=0]").prop('checked', true);
    }

    text = '{ ' +
      ' "id":"'             + ''                     + '" , ' +
      ' "reference":"'      + ''                     + '" , ' +
      ' "product_name":"'   + $("#name").val()       + '" , ' +
      ' "cost_price":"'     + $("#cost_price").val() + '" , ' +
      ' "price":"'          + $("#price").val()      + '" , ' +
      ' "price_customer":"' + $("#price").val()      + '" , ' +
      ' "tax_id":"'         + $("#tax_id").val()     + '" , ' +
      ' "line_type":"'      + type                   + '"   ' + '}';

    add_product_to_order( JSON.parse(text), {} );
}

function add_discount_to_order()
{
    var text;
    var type = 'discount';

    if ($("#discount_name").val() == '')
    {
        $("#discount_name").parent().addClass('has-error');
        return ;
    }

    if ($("#discount_tax_id").val() == '0')
    {
        $("#discount_tax_id").parent().addClass('has-error');
        return ;
    }
    // ToDo: more error checking...

    text = '{ ' +
      ' "id":"'             + ''                     + '" , ' +
      ' "reference":"'      + ''                     + '" , ' +
      ' "product_name":"'   + $("#discount_name").val()        + '" , ' +
      ' "cost_price":"'     + 0                                + '" , ' +
      ' "price":"'          + -$("#discount_price").val()      + '" , ' +
      ' "price_customer":"' + -$("#discount_price").val()      + '" , ' +
      ' "tax_id":"'         + $("#discount_tax_id").val()      + '" , ' +
      ' "line_type":"'      + type                   + '"   ' + '}';

    add_product_to_order( JSON.parse(text), {} );
}

function add_product_to_order(p_string, pc_string)
{
   // alert(JSON.stringify(p_string));
   // var p = JSON.parse(p_string);     // $.parseJSON(p_string);
   var p  = p_string;
   var pc = pc_string; // alert(pc.combination_name);
   var p_name;
   var p_reference;
   var pc_id;

   if ( pc.id != 'undefined' && pc.id ) {
      p_name      = p.product_name+' | '+pc.combination_name;
      p_reference = pc.reference;
      pc_id = pc.id;
   } else {
      p_name      = p.product_name;
      p_reference = p.reference;
      pc_id = 0;
   }

   // ToDo: isNaN( p.price_customer )  ->  manage to get customer price

   $("#order_lines").append('<tr id="line_'+nbrlines+'">\n\
      <td><input type="text" id="line_sort_order_'+nbrlines+'" name="line_sort_order_'+nbrlines+'" value="'+((nbrlines+1)*10)+'" class="form-control" /></td>\n\
 \n\
      <td><input type="hidden" id="lineid_'+nbrlines+'"          name="lineid_'+nbrlines+'"          value="'+nbrlines+'"/>\n\
          <!-- input type="hidden" id="line_sort_order_'+nbrlines+'" name="line_sort_order_'+nbrlines+'" value="'+nbrlines+'"/ -->\n\
          <input type="hidden" id="line_type_'+nbrlines+'"       name="line_type_'+nbrlines+'"       value="'+( p.product_id > 0 ? 'product' : p.line_type )+'"/>\n\
          <input type="hidden" id="product_id_'+nbrlines+'"      name="product_id_'+nbrlines+'"      value="'+p.product_id+'"/>\n\
          <input type="hidden" id="combination_id_'+nbrlines+'"  name="combination_id_'+nbrlines+'"  value="'+pc_id+'"/>\n\
          <input type="hidden" id="reference_'+nbrlines+'"       name="reference_'+nbrlines+'"       value="'+p_reference+'"/>\n\
 \n\
          <input type="hidden" id="cost_price_'+nbrlines+'"          name="cost_price_'+nbrlines+'"          value="'+p.cost_price+'"/>\n\
          <input type="hidden" id="unit_price_'+nbrlines+'"          name="unit_price_'+nbrlines+'"          value="'+p.price+'"/>\n\
          <input type="hidden" id="unit_customer_price_'+nbrlines+'" name="unit_customer_price_'+nbrlines+'" value="'+p.price_customer+'"/>\n\
          <!-- input type="hidden" id="unit_final_price_'+nbrlines+'"    name="unit_final_price_'+nbrlines+'"    value="'+p.price_customer+'"/ -->\n\
          <input type="hidden" id="tax_percent_'+nbrlines+'"         name="tax_percent_'+nbrlines+'"         value=""/>\n\
          <input type="hidden" id="total_tax_'+nbrlines+'"           name="total_tax_'+nbrlines+'"           value=""/>\n\
 \n\
          <input type="hidden" id="discount_amount_tax_incl_'+nbrlines+'" name="discount_amount_tax_incl_'+nbrlines+'" value=""/>\n\
          <input type="hidden" id="discount_amount_tax_excl_'+nbrlines+'" name="discount_amount_tax_excl_'+nbrlines+'" value=""/>\n\
 \n\
         <div class="form-control"><a target="_blank" href="{{ URL::to('products') }}'+'/'+p.product_id+'/edit">'+p_reference+'</a></div></td>\n\
 \n\
      <td><input type="text" class="form-control" name="name_'+nbrlines+'" value="'+p_name+'" onclick="this.select()"/></td>\n\
 \n\
      <td><input type="number" step="any" id="quantity_'+nbrlines+'" class="form-control text-right" name="quantity_'+nbrlines+
         '" onkeyup="calculate_line('+nbrlines+')" onchange="calculate_line('+nbrlines+')" autocomplete="off" value="1"/></td>\n\
 \n\
      <td><button class="btn btn-md btn-danger" type="button" onclick="$(\'#line_'+nbrlines+'\').remove();calculate_order();">\n\
         <i class="fa fa-trash"></i></button><!-- div class="form-control">'+nbrlines+'</div --></td>\n\
 \n\
      <!-- td><div class="form-control">'+jsp_money(p.price_customer)+'</div></td -->\n\
 \n\
      <td><input type="text" id="unit_final_price_'+nbrlines+'" name="unit_final_price_'+nbrlines+'" value="'+p.price_customer+
         '" class="form-control text-right" onkeyup="calculate_line('+nbrlines+')" onclick="this.select()" autocomplete="off"/></td>\n\
 \n\
      <td><input type="text" id="discount_percent_'+nbrlines+'" name="discount_percent_'+nbrlines+'" value="'+0+
         '" class="form-control text-right" onkeyup="calculate_line('+nbrlines+')" onclick="this.select()" autocomplete="off"/></td>\n\
 \n\
      <td><input type="text" class="form-control text-right" id="total_tax_excl_'+nbrlines+'" name="total_tax_excl_'+nbrlines+
         '" onkeyup="calculate_line('+nbrlines+', \'net\')" onclick="this.select()" autocomplete="off"/></td>\n\
 \n\
      <td>  '+taxes_dropdown( nbrlines )+'  </td>'+'\n\
 \n\
      <td><input type="text" class="form-control text-right" id="total_tax_incl_'+nbrlines+'" name="total_tax_incl_'+nbrlines+
         '" onkeyup="calculate_line('+nbrlines+', \'total\')" onclick="this.select()" autocomplete="off"/></td></tr>');
   
   $("#tax_id_"+nbrlines).val(p.tax_id);
//   $("#tax_id_"+nbrlines).onchange = function() { alert('Hola '+nbrlines); calculate_line( nbrlines ); };    // http://stackoverflow.com/questions/1628826/how-to-add-an-onchange-event-to-a-select-box-via-javascript
//   $("#tax_id_"+nbrlines).on("change", function() { calculate_line( ($(this).attr("id")).replace("tax_id_", "") ); });    // http://stackoverflow.com/questions/12496838/how-to-add-onchange-attribute-for-select-box-from-javascript-jquery
//   $("#tax_percent_"+nbrlines).val( get_tax_percent_by_id( p.tax_id ) );   <--  Not needed any more. See: calculate_line()

   nbrlines += 1;
   $("#nbrlines").val(nbrlines);
   calculate_line(nbrlines-1);
   
//   $("#nav_product_search").hide();
   $("#search_results").html('');
   $("#new_service").hide();
   $("#modal_product_search").modal('hide');
   
   $("#quantity_"+(nbrlines-1)).focus();
}

// function format_price(price, decimal) 
function jsp_money(price, decimal)        // JavaScript presenter
{
  if (typeof decimal === 'undefined')
  {
    // Just remuve right trailing zeros
    // http://stackoverflow.com/questions/3612744/javascript-remove-insignificant-trailing-zeros-from-a-number
   return parseFloat(price).toString();

  } else if (decimal<0) {
    // Use decimal places according to currency (PHP Model)
    // ....
  } else {
    // Use decimal as decimal places
    return parseFloat(price).toFixed(decimal);
  }
}

function jsp_percent(percent, decimal)        // JavaScript presenter
{
  if (typeof decimal === 'undefined')
  {
    // Default
   return parseFloat(percent).toFixed(2);

  } else if (decimal<0) {
    // Use decimal places according to currency (PHP Model)
    // ....
  } else {
    // Use decimal as decimal places
    return parseFloat(percent).toFixed(decimal);
  }
}

function taxes_dropdown(id) 
{
    var html = '<option value="0">{{ l('-- Please, select --', [], 'layouts') }}</option>';

    @foreach ( $taxList as $k => $v)
        html += '<option value="{{$k}}">{{$v}}</option>';
    @endforeach

    html = '<select name="tax_id_'+id+'" class="form-control" id="tax_id_'+id+'" onchange="calculate_line('+id+')">' + html + '</select>';
    return html;
}

function get_tax_percent_by_id(tax_id) 
{
   // http://stackoverflow.com/questions/18910939/how-to-get-json-key-and-value-in-javascript
   // var taxes = $.parseJSON( '{{ json_encode( $taxpercentList ) }}' );
   var taxes = {!! json_encode( $customer->sales_equalization
                                  ? $taxeqpercentList 
                                  : $taxpercentList 
                              ) !!} ;

   if (typeof taxes[tax_id] == "undefined")   // or if (taxes[tax_id] === undefined) {
   {
        // variable is undefined
        alert('Tax code ['+tax_id+'] not found!');
   } else
        return taxes[tax_id];
}

function show_price_iva(pvp,iva) {
   return show_precio(pvp + pvp*iva/100);}


function foo(a, b)
 {
   a = typeof a !== 'undefined' ? a : 42;
   b = typeof b !== 'undefined' ? b : 'default_b';
   // ...
 }

function initialize_order() 
{
   for(var i=0; i<nbrlines; i++)
   {
      if($("#line_"+i).length > 0)
      {         
         calculate_line(i);
      }
   }
}

function calculate_order() 
{
   var gross_net = 0;
   var gross_tax = 0;
   var gross = 0;

   var total_net = 0;
   var total_tax = 0;
   var total = 0;

   var doc_dis = 0;
   
   for(var i=0; i<nbrlines; i++)
   {
      if($("#line_"+i).length > 0)
      {         
         gross_net += parseFloat( $("#total_tax_excl_"+i).val() );
         gross_tax += parseFloat( $("#total_tax_"+i).val() );
         gross     += parseFloat( $("#total_tax_incl_"+i).val() );
      }
   }

//   $("#document_discount").val( parseFloat( $("#document_discount").val() ) );
   doc_dis = parseFloat( $("#document_discount").val() )

   total_net = gross_net*(1.0-doc_dis/100.0);
   total_tax = gross_tax*(1.0-doc_dis/100.0);
   total     = gross*(1.0-doc_dis/100.0);

   $("#order_gross_tax_excl").val( gross_net );
   $("#order_gross_tax_incl").val( jsp_money(gross) );
   $("#order_gross_taxes").val( jsp_money(gross_tax) );

   $("#order_total_tax_excl").val( jsp_money(total_net) );
   $("#order_total_tax_incl").val( jsp_money(total) );
   $("#order_total_taxes").val( jsp_money(total_tax) );
}

function calculate_line(line_id, val) 
{
  i = line_id;
  if( !($("#line_"+i).length > 0) ) return ;

  // Calculate hidden values
  $("#tax_percent_"+i).val( get_tax_percent_by_id( $("#tax_id_"+i).val() ) );

  if (typeof val === 'undefined')
  {
    // 
         l_qty  = parseFloat( $("#quantity_"+i).val() );
         l_pri  = parseFloat( $("#unit_final_price_"+i).val() );
         l_disp = parseFloat( $("#discount_percent_"+i).val() );
         l_tax  = parseFloat( $("#tax_percent_"+i).val() );

         l_dis  = l_qty*l_pri*(l_disp/100.0);
         l_dist = l_dis*(1+l_tax/100.0);

         l_net = l_qty*l_pri*(100.0-l_disp)/100.0;
         l_tot = l_net + l_net*l_tax/100.0;
         
         $("#discount_amount_tax_incl_"+i).val( l_dist );
         $("#discount_amount_tax_excl_"+i).val( l_dis  );

         $("#total_tax_excl_"+i).val( jsp_money(l_net) );
         $("#total_tax_"+i).val( jsp_money(l_net*l_tax/100.0) );
         $("#total_tax_incl_"+i).val( jsp_money(l_tot) );

  } else if (val=='total') {
    // if line total has changed
        l_tot  = $("#total_tax_incl_"+i).val();
        l_qty  = parseFloat( $("#quantity_"+i).val() );
        l_disp = 0.0;
        l_tax  = parseFloat( $("#tax_percent_"+i).val() );

        l_pri  = (l_tot/l_qty)/(1.0+l_tax/100.0);

        $("#unit_final_price_"+i).val( l_pri );
        $("#discount_percent_"+i).val( l_disp );

        calculate_line(i);

  } else if (val=='net')  {
    // if line net has changed
        l_net  = $("#total_tax_excl_"+i).val();
        l_qty  = parseFloat( $("#quantity_"+i).val() );
        l_disp = 0.0;

        l_pri  = (l_net/l_qty);

        $("#unit_final_price_"+i).val( l_pri );
        $("#discount_percent_"+i).val( l_disp );

        calculate_line(i);

  }

  calculate_order();
  
}

function calculate_profit() 
{
  var commission = {{ $customer->salesrep->commission_percent }};
  var line = '', line_t = '';
  var q, p, d, c, n, m1, co, m2;
  var p_t=0, d_t=0, c_t=0, n_t=0, m1_t=0, co_t=0, m2_t=0;

  // ToDo: calculate Sales Rep Comission

  for(var i=0; i<nbrlines; i++)
   {
      if($("#line_"+i).length > 0)
      {
         q = $("#quantity_"+i).val();
         p = $("#unit_final_price_"+i).val();
         d = $("#discount_percent_"+i).val();
         n = p*(1.0-d/100.0);
         c = $("#cost_price_"+i).val();
         m1 = margincalc(c, n);
         co = p*commission/100.0;
         m2 = margincalc(c, n-co);

         line += '<td>'+q+'</td>';
         line += '<td>'+$("#reference_"+i).val()+'</td>';
         line += '<td>'+p+'</td>';
         line += '<td>'+d+'</td>';
         line += '<td>'+n+'</td>';
         line += '<td class="text-right">'+c+'</td>';
         line += '<td class="text-right">'+m1+'</td>';
         line += '<td class="text-right">'+commission+'</td>';
         line += '<td class="text-right">'+m2+'</td>';

         p_t += q*p;
         d_t += q*p*d/100.0;
         c_t += q*c;
         co_t += q*p*commission/100.0;
      }
   }
  n_t = p_t-d_t;
  m1_t = margincalc(c_t, n_t);
  m2_t = margincalc(c_t, n_t-co_t);

   line_t += '<td>'+p_t+'</td>';
   line_t += '<td>'+d_t+'</td>';
   line_t += '<td>'+n_t+'</td>';
   line_t += '<td class="text-right">'+c_t+'</td>';
   line_t += '<td class="text-right">'+m1_t+'</td>';
   line_t += '<td class="text-right">'+co_t+'</td>';
   line_t += '<td class="text-right">'+m2_t+'</td>';



  $("#profit_detail_lines").html('');
  $("#profit_detail_lines").append('<tr>'+line+'</tr>');

  $("#profit_detail").html('');
  $("#profit_detail").append('<tr>'+line_t+'</td></tr>');
}


/* ************************************************************************************* */


// http://www.tutorialrepublic.com/twitter-bootstrap-tutorial/bootstrap-popovers.php
// $('[data-toggle="popover"]').popover({
//   html : true
// });

</script>

{{-- Date Picker --}}

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
{!! HTML::script('assets/jquery-ui/datepicker/datepicker-'.\App\Context::getContext()->language->iso_code.'.js'); !!}

<script>

  $(function() {
    $( "#document_date_form" ).datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
    });
  });

  $(function() {
    $( "#delivery_date_form" ).datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
    });
  });
  
</script>

