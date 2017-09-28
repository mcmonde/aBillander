<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Product Types
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'simple'     => 'Simple', 			// a collection of related products that can be purchased individually and only consist of simple products. Simple products are shipped and have no combitions.
	'virtual'    => 'Servicio', 		// one that doesn’t require shipping or stock management (Services, downloads...)
	'combinable' => 'Combinable', 		// a product with combitions, each of which may have a different SKU, price, stock option, etc.
	'grouped'    => 'Agrupado',			// a collection of related products that can be purchased individually and only consist of simple products. 


	/*
	|--------------------------------------------------------------------------
	| Document Types
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'CustomerInvoice' => 'Factura de Cliente',
	

	/*
	|--------------------------------------------------------------------------
	| Stock Movement Types
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	10 => 'Stock inicial',

	12 => 'Ajuste de stock',

	20 => 'Orden de compra',

	21 => 'Devolución de compras',

	30 => 'Orden de venta',

	31 => 'Devolución de ventas',

	40 => 'Transferencia (Salida)',

	41 => 'Transferencia (Entrada)',

	50 => 'Consumo de fabricación',

	51 => 'Devolución de fabricación',

	55 => 'Producto de fabricación',

	/*
	|--------------------------------------------------------------------------
	| Margin calculation methods
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'CST' => 'Sobre el Precio de Coste',	// Markup Percentage = (Sales Price – Unit Cost)/Unit Cost
	'PRC' => 'Sobre el Precio de Venta',	// Gross Margin Percentage = (Gross Profit/Sales Price) X 100

	/*
	|--------------------------------------------------------------------------
	| Price List CSTPRCTypes
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'Fixed price' => 'Precio fijo',
	'Discount percentage' => 'Porcentaje de descuento',
	'Margin percentage' => 'Porcentaje de margen',

	/*
	|--------------------------------------------------------------------------
	| Customer Invoice Statuses
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'draft' => 'Borrador',
	'pending' => 'Pendiente',
	'halfpaid'    => 'Parcialmente Pagado',
	'paid'    => 'Pagado',

	/*
	|--------------------------------------------------------------------------
	| Payment Statuses
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'pending' => 'Pendiente',
	'bounced' => 'Devuelto',
	'paid'    => 'Pagado',

	/*
	|--------------------------------------------------------------------------
	| Paper Orientation Types
	|--------------------------------------------------------------------------
	|
	| .
	|
	*/

	'Portrait' => 'Vertical',
	'Landscape' => 'Horizontal',
	'portrait' => 'Vertical',
	'landscape' => 'Horizontal',

);
