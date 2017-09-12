<?php

use Illuminate\Database\Seeder;
use App\Tax as Tax;
use App\TaxRule as TaxRule;
  
class TaxesTableSeeder extends Seeder {
  
    public function run() {
        Tax::truncate();
        TaxRule::truncate();
  
        Tax::create( [
            'id'      => '1' ,
            'name'      => 'IVA Normal' ,
            'active'    => '1' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '0' ,

            'name'      => 'IVA Normal (21%)' ,
            'percent' => '21.0' ,

            'position' => '10' ,
            'tax_id'    => '1' ,  // Won't work. Why? -> 'tax_id' not in Model $fillable array
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '1' ,

            'name'      => 'Recargo de Equivalencia (5.2%)' ,
            'percent' => '5.2' ,

            'position' => '20' ,
            'tax_id'    => '1' ,  // Won't work. Why?
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        Tax::create( [
            'id'      => '2' ,
            'name'      => 'IVA Reducido' ,
            'active'    => '1' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '0' ,

            'name'      => 'IVA Reducido (10.0%)' ,
            'percent' => '10.0' ,

            'position' => '10' ,
            'tax_id'    => '2' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '1' ,

            'name'      => 'Recargo de Equivalencia (1.4%)' ,
            'percent' => '1.4' ,

            'position' => '20' ,
            'tax_id'    => '2' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        Tax::create( [
            'id'      => '3' ,
            'name'      => 'IVA Super Reducido' ,
            'active'    => '1' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '0' ,

            'name'      => 'IVA Super Reducido (4%)' ,
            'percent' => '4.0' ,

            'position' => '10' ,
            'tax_id'    => '3' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '1' ,

            'name'      => 'Recargo de Equivalencia (0.5%)' ,
            'percent' => '0.5' ,

            'position' => '20' ,
            'tax_id'    => '3' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        Tax::create( [
            'id'      => '4' ,
            'name'      => 'IVA Exento (0%)' ,
            'active'    => '1' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
  
        TaxRule::create( [
            'country' => 'ES' ,
            'state'   => '' ,
            'sales_equalization' => '0' ,

            'name'      => 'IVA Exento' ,
            'percent' => '0.0' ,

            'position' => '10' ,
            'tax_id'    => '4' ,
                    'created_at'  => \Carbon\Carbon::createFromDate(2015,04,01)->toDateTimeString(),
                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ] );
    }
}
