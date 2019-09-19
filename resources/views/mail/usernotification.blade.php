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

			{{$feed->senderUser->name}} sent you {{$feed->transaction->amount}} momo(s)
			<br>
			{{$feed->title}}
			{{$feed->description}}

		</p>
		Regards,
		{{env('MAIL_FROM_NAME')}}
	</div>
	
</body>
</html>