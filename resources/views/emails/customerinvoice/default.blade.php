<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Su Factura núm.: {{ $invoice_date }}) Total: {{ $invoice_total }}</h2>

		<div>
			Adjunto les enviamos la factura de referencia.<br /><br />
			{{{ $custom_body }}}<br /><br />
			Sin otro particular, reciban un cordial saludo.
		</div>

		<!-- div>
			To reset your password, complete this form: { { URL::to('password/reset', array($token)) }}.<br/>
			This link will expire in { { Config::get('auth.reminder.expire', 60) }} minutes.
		</div -->
	</body>
</html>
