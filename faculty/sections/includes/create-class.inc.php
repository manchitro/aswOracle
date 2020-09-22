<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['submit']) && isset($_POST['sectionId'])){
		
		$sectionId = $_POST['sectionId'];
		$sectionName = $_POST['sectionName'];
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

					$sql = "INSERT INTO classes (SectionId, ClassDate, ClassType, StartTimeId, EndTimeId, RoomNo, CreatedAt) VALUES (:sid, :classDate, :ct, :stid, :etid, :room, current_timestamp(2))
					RETURNING Id INTO :p_val";
					$stmt = oci_parse($conn, $sql);

					if (!$stmt) {
						$_SESSION['sectionId'] = $sectionId;
						$_SESSION['sectionName'] = $sectionName;
						header("Location: ../createclass.php?error=sqlerror");
						exit();
					}
					else{
						echo "prepared, inserting".PHP_EOL;
						oci_bind_by_name($stmt, ':sid', $sectionId);
						oci_bind_by_name($stmt, ':classdate', $classDate);
						oci_bind_by_name($stmt, ':ct', $ct);
						oci_bind_by_name($stmt, ':stid', $st);
						oci_bind_by_name($stmt, ':etid', $et);
						oci_bind_by_name($stmt, ':room', $room);
						oci_bind_by_name($stmt, ":p_val", $lastId);
						oci_execute($stmt);

						$sql2 = "SELECT * FROM users where Id in (SELECT studentId from sectionstudents where sectionId = :sid)";
						$stmt2 = oci_parse($conn, $sql2);
						if (!$stmt2) {
							$_SESSION['sectionId'] = $sectionId;
							$_SESSION['sectionName'] = $sectionName;
							header("Location: ../createclass.php?error=sqlerror");
							exit();
						}
						else{
							oci_bind_by_name($stmt2, ':sid', $sectionId);
							oci_execute($stmt2);
							while (($row = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
								$sql3 = "INSERT INTO attendances (StudentId, ClassId, CreatedAt) VALUES (:stuid, :cid, current_timestamp(2))";
								$stmt3 = oci_parse($conn, $sql3);
								if (!$stmt3) {
									$_SESSION['sectionId'] = $sectionId;
									$_SESSION['sectionName'] = $sectionName;
									header("Location: ../createclass.php?error=sqlerror");
									exit();
								}
								else{
									//$entry = "0";
									oci_bind_by_name($stmt3, ':stuid', $stu_id);
									oci_bind_by_name($stmt3, ':cid', $lastId);
									oci_execute($stmt3);
								}
							}
							$_SESSION['sectionId'] = $sectionId;
							$_SESSION['sectionName'] = $sectionName;
							header("Location: ../students.php?success=classcreated&date=".$classDate);
							echo "done";
							exit();
						}
					}
				}
				else{
					$_SESSION['sectionId'] = $sectionId;
					$_SESSION['sectionName'] = $sectionName;
					header("Location: ../createclass.php?error=maxlen3h");
					exit();
				}
			}
			else{
				$_SESSION['sectionId'] = $sectionId;
				$_SESSION['sectionName'] = $sectionName;
				header("Location: ../createclass.php?error=minlen1h");
				exit();

			}
		}
		else{
			$_SESSION['sectionId'] = $sectionId;
			$_SESSION['sectionName'] = $sectionName;
			header("Location: ../createclass.php?error=neglen");
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