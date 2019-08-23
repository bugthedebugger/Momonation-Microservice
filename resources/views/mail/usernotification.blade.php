<!DOCTYPE html>
<html>
<head>
	<title>Mail</title>
</head>
<body>
	<div style="float: left;margin-left: 30px;">
		<br>
		Hello {{$feed->receiverUser->name}},
		<br><br>
		<p>
			You received {{$feed->transaction->amount}} momo(s) from {{$feed->senderUser->name}}.
		</p>
		Regards,
		{{env('MAIL_FROM_NAME')}}
	</div>
	
</body>
</html>