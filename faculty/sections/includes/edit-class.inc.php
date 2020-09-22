<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['submit']) && isset($_POST['sectionId']) && isset($_POST['classId'])){
		
		$sectionId = $_POST['sectionId'];
		$sectionName = $_POST['sectionName'];
		$classId = $_POST['classId'];
		$classDate = $_POST['class-date'];
		$st = $_POST['start-time'];
		$et = $_POST['end-time'];
		$ct = $_POST['class-type'];
		$room = $_POST['room'];

		if ($st < $et) {
			if($et >= $st + 2){
				if ($et <= $st + 6) {
					echo implode(",", $_POST).PHP_EOL;
					require '../../../includes/oracleConn.php';

					$sql = "UPDATE classes SET ClassDate = :cd, ClassType = :ct, StartTimeId = :stid, EndTimeId = :etid, RoomNo = :room WHERE Id = :cid";
					$stmt = oci_parse($conn, $sql);
					if (!$stmt) {
						$_SESSION['sectionId'] = $sectionId;
						$_SESSION['sectionName'] = $sectionName;
						$_SESSION['classId'] = $classId;
						header("Location: ../editclass.php?error=sqlerror");
						exit();
					}
					else{
						oci_bind_by_name($stmt, ':cd', $classDate);
						oci_bind_by_name($stmt, ':ct', $ct);
						oci_bind_by_name($stmt, ':stid', $st);
						oci_bind_by_name($stmt, ':etid', $et);
						oci_bind_by_name($stmt, ':room', $room);
						oci_bind_by_name($stmt, ':cid', $classId);
						oci_execute($stmt);
						
						header("Location: ../classes.php?success=classedited");
						exit();
					}
				}
				else{
					$_SESSION['sectionId'] = $sectionId;
					$_SESSION['sectionName'] = $sectionName;
					$_SESSION['classId'] = $classId;
					header("Location: ../editclass.php?error=maxlen3h");
					exit();
				}
			}
			else{
				$_SESSION['sectionId'] = $sectionId;
				$_SESSION['sectionName'] = $sectionName;
				$_SESSION['classId'] = $classId;
				header("Location: ../editclass.php?error=minlen1h");
				exit();

			}
		}
		else{
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			$_SESSION['classId'] = $classId;
			header("Location: ../editclass.php?error=neglen");
			exit();

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