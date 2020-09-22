<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['submit'])){
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$email = $_POST['email'];

		require '../../../includes/oracleConn.php';

		$sql = "UPDATE users SET FirstName = :fname, LastName = :lname, Email = :email WHERE Id = :fid";
		$stmt = oci_parse($conn, $sql);
		if (!$stmt) {
			header("Location: ../profile.php?error=sqlerror");
			exit();
		}
		else{
			$firstName = trim($firstName);
			$lastName = trim($lastName);
			oci_bind_by_name($stmt, ':fname', $firstName);
			oci_bind_by_name($stmt, ':lanme', $lastName);
			oci_bind_by_name($stmt, ':email', $email);
			oci_bind_by_name($stmt, ':fid', $_SESSION['userId']);
			oci_execute($stmt);

			$_SESSION['userFirstName'] = $firstName;
			$_SESSION['userLastName'] = $lastName;
			$_SESSION['userEmail'] = $email;

			header("Location: ../profile.php?success=profupdated".mysqli_error($conn));
			exit();
		}
	}
	else{
		header("Location: ../profile.php");
		exit();
	}
}
else{
	header("Location: ../../login.php?error=nosession");
	exit();
}
?>

