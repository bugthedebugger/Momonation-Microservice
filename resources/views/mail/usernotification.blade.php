<!DOCTYPE html>
<html>
<head>
	<title>Mail</title>
</head>
<body>
	<div style="float: left;margin-left: 30px;">
		<br>
		Hello {{$feed->receiverUser->name}},
		<br>
		<p>
			{{$feed->senderUser->name}} send you {{$feed->transaction->amount}} momo(s)
		</p>
		Regards,
		{{env('MAIL_FROM_NAME')}}
	</div>
	
</body>
</html>