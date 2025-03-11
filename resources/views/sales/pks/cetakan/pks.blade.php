<!DOCTYPE html>
<html lang="en-US">
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8" />
		<title>PERJANJIAN KERJASAMA</title>
	</head>
	<body style="background:#ffffff">
		@foreach($data as $d)
		{!!$d->raw_text!!}
		@endforeach
	</body>
<script>
    window.print();
</script>
</html>