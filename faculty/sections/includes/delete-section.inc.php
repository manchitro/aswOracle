<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if (isset($_POST['submit']) && isset($_POST['sectionId']) && $_POST['sectionId'] !== "") {
		$sectionId = $_POST['sectionId'];
		$sectionName = $_POST['sectionName'];

		require '../../../includes/oracleConn.php';

		$sql = "SELECT Id FROM classes WHERE SectionId = :sid";
		$stmt = oci_parse($conn, $sql);
		if (!$stmt) {
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			header("Location: ../editsection.php?error=sqlerror");
			//echo "get classes ".mysqli_error($conn);
			exit();
		}
		else{
			oci_bind_by_name($stmt, ':sid', $sectionId);
			oci_execute($stmt);

			//=delete all attendances for each class
			while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				$sql2 = "DELETE FROM attendances where classId = :cid";
				$stmt2 = oci_parse($conn, $sql2);

				if (!$stmt2) {
					$_SESSION['sectionId'] = $sectionId;
					$_SESSION['sectionName'] = $sectionName;
					header("Location: ../editsection.php?error=sqlerror");
					//echo "delete att ".mysqli_error($conn);
					exit();
				}
				else{
					oci_bind_by_name($stmt2, ':cid', $class_Id);
					oci_execute($stmt2);
					echo "deleted attendances";
				}
			}
		}

		//after deleting all attendances delete all classes
		$sql3 = "DELETE FROM classes where sectionId = :sid";
		$stmt3 = oci_parse($conn, $sql3);

		if (!$stmt3) {
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			header("Location: ../editsection.php?error=sqlerror");
			//echo "delete classes ".mysqli_error($conn);
			exit();
		}
		else{
			oci_bind_by_name($stmt3, ':sid', $sectionId);
			oci_execute($stmt3);
			echo "deleted classes";
		}

		//after deleting classes remove all students from section
		$sql4 = "DELETE FROM sectionStudents where sectionId = :sid";
		$stmt4 = oci_parse($conn, $sql4);

		if (!$stmt4) {
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			header("Location: ../editsection.php?error=sqlerror");
			//echo "delete sectionstudents ".mysqli_error($conn);
			exit();
		}
		else{
			oci_bind_by_name($stmt4, ':sid', $sectionId);
			oci_execute($stmt4);
			echo "deleted sectionstudents";
		}

		//after removing all students, delete all sectionTimes
		$sql5 = "DELETE FROM sectionTimes where sectionId = :sid";
		$stmt5 = oci_parse($conn, $sql5);

		if (!$stmt5) {
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			header("Location: ../editsection.php?error=sqlerror");
			//echo "delete sectionstudents ".mysqli_error($conn);
			exit();
		}
		else{
			oci_bind_by_name($stmt5, ':sid', $sectionId);
			oci_execute($stmt5);
			echo "deleted sectionstudents";
		}

		//after deleting all sectionTimes, delete the section
		$sql6 = "DELETE FROM sections where Id = :sid";
		$stmt6 = oci_parse($conn, $sql6);

		if (!$stmt6) {
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			header("Location: ../editsection.php?error=sqlerror");
			//echo "delete sectionstudents ".mysqli_error($conn);
			exit();
		}
		else{
			oci_bind_by_name($stmt6, ':sid', $sectionId);
			oci_execute($stmt6);
			echo "deleted sectionstudents";

			mysqli_stmt_close($stmt);
			mysqli_close($conn);

			//redirect to sections after creation
			header("Location: ../sections.php?secdeleted=".$sectionName);
			exit();
		}
	}
	else{
		header("Location: ../sections.php");
		exit();
	}
}
else{
	header("Location: ../../../login.php?error=nosession");
	exit();
}

?>