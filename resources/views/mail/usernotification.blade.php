<!DOCTYPE html>
<html>
<head>
	<title>Mail</title>
</head>
<body>
	<div style="float: left;margin-left: 30px;">
		Hello {{$feed->receiverUser->name}},
		<br>
		<p>
			{{$feed->senderUser->name}} sent you {{$feed->transaction->amount}} momo(s)
			<br>
			{{$feed->title}}
			{{$feed->description}}
		</p>
		Regards,
		Wasp Team
	</div>
	
</body>
</html>