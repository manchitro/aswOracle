<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['submit']))
	{
		//echo "Faculty: ".$_SESSION['userId'].PHP_EOL;
		$sectionName = $_POST['section-name'];
		
		$weekDay1 = $_POST['weekday-1'];
		$startTime1 = $_POST['start-time-1'];
		$endTime1 = $_POST['end-time-1'];
		$classType1 = $_POST['class-type-1'];
		$room1 = $_POST['room-1'];
		
		$weekDay2 = $_POST['weekday-2'];
		$startTime2 = $_POST['start-time-2'];
		$endTime2 = $_POST['end-time-2'];
		$classType2 = $_POST['class-type-2'];
		$room2 = $_POST['room-2'];
		
		//validate section time 1
		if ($startTime1 < $endTime1) {
			if($endTime1 >= $startTime1 + 2){
				if ($endTime1 <= $startTime1 + 6) {
					if (!isset($_POST['one-class'])) {
						//if one class is not set
						if ($startTime2 < $endTime2) {
							if($endTime2 >= $startTime2 + 2){
								if ($endTime2 <= $startTime2 + 6) {
									if ($weekDay1 !== $weekDay2) {
										if(!empty($room2) && $room2 !== ""){
											require '../../../includes/oracleConn.php';
											
											$sql = "INSERT INTO sections (SectionName, FacultyId, CreatedAt) VALUES (:sectionName, :fid, current_timestamp(2))
											RETURNING Id INTO :p_val";
											$stmt = oci_parse($conn, $sql);
											
											if (!$stmt) {
												header("Location: ../addsection.php?error=sqlerrorhere");
												exit();
											}
											else{
												$lastId="";
												oci_bind_by_name($stmt, ':fid', $_SESSION['userId']);
												oci_bind_by_name($stmt, ':sectionName', $sectionName);
												oci_bind_by_name($stmt, ":p_val", $lastId);
												oci_execute($stmt);
												
												$sql = "INSERT INTO sectiontimes (StartTimeId, EndTimeId, WeekDayId, ClassType, RoomNo, SectionId, CreatedAt) VALUES (:stid, :etid, :wdid, :ct, :room, :sid, current_timestamp(2))";
												$stmt = oci_parse($conn, $sql);
												
												if (!$stmt) {
													header("Location: ../addsection.php?error=sqlerror");
													exit();
												}
												else{
													//section time 1
													oci_bind_by_name($stmt, ':stid', $startTime1);
													oci_bind_by_name($stmt, ':etid',$endTime1);
													oci_bind_by_name($stmt, ':wdid', $weekDay1);
													oci_bind_by_name($stmt, ':ct', $classType1);
													oci_bind_by_name($stmt, ':room', $room1);
													oci_bind_by_name($stmt, ':sid', $lastId);
													oci_execute($stmt);
													
													//section time 2
													oci_bind_by_name($stmt, ':stid', $startTime2);
													oci_bind_by_name($stmt, ':etid',$endTime2);
													oci_bind_by_name($stmt, ':wdid', $weekDay2);
													oci_bind_by_name($stmt, ':ct', $classType2);
													oci_bind_by_name($stmt, ':room', $room2);
													oci_bind_by_name($stmt, ':sid', $lastId);
													oci_execute($stmt);
													
													//redirect to sections after creation
													header("Location: ../sections.php?seccreated=".$sectionName);
													exit();
												}
											}
										}
										else{
											header("Location: ../addsection.php?error=noroom&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
											exit();
										}
									}
									else{
										header("Location: ../addsection.php?error=sameday&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
										exit();
									}
								}
								else{
									header("Location: ../addsection.php?error=maxlen3h2&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
									exit();
								}
							}
							else{
								header("Location: ../addsection.php?error=minlen1h2&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
								exit();
							}
						}
						else{
							header("Location: ../addsection.php?error=neglen2&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
							exit();
						}
					}
					else{
						//one class is set
						require '../../../includes/oracleConn.php';
						
						$sql = "INSERT INTO sections (SectionName, FacultyId, CreatedAt) VALUES (:sectionName, :fid, current_timestamp(2))
						RETURNING Id INTO :p_val";
						$stmt = oci_parse($conn, $sql);
						
						if (!$stmt) {
							header("Location: ../addsection.php?error=sqlerrorhere");
							exit();
						}
						else{
							$lastId="";
							oci_bind_by_name($stmt, ':fid', $_SESSION['userId']);
							oci_bind_by_name($stmt, ':sectionName', $sectionName);
							oci_bind_by_name($stmt, ":p_val", $lastId);
							oci_execute($stmt);
							
							$sql = "INSERT INTO sectiontimes (StartTimeId, EndTimeId, WeekDayId, ClassType, RoomNo, SectionId, CreatedAt) VALUES (:stid, :etid, :wdid, :ct, :room, :sid, current_timestamp(2))";
							$stmt = oci_parse($conn, $sql);
							
							if (!$stmt) {
								header("Location: ../addsection.php?error=sqlerror");
								exit();
							}
							else{
								//section time 1
								oci_bind_by_name($stmt, ':stid', $startTime1);
								oci_bind_by_name($stmt, ':etid',$endTime1);
								oci_bind_by_name($stmt, ':wdid', $weekDay1);
								oci_bind_by_name($stmt, ':ct', $classType1);
								oci_bind_by_name($stmt, ':room', $room1);
								oci_bind_by_name($stmt, ':sid', $lastId);
								oci_execute($stmt);

								//redirect to sections after creation
								header("Location: ../sections.php?seccreated=".$sectionName);
								exit();
							}
						}
					}
				}
				else{
					header("Location: ../addsection.php?error=maxlen3h&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
					exit();
				}
			}
			else{
				header("Location: ../addsection.php?error=minlen1h&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
				exit();
			}
		}
		else{
			header("Location: ../addsection.php?error=neglen&name=".$sectionName."&wd1=".$weekDay1."&st1=".$startTime1."&et1=".$endTime1."&ct1=".$classType1."&r1=".$room1."&wd2=".$weekDay2."&st2=".$startTime2."&et2=".$endTime2."&ct2=".$classType2."&r2=".$room2);
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

oci_close($conn);