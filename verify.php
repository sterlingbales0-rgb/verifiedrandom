<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>VerifiedRandom - Verification</title>
	<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%236b66c6'/%3E%3Ctext x='16' y='22' font-family='Georgia' font-size='15' fill='white' text-anchor='middle'%3EVR%3C/text%3E%3C/svg%3E">
	<link rel="stylesheet" type="text/css" href="styles/base.css">
	<link rel="stylesheet" type="text/css" href="styles/verify.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
	<link href="https://fonts.googleapis.com/css2?family=Nixie+One&display=swap" rel="stylesheet">
</head>
<body>
	<header>
		<a href="index.php"><h1>VerifiedRandom.org</h1></a>
	</header>
	<nav>
		<div class="navigation">
			<a href="index.php">Home</a>
		</div>
		<div class="navigation">
			<a href="#">Verify</a>
		</div>
		<div class="navigation">
			<a href="contact.html">Contact</a>
		</div>
		<div class="navigation">
			<a href="about.html">About</a>
		</div>
	</nav>
	<div class="page" id="wrapper">
		<p id="verifyIntro">Enter a verification code to view the drawing results recorded on our servers. All dates and times are shown in UTC.</p>
		<form id="form" action="" method="post" onsubmit="return false;">
			<label for="codeInput">Verification Code</label>
			<input type="text" name="code" id="codeInput" maxlength="12" autocomplete="off">
			<div id="btn" role="button" tabindex="0">Verify</div>
		</form>
		<div id="statusLine" class="hidden">
			<div id="displayCode"></div>
			<div id="badge"></div>
		</div>
		<div id="verifyInfo" class="hidden"></div>
		<div id="tableWrap" class="hidden"></div>
	</div>
	<footer>
		<a href="index.php">Home</a>
		<a href="#">Verify</a>
		<a href="contact.html">Contact</a>
		<a href="about.html">About</a>
		<a href="terms.html" id="tos">Terms of Service</a>
		<a href="index.php" id="logo">VerifiedRandom.org</a>
	</footer>
	<script src="js/api-client.js?v=3"></script>
	<script src="js/verifyPage.js?v=3"></script>
</body>
</html>
