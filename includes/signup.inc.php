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
	$stuser = oci_parse($conn, $sql);

	if (!$stuser) {
		$e = oci_error($conn);
			trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
			header("Location: ../login.php?error=sqlerror");
			exit();
	}
	else{
		oci_bind_by_name($stuser, ':aid', $academicid);
		oci_execute($stuser);
		$row = oci_fetch_array($stuser, OCI_ASSOC);
		if($row){
			header("Location: ../signup.php?error=academicidexists&email=".$email."&firstname=".$firstname."&lastname=".$lastname);
			exit();
		}
		else{
			$sql = "SELECT email FROM users where email = :email";
			$stuser = oci_parse($conn, $sql);

			if (!$stuser) {
				$e = oci_error($conn);
					trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
					header("Location: ../login.php?error=sqlerror");
					exit();
			}
			else{
				oci_bind_by_name($stuser, ':email', $email);
				oci_execute($stuser);
				$row = oci_fetch_array($stuser, OCI_ASSOC);
				if($row){
					header("Location: ../signup.php?error=emailexists&academicid=".$academicid."&firstname=".$firstname."&lastname=".$lastname);
					exit();
				}
				else{
					$sql = "INSERT INTO users (AcademicId, FirstName, LastName, Email, Password, UserType, CreatedAt) VALUES (:academicid, :firstName, :lastName, :email, :password, '0', current_timestamp(2))";
					$stuser = oci_parse($conn, $sql);
					if (!$stuser) {
						$e = oci_error($conn);
						trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
						header("Location: ../login.php?error=sqlerror");
						exit();
					}
					else{
						oci_bind_by_name($stuser, ':academicid', $academicid);
						oci_bind_by_name($stuser, ':firstName', $firstname);
						oci_bind_by_name($stuser, ':lastName', $lastname);
						oci_bind_by_name($stuser, ':email', $email);
						oci_bind_by_name($stuser, ':password', $password);
						oci_execute($stuser);
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