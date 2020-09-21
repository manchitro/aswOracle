<!DOCTYPE HTML>
<html>
<head>
	<title>ASW - Signup</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<link rel="icon" href="favicon.png">
</head>
<body>
	<!-- Header -->
	<?php include 'header.php'; ?>

	<!-- Banner -->
	<section id="banner" class="banner-signup">
		<div class="inner">
			<header>
				<h1 id="page-title">Signup</h1>
				<p>Faculties only</p>
			</header>
			<div class="divider-banner"></div>
			<div class="flex ">

				<form action="includes/signup.inc.php" method="post">
					<?php
					if ($_GET) {
						if ($_GET['error'] == 'academicidexists') {
							echo '<p style = "color: tomato;">Could not signup. Academic ID already belongs to an account</p>';
						}
						if ($_GET['error'] == 'emailexists') {
							echo '<p style = "color: tomato;">Could not signup. Email already belongs to an account</p>';
						}
						if ($_GET['error'] == 'sqlerrorinsert') {
							echo '<p style = "color: tomato;">Could not process your request. Please try again!</p>';
						}
					}
					?>
					<input class="text" type="text" name="academicid" id="academicid" placeholder="Academic ID (XXXX-XXXX-X) (Warning: Cannnot be changed in the future)" required title="XXXX-XXXX-X format required" pattern="^\d{4}-\d{4}-[1-3]$" value="<?php echo(isset($_GET['academicid']))?$_GET['academicid']:''?>" autocomplete="off">
					<input class="text" type="text" name="firstname" id="firstname" placeholder="First Name" required="" value="<?php echo(isset($_GET['firstname']))?$_GET['firstname']:''?>">
					<input class="text" type="text" name="lastname" id="lastname" placeholder="Last Name" required="" value="<?php echo(isset($_GET['lastname']))?$_GET['lastname']:''?>">

					<input class="text-email" type="email" name="email" id="email" placeholder="Academic Email (@aiub.edu)" required title="You need an aiub.edu email to sign up" pattern="^[a-zA-Z0-9]+@aiub\.edu$" value="<?php echo(isset($_GET['email']))?$_GET['email']:''?>">
					
					<input class="text" type="password" name="password" id="password" placeholder="Password" required title="Password has to be at least 6 characters long. Mix of uppercase, lowercase, numbers is recommended!" pattern=".{6,}" autocomplete="new-password">
					<input class="text-w3lpass" type="password" name="cpassword" id="confirm_password" placeholder="Confirm Password" required="">

					<button type="submit" name="signup-submit">SIGNUP</button>

					<p id="already">Already have an Account? <a href="login.php"> Login Now!</a></p>
				</form>
				<script src="assets/js/passval.js"></script>



			</div>
		</div>
	</section>

	<?php include 'footer.php'; ?>

	<!-- Scripts -->
	<?php include 'scripts.php'; ?>

</body>
</html>
