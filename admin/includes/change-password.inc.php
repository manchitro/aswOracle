<?php
session_start();
if (isset($_SESSION['userEmail']) && $_SESSION['userEmail'] == "admin") {
	if (isset($_POST['submit'])) {
		$oldPassword = $_POST['oldPassword'];
		$newPassword = $_POST['password'];

		require '../../includes/oracleConn.php';

		$sql ="SELECT * FROM users WHERE Email = :email";
		$stuser = oci_parse($conn, $sql);

		if (!$stuser) {
			$e = oci_error($conn);
				trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
				header("Location: ../profile.php?error=sqlerror");
				exit();
		}
		else{
			oci_bind_by_name($stuser, ':email', $_SESSION['userEmail']);
			oci_execute($stuser);
			$row = oci_fetch_array($stuser, OCI_ASSOC);
			if ($row) {
				if ($oldPassword != $row['PASSWORD']) {
					header("Location: ../profile.php?error=badpass");
					exit();
				}
				elseif ($oldPassword == $row['PASSWORD']) {
					$sql2 ="UPDATE users SET Password = :password where Email = :email";
					$stuser = oci_parse($conn, $sql2);

					if (!$stuser) {
						$e = oci_error($conn);
							trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
							header("Location: ../profile.php?error=sqlerror");
							exit();
					}
					else{
						oci_bind_by_name($stuser, ':password', $newPassword);
						oci_bind_by_name($stuser, ':email', $_SESSION['userEmail']);
						oci_execute($stuser);

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