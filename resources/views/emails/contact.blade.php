<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Contact</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="80">From</td>
			<td>: {{ $first_name }} {{ $last_name }} ({{ $email }})</td>
		</tr>
		<tr>
			<td width="80">Phone</td>
			<td>: {{ $phone }} </td>
		</tr>
		<tr>
			<td width="80">Subject</td>
			<td>: {{ $subject }}</td>
		</tr>
		<tr>
			<td width="80">Question</td>
			<td>: {{ $question }}</td>
		</tr>
	</table>
</body>
</html>