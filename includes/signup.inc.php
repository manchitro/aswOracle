<?php
if (isset($_POST['signup-submit'])) 
{
	require 'oracleConn.php';

	$academicid = $_POST['academicid'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$cpassword = $_POST['cpassword'];
	$default_salt = "default_salt";

	$sql = "SELECT academicid FROM users where academicid = :aid";
	$stmt = oci_parse($conn, $sql);

	if (!$stmt) {
		$e = oci_error($conn);
			trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
			header("Location: ../login.php?error=sqlerror");
			exit();
	}
	else{
		oci_bind_by_name($stmt, ':aid', $academicid);
		oci_execute($stmt);
		$row = oci_fetch_array($stmt, OCI_ASSOC);
		if($row){
			header("Location: ../signup.php?error=academicidexists&email=".$email."&firstname=".$firstname."&lastname=".$lastname);
			exit();
		}
		else{
			$sql = "SELECT email FROM users where email = :email";
			$stmt = oci_parse($conn, $sql);

			if (!$stmt) {
				$e = oci_error($conn);
					trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
					header("Location: ../login.php?error=sqlerror");
					exit();
			}
			else{
				oci_bind_by_name($stmt, ':email', $email);
				oci_execute($stmt);
				$row = oci_fetch_array($stmt, OCI_ASSOC);
				if($row){
					header("Location: ../signup.php?error=emailexists&academicid=".$academicid."&firstname=".$firstname."&lastname=".$lastname);
					exit();
				}
				else{
					$sql = "INSERT INTO users (AcademicId, FirstName, LastName, Email, Password, UserType, CreatedAt) VALUES (:academicid, :firstName, :lastName, :email, :password, '0', current_timestamp(2))";
					$stmt = oci_parse($conn, $sql);
					if (!$stmt) {
						$e = oci_error($conn);
						trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
						header("Location: ../login.php?error=sqlerror");
						exit();
					}
					else{
						oci_bind_by_name($stmt, ':academicid', $academicid);
						oci_bind_by_name($stmt, ':firstName', $firstname);
						oci_bind_by_name($stmt, ':lastName', $lastname);
						oci_bind_by_name($stmt, ':email', $email);
						oci_bind_by_name($stmt, ':password', $password);
						oci_execute($stmt);
						header("Location: ../login.php?signup=success");
						exit();	
					}
				}
			}
		}
	}
}
else{
	header("Location: ../signup.php?error=directaccess");
}