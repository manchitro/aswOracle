<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['submit-single']))
	{
		if(isset($_POST['sectionId'])){
			$sectionId = $_POST['sectionId'];
			$sectionName = $_POST['sectionName'];
			$academicId = $_POST['student-id'];
			$firstName = $_POST['student-first-name'];
			$lastName = $_POST['student-last-name'];

			require '../../../includes/oracleConn.php';

			$sql = "SELECT * FROM users where academicId = :aid";
			$stmt = oci_parse($conn, $sql);
			if (!$stmt) {
				$_SESSION['sectionId'] = $sectionId;
				$_SESSION['sectionName'] = $sectionName;
				header("Location: ../addstudent.php?error=sqlerror");
				exit();
			}
			else{
				oci_bind_by_name($stmt, ':aid', $academicId);
				oci_execute($stmt);
				$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
				if($nrows > 0){
					//if already exists
					oci_execute($stmt);
					if (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
						$foundId = $row['ID'];

						$sql1 = "SELECT * FROM SectionStudents WHERE SectionId = :sid AND StudentId = :stuid";
						$stmt1 = oci_parse($conn, $sql1);

						if (!$stmt1) {
							$_SESSION['sectionId'] = $sectionId;
							$_SESSION['sectionName'] = $sectionName;
							header("Location: ../addstudent.php?error=sqlerror".mysqli_error($conn));
							exit();
						}
						else{
							oci_bind_by_name($stmt1, ':sid', $sectionId);
							oci_bind_by_name($stmt1, ':stuid', $foundId);
							oci_execute($stmt1);
							$nrows = oci_fetch_all($stmt1, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
							if($nrows == 0){
								$sql2 = "INSERT INTO sectionstudents (SectionId, StudentId) VALUES (:sid, :stuid)";
								$stmt2 = oci_parse($conn, $sql2);

								if (!$stmt2) {
									$_SESSION['sectionId'] = $sectionId;
									$_SESSION['sectionName'] = $sectionName;
									header("Location: ../addstudent.php?error=sqlerror");
									exit();
								}
								else{
									oci_bind_by_name($stmt2, ':aid', $academicId);
									oci_bind_by_name($stmt2, ':stuid', $foundId);
									oci_execute($stmt2);

									$sql3 = "SELECT * FROM classes where sectionId = :sid";
									$stmt3 = oci_parse($conn, $sql3);
									if (!$stmt3) {
										echo '<p class="error-msg">Error retrieving data</p>';
									}
									else{
										oci_bind_by_name($stmt3, ':sid', $sectionId);
										oci_execute($stmt3);
										while (($row = oci_fetch_array($stmt3, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
											$sql4 = "INSERT INTO attendances (StudentId, ClassId, CreatedAt) VALUES (:stuid, :cid, current_timestamp(2))";
											$stmt4 = oci_parse($conn, $sql4);
											if (!$stmt4) {
												$_SESSION['sectionId'] = $sectionId;
												$_SESSION['sectionName'] = $sectionName;
												header("Location: ../createclass.php?error=sqlerror");
												exit();
											}
											else{
												//$entry = "0";
												oci_bind_by_name($stmt4, ':stuid', $foundId);
												oci_bind_by_name($stmt4, ':cid', $row['ID']);
												oci_execute($stmt4);
											}
										}
										$_SESSION['sectionId'] = $sectionId;
										$_SESSION['sectionName'] = $sectionName;
										header("Location: ../addstudent.php?success=added&aid=".$academicId."&name=".$stu_firstname." ".$stu_lastname."&existing=Existing%20Student%20");
										exit();
									}	
								}
							}
							else{
								$_SESSION['sectionId'] = $sectionId;
								$_SESSION['sectionName'] = $sectionName;
								header("Location: ../addstudent.php?error=alreadyexists&aid=".$academicId."&name=".$stu_firstname." ".$stu_lastname);
								exit();
							}
						}
					}
					else{
						$_SESSION['sectionId'] = $sectionId;
						$_SESSION['sectionName'] = $sectionName;
						header("Location: ../addstudent.php?error=lostid");
						exit();
					}
				}else{
					//if not already exists
					$sql = "INSERT INTO users (AcademicId, FirstName, LastName, Email, Password, UserType, CreatedAt) VALUES (:aid, :fname, :lname, NULL, NULL, '1', current_timestamp(2))
					RETURNING Id INTO :p_val";
					$stmt = oci_parse($conn, $sql);
					if (!$stmt) {
						$_SESSION['sectionId'] = $sectionId;
						$_SESSION['sectionName'] = $sectionName;
						header("Location: ../addstudent.php?error=sqlerror");
						exit();
					}
					else{
						$firstName = strtoupper($firstName);
						$lastName = strtoupper($lastName);
						oci_bind_by_name($stmt, ':aid', $academicId);
						oci_bind_by_name($stmt, ':fname', $firstName);
						oci_bind_by_name($stmt, ':lname', $lastName);
						oci_bind_by_name($stmt, ":p_val", $lastId);
						oci_execute($stmt);
						echo $academicId." inserted id: ".$lastId;

						$sql2 = "INSERT INTO sectionstudents (SectionId, StudentId) VALUES (:sid, :stuid)";
						$stmt2 = oci_parse($conn, $sql2);
						if (!$stmt2) {
							$_SESSION['sectionId'] = $sectionId;
							$_SESSION['sectionName'] = $sectionName;
							header("Location: ../addstudent.php?error=sqlerror");
							exit();
						}
						else{
							oci_bind_by_name($stmt2, ':sid', $sectionId);
							oci_bind_by_name($stmt2, ':stuid', $lastId);
							oci_execute($stmt2);
							
							$sql3 = "SELECT * FROM classes where sectionId = :sid";
							$stmt3 = oci_parse($conn, $sql3);
							if (!$stmt3) {
								echo '<p class="error-msg">Error retrieving data</p>';
							}
							else{
								oci_bind_by_name($stmt3, ':sid', $sectionId);
								oci_execute($stmt3);
								while (($row = oci_fetch_array($stmt3, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
									$sql4 = "INSERT INTO attendances (StudentId, ClassId, CreatedAt) VALUES (:stuid, :cid, current_timestamp(2))";
									$stmt4 = oci_parse($conn, $sql4);
									if (!$stmt4) {
										$_SESSION['sectionId'] = $sectionId;
										$_SESSION['sectionName'] = $sectionName;
										header("Location: ../addstudent.php?error=sqlerror");
										exit();
									}
									else{
										//$entry = "0";
										oci_bind_by_name($stmt4, ':stuid', $lastId);
										oci_bind_by_name($stmt4, ':cid', $row['ID']);
										oci_execute($stmt4);
									}
								}
								$_SESSION['sectionId'] = $sectionId;
								$_SESSION['sectionName'] = $sectionName;
								header("Location: ../addstudent.php?success=added&aid=".$academicId."&name=".$firstName." ".$lastName."&existing=New%20Student%20");
								exit();
							}
						}
					}
				}
			}
		}
		else{
			//send to sections if no section ID was found 
			header("Location: ../sections.php?error=addstunosec");
		}
		mysqli_stmt_close($stmt);
		mysqli_close($conn);
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