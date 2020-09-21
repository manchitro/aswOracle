<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	
}
else{
	header("Location: ../../login.php?error=nosession");
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../assets/css/faculty-dashboard.css">
	
	<link rel="icon" href="../../favicon.png">
</head>
<body>
	<?php include '../header.php'?>
	<div class="main">
		<?php include '../navigation.php'?>
		<div class="right-panel">
			<div class="page-title">
				<p>Your Profile</p>
			</div>
			<div class="main-container">
				<div class="profile-form">
					<form action="includes/edit-profile.inc.php" method="post">
						<table class="profile-table">
							<?php 
							require '../../includes/oracleConn.php';
							$sql ="SELECT AcademicId, FirstName, LastName, Email FROM users WHERE Id=:id";
							$stmt = oci_parse($conn, $sql);

							if (!$stmt) {
								echo "Could not retrieve data";
							}
							else{
								oci_bind_by_name($stmt, ':id', $_SESSION['userId']);
								oci_execute($stmt);
								$row = oci_fetch_array($stmt, OCI_ASSOC);
								if($row){
									echo '<tr><td class="key">Academic Id: </td><td class="value"><p>'.$row['ACADEMICID'].'</p></td></tr>';
									echo '<tr><td class="key">First Name: </td><td class="value"><input type="text" name="firstName" value="'.$row['FIRSTNAME'].'"></td></tr>';
									echo '<tr><td class="key">Last Name: </td><td class="value"><input type="text" name="lastName" value="'.$row['LASTNAME'].'"></td></tr>';
									echo '<tr><td class="key">Email: </td><td class="value"><input type="email" name="email" title="Your email has to be 	on aiub.edu domain" pattern="^[a-zA-Z0-9]+@aiub\.edu$" value="'.$row['EMAIL'].'"></td></tr>';
									echo '<tr><td></td><td class="submit-button"><input type="submit" name="submit" value="Save"></td></tr>';
								}
								else{
									echo '<p>User Data not found</p>';
								}
							}
							?>
							
						</table>
					</form>
				</div>
				<div class="pass-change-title">
					<p>Change Password</p>
				</div>
				<div class="pass-change-form">
					<p class="error" style="color: red; margin-bottom: 10px;"><?php if(isset($_GET['error']) && $_GET['error'] == "badpass") echo "You have entered a wrong password"?></p>
					<form action="includes/change-password.inc.php" method="post">
						<table class="pass-table">
							<tr><td class="key">Old Password: </td><td class="value"><input type="password" name="oldPassword" required=""></td></tr>
							<tr><td class="key">New Password: </td><td class="value"><input type="password" name="password" id="password" required="" pattern=".{6,}" autocomplete="new-password" title="Password has to be at least 6 characters long. Mix of uppercase, lowercase, numbers is recommended!"></td></tr>
							<tr><td class="key">Confirm Password: </td><td class="value"><input type="password" name="confirm_password" id="confirm_password" required=""></td></tr>
							<tr><td class="Key"></td><td class="submit-button"><input type="submit" name="submit" value="Change"></td></tr>
						</table>
					</form>
					<script src="../../assets/js/passval.js"></script>
				</div>
			</div>
		</div>
	</body>
	</html>