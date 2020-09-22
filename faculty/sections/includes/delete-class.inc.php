<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['submit']) && isset($_POST['sectionId']) && isset($_POST['classId'])){
		$sectionId = $_POST['sectionId'];
		$sectionName = $_POST['sectionName'];
		$classId = $_POST['classId'];

		require '../../../includes/oracleConn.php';

		$sql = "DELETE FROM attendances WHERE ClassId = :cid";
		$stmt = oci_parse($conn, $sql);
		if (!$stmt) {
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			$_SESSION['classId'] = $classId;
			header("Location: ../editclass.php?error=sqlerror");
		}
		else{
			oci_bind_by_name($stmt, ':cid', $classId);
			oci_execute($stmt);

			$sql2 = "DELETE FROM classes WHERE Id = :cid";
			$stmt2 = oci_parse($conn, $sql2);
			if (!$stmt2) {
				$_SESSION['sectionId'] = $sectionId;
				$_SESSION['sectionName'] = $sectionName;
				$_SESSION['classId'] = $classId;
				header("Location: ../editclass.php?error=sqlerror");
			}
			else{
				oci_bind_by_name($stmt2, ':cid', $classId);
				oci_execute($stmt2);

				$_SESSION['sectionId'] = $sectionId;
				$_SESSION['sectionName'] = $sectionName;
				header("Location: ../classes.php?success=classdeleted");
				exit();
			}
		}
	}
	else{
		//send to sections if no post
		header("Location: ../sections.php");
		exit();
	}
}
else{
	//send to login if no session
	header("Location: ../../../login.php?error=nosession");
	exit();
}

oci_close($conn);