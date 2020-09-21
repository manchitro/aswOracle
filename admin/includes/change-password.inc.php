<?php
session_start();
if (isset($_SESSION['userEmail']) && $_SESSION['userEmail'] == "admin") {
	if (isset($_POST['submit'])) {
		$oldPassword = $_POST['oldPassword'];
		$newPassword = $_POST['password'];

		require '../../includes/oracleConn.php';

		$sql ="SELECT * FROM users WHERE Email = :email";
		$stmt = oci_parse($conn, $sql);

		if (!$stmt) {
			$e = oci_error($conn);
				trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
				header("Location: ../profile.php?error=sqlerror");
				exit();
		}
		else{
			oci_bind_by_name($stmt, ':email', $_SESSION['userEmail']);
			oci_execute($stmt);
			$row = oci_fetch_array($stmt, OCI_ASSOC);
			if ($row) {
				if ($oldPassword != $row['PASSWORD']) {
					header("Location: ../profile.php?error=badpass");
					exit();
				}
				elseif ($oldPassword == $row['PASSWORD']) {
					$sql2 ="UPDATE users SET Password = :password where Email = :email";
					$stmt = oci_parse($conn, $sql2);

					if (!$stmt) {
						$e = oci_error($conn);
							trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
							header("Location: ../profile.php?error=sqlerror");
							exit();
					}
					else{
						oci_bind_by_name($stmt, ':password', $newPassword);
						oci_bind_by_name($stmt, ':email', $_SESSION['userEmail']);
						oci_execute($stmt);

						header("Location: ../profile.php?success=passchanged");
						exit();
					}
				}
				else{
					header("Location: ../profile.php?error=badpass");
					exit();
				}
			}
			else{
				header("Location: ../profile.php?error=ukerror");
				exit();
			}
		}
	}
	else{
		header("Location: ../profile.php");
		exit();
	}
}
else{
	header("Location: ../login.php?error=nosession");
	exit();
}
?>