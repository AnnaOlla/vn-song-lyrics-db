<!DOCTYPE html>
<html>
	<head>
		<title>Access Denied</title>
	</head>
	<style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		body {
			display: flex;
			align-items: center;
			justify-content: center;
		}
		section {
			border: 1px solid black;
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
		}
		section > * {
			padding: 0px;
			margin: 10px;
		}
	</style>
	<body>
		<section>
			<h1>Access Denied</h1>
			<p>You have been permanently banned for malicious behavior.</p>
			<p>If you see this message in error, contact us at support@vn-song-lyrics-db.ru.</p>
			<p>In this case, include the information below.</p>
			<p>Current time: <?php echo date("Y-m-d H:i:s"), ', UTC+3'; ?>.</p>
			<p>Your IP address: <?php echo $_SERVER['REMOTE_ADDR'] ?? 'unknown'; ?>.</p>
		</section>
	</body>
</html>