<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if (isset($_POST['sectionId']) && $_POST['sectionId'] !== "") {
		if(isset($_POST['submit']))
		{
			if (isset($_POST["foo"])) 
			{
				$idList = $_POST['foo'];
				$sectionId = $_POST['sectionId'];
				
				require '../../../includes/oracleConn.php';

				$sql0 = "SELECT * FROM classes where sectionId = :sid";
				$stmt0 = oci_parse($conn, $sql0);
				if (!$stmt0) {
					echo '<p class="error-msg">Error retrieving data</p>';
				}
				else{
					oci_bind_by_name($stmt0, ':sid', $sectionId);
					oci_execute($stmt0);
					$nrows = oci_fetch_all($stmt0, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
					if($nrows == 0){
						echo "<p>No classes found. Add a class using the button above.</p>";
					}
					else{
						oci_execute($stmt0);
						while (($row = oci_fetch_array($stmt0, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
							$sql1 ='DELETE FROM attendances WHERE classId = :cid AND StudentId in ('.implode(",", $idList).')';
							$stmt1 = oci_parse($conn, $sql1);
							if (!$stmt1) {
								header("Location: ../removestudents.php?error=sqlerror");
								exit();
							}
							else{
								oci_bind_by_name($stmt1, ':cid', $classId);
								oci_execute($stmt1);

								header("Location: ../removestudents.php?success=removedstudents");
							}
						}

					}
				}

				$sql ='DELETE FROM sectionstudents WHERE SectionId = :sid AND StudentId in ('.implode(",", $idList).')';
				$stmt = oci_parse($conn, $sql);
				if (!$stmt) {
					header("Location: ../removestudents.php?error=sqlerror");
					exit();
				}
				else{
					oci_bind_by_name($stmt, ':sid', $sectionId);
					oci_execute($stmt);

					header("Location: ../removestudents.php?success=removedstudents");
				}
			}
			else
			{
				$_SESSION['sectionId'] = $_POST['sectionId'];
				$_SESSION['sectionName'] = $_POST['sectionName'];
				header("Location: ../removestudents.php?error=nosel");
			}
		}
		else{
		//send to sections if no post
			header("Location: ../sections.php");
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