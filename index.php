<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>VerifiedRandom</title>
	<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%236b66c6'/%3E%3Ctext x='16' y='22' font-family='Georgia' font-size='15' fill='white' text-anchor='middle'%3EVR%3C/text%3E%3C/svg%3E">
	<link rel="stylesheet" type="text/css" href="styles/base.css">
	<link rel="stylesheet" type="text/css" href="styles/front.css">
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
			<a href="verify.php">Verify</a>
		</div>
		<div class="navigation">
			<a href="contact.html">Contact</a>
		</div>
		<div class="navigation">
			<a href="about.html">About</a>
		</div>
	</nav>
	<div class="page" id="homeMain">
		<div id="mainLeft">
			<h3 id="mainTitle">The only FREE verified number generation service on the web.</h3>
			<p><a href="index.php">VerifiedRandom.org</a> is a simple, free service that offers verifiable results and true random number generation for giveaways, contests, lotteries and much more.</p>
			<p><a href="index.php">VerifiedRandom.org</a> provides TRUE random numbers which are verifiable by any user with the unique verification code at: <a href="verify.php">VerifiedRandom.org/verify</a>.</p>
			<p>We use a proprietary system which generates random numbers from a physical process rather than a hardware process. Numbers are generated from thermal noise, which is unpredictable and ever changing. Other services utilize hardware algorithms to generate their numbers, which can cause predictable results, which are not able to be verified by users without paying subscription fees.</p>
			<p><a href="index.php">VerifiedRandom.org</a> has always been and will forever be a free service for those who wish to host giveaways, lotteries, contests, etc. And give their users a way to verify the results against our database to ensure that the drawing was -in fact- random.</p>
			<p>No two verification codes are alike, and results are stored for a minimum of six months from the date of draw. Each drawing requires the input of a name, minimum, and maximum value. You may generate as many numbers as you like in one session; every number is recorded with its own UTC timestamp. When you press Save, a unique verification code is issued for all of them. If the results displayed on our /verify page match the timestamp shown when the number was drawn, you can rest assured that the drawing was -in fact- truly random.</p>
		</div>
		<div id="mainRight">
			<div id="content">
				<div id="top">
					<h3>Verified Random Number Generator</h3>
				</div>
				<form id="middle" action="" method="post" onsubmit="return false;">
					<div class="form">
						<div class="lbl">Name: </div>
						<input class="txt" id="name" type="text" maxlength="100" placeholder="Optional" autocomplete="off">
					</div>
					<div class="form">
						<div class="lbl">Min:</div>
						<input class="txt" id="minTxt" type="number" value="1">
					</div>
					<div class="form">
						<div class="lbl">Max:</div>
						<input class="txt" id="maxTxt" type="number" value="100">
					</div>
					<div class="form btnRow">
						<div id="generateBtn" class="btn" role="button" tabindex="0">Generate</div>
						<div id="saveBtn" class="btn hiddenBtn" role="button" tabindex="0">Save</div>
					</div>
					<span id="info">Press SAVE when completed to generate verification code</span>
					<div id="blue"></div>
				</form>
			</div>
			<div id="bottom" class="resultHidden">
				<h3>Results:</h3>
				<div id="drawList"></div>
				<div id="codeWrapper" class="hidden">
					<div id="codeTitle">Verification Code:</div>
					<a id="code" href="#"></a>
				</div>
			</div>
		</div>
	</div>
	<footer>
		<a href="index.php">Home</a>
		<a href="verify.php">Verify</a>
		<a href="contact.html">Contact</a>
		<a href="about.html">About</a>
		<a href="terms.html" id="tos">Terms of Service</a>
		<a href="index.php" id="logo">VerifiedRandom.org</a>
	</footer>
	<script>
		window.VR_CONFIG = { verifyUrl: 'verify.php' };
	</script>
	<script src="js/api-client.js?v=3"></script>
	<script src="js/frontPage.js?v=3"></script>
</body>
</html>
