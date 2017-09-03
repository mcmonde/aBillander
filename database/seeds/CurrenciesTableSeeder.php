<?php

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('configurations')->truncate();
		DB::table('currencies')->delete();
		
		$configurations = array(
			array(	'name'         => 'Euro', 
					'iso_code'     => 'EUR',
					'iso_code_num' => '978',
					'sign'         => '€',

					'signPlacement'      => '1',
					'thousandsSeparator' => '.',
					'decimalSeparator'   => ',',
					'decimalPlaces'      => '2',

					'blank'                    => '0',
					'currency_conversion_rate' => '1.0',
					'active'                   => '1',

					'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),		// date('Y-m-d H:i:s');
					),
			array(	'name'         => 'Dollar', 
					'iso_code'     => 'USD',
					'iso_code_num' => '840',
					'sign'         => '$',

					'signPlacement'      => '0',
					'thousandsSeparator' => ',',
					'decimalSeparator'   => '.',
					'decimalPlaces'      => '2',

					'blank'                    => '0',
					'currency_conversion_rate' => '0.94',
					'active'                   => '1',

					'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),		// date('Y-m-d H:i:s');
					),

		);

		// Uncomment the below to run the seeder
		DB::table('currencies')->insert($configurations);
	}

}
