<?php namespace App;

class Calculator {

    // PHP Margin Calculator
    public static function margin( $icst, $iprc ) 
    {
      if ( Configuration::get('MARGIN_METHOD') == 'CST' )  
      {  // margin sobre el precio de coste

         if ($icst==0) return NULL;

         $margin = ($iprc-$icst)/$icst;

      } else {
         // Default (or PRC): sobre el precio de venta

         if ($iprc==0) return NULL;

         $margin = ($iprc-$icst)/$iprc;

      }
      return 100.0*$margin;
    }

    // PHP Price Calculator
    public static function price( $icst, $im ) 
    {
      if ( Configuration::get('MARGIN_METHOD') == 'CST' )  
      {

         $price = $icst*(1.0+$im/100.0);

      } else {
         
         if ((1.0-$im/100.0)==0) return NULL;

         $price = $icst/(1.0-$im/100.0);

      }
      return $price;
    }

    // JavaScript Margin Calculator
    public static function marginJSCode( $withTags = NULL)
    {
        $jscode = '';

        if ( Configuration::get('MARGIN_METHOD') == 'CST' ) {   // {* Margen sobre el precio de coste *}
           $jscode .= "
               function margincalc(icst, iprc)
               {
                  var margin = 0;

                  if (icst==0) return '-';

                  margin = (iprc-icst)/icst;

                  return margin*100.0;
               }
               function pricecalc(icst, imc)
               {
                  var price = 0;

                  imc = imc/100.0;

                  price = icst*(1+imc);

                  return price;
               }";
        } else {                                                // {* Default: sobre el precio de venta *}
           $jscode .= "
               function margincalc(icst, iprc)
               {
                  var margin = 0;

                  if (iprc==0) return '-';

                  margin = (iprc-icst)/iprc;

                  return margin*100.0;
               }
               function pricecalc(icst, ims)
               {
                  var price = 0;

                  ims = ims/100.0;

                  if ((1-ims)==0) return '-';

                  price = icst/(1.0-ims);

                  return price;
               }";
        }
        if ($withTags) $jscode = '<script type="text/javascript">'."\n" . $jscode . "\n".'</script>';

        return $jscode;
    }

    // PHP Discount Calculator
    public static function discount( $pmax, $pmin ) 
    {
      $discount = 100.0*(1.0-$pmin/$pmax);

      return $discount;
    }
	
}