<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['key']) && isset($_POST['facultyId'])){
		$key = $_POST['key'];
		$key = "%{$key}%";
		$facultyId = $_POST['facultyId'];
		
		require '../../../includes/oracleConn.php';
		
		if ($key == "") {
			echo "<p>Please insert a search term first</p>";
		}
		else if(preg_match("~[0-9]~", $key) == 1){
			$sql = "SELECT * FROM faculty_".$facultyId."_students where academicid like :key";
			$stmt = oci_parse($conn, $sql);
			if (!$stmt) {
				echo "Failed to retrieve data";
				exit();
			}
			else{
				oci_bind_by_name($stmt, ':key', $key);
				oci_execute($stmt);

				echo '<table class=student-table>';
					echo '<tr><th>Academic ID</th><th>Name</th><th>Section</th></tr>';
					oci_execute($stmt);
					while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
						echo '<tr><td>'.$row['ACADEMICID'].'</td><td>'.$row['FIRSTNAME'].' '.$row['LASTNAME'].'</td><td>'.$row['SECTIONNAME'].'</td></tr>';				
						// $sql2 = "SELECT AcademicId, FirstName, LastName FROM users where AcademicId like :key AND UserType = 1 AND id in
						// (select StudentId FROM SectionStudents where SectionId = :sid)";
						// $stmt2 = oci_parse($conn, $sql2);
						// if (!$stmt2) {
						// 	echo "Failed to retrieve data";
						// 	exit();
						// }
						// else{
						// 	oci_bind_by_name($stmt2, ':key', $key);
						// 	oci_bind_by_name($stmt2, ':sid', $row['ID']);
						// 	oci_execute($stmt2);
							
						// 	while (($row2 = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
						// 		echo '<tr><td>'.$row2['ACADEMICID'].'</td><td>'.$row2['FIRSTNAME'].' '.$row2['LASTNAME'].'</td><td>'.$row['SECTIONNAME'].'</td></tr>';				
						// 	}
						// }
					}
					echo '</table>';
			}
			// $sql = "SELECT Id, SectionName FROM sections WHERE FacultyId = :fid";
			// $stmt = oci_parse($conn, $sql);
			// if (!$stmt) {
			// 	echo "Failed to retrieve data";
			// 	exit();
			// }
			// else{
			// 	oci_bind_by_name($stmt, ':fid', $facultyId);
			// 	oci_execute($stmt);
			// 	$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
			// 	if($nrows == 0){
			// 		echo "<p>You have no students as of now.";
			// 	}
			// 	else{
			// 		echo '<table class=student-table>';
			// 		echo '<tr><th>Academic ID</th><th>Name</th><th>Section</th></tr>';
			// 		oci_execute($stmt);
			// 		while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
			// 			$sql2 = "SELECT AcademicId, FirstName, LastName FROM users where AcademicId like :key AND UserType = 1 AND id in
			// 			(select StudentId FROM SectionStudents where SectionId = :sid)";
			// 			$stmt2 = oci_parse($conn, $sql2);
			// 			if (!$stmt2) {
			// 				echo "Failed to retrieve data";
			// 				exit();
			// 			}
			// 			else{
			// 				oci_bind_by_name($stmt2, ':key', $key);
			// 				oci_bind_by_name($stmt2, ':sid', $row['ID']);
			// 				oci_execute($stmt2);
							
			// 				while (($row2 = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
			// 					echo '<tr><td>'.$row2['ACADEMICID'].'</td><td>'.$row2['FIRSTNAME'].' '.$row2['LASTNAME'].'</td><td>'.$row['SECTIONNAME'].'</td></tr>';				
			// 				}
			// 			}
			// 		}
			// 		echo '</table>';
			// 	}
		}
		else{
			$sql = "SELECT * FROM faculty_".$facultyId."_students where firstname like :key or lastname like :key";
			$stmt = oci_parse($conn, $sql);
			if (!$stmt) {
				echo "Failed to retrieve data";
				exit();
			}
			else{
				oci_bind_by_name($stmt, ':key', $key);
				oci_execute($stmt);

				echo '<table class=student-table>';
					echo '<tr><th>Academic ID</th><th>Name</th><th>Section</th></tr>';
					oci_execute($stmt);
					while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
						echo '<tr><td>'.$row['ACADEMICID'].'</td><td>'.$row['FIRSTNAME'].' '.$row['LASTNAME'].'</td><td>'.$row['SECTIONNAME'].'</td></tr>';				
						// $sql2 = "SELECT AcademicId, FirstName, LastName FROM users where AcademicId like :key AND UserType = 1 AND id in
						// (select StudentId FROM SectionStudents where SectionId = :sid)";
						// $stmt2 = oci_parse($conn, $sql2);
						// if (!$stmt2) {
						// 	echo "Failed to retrieve data";
						// 	exit();
						// }
						// else{
						// 	oci_bind_by_name($stmt2, ':key', $key);
						// 	oci_bind_by_name($stmt2, ':sid', $row['ID']);
						// 	oci_execute($stmt2);
							
						// 	while (($row2 = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
						// 		echo '<tr><td>'.$row2['ACADEMICID'].'</td><td>'.$row2['FIRSTNAME'].' '.$row2['LASTNAME'].'</td><td>'.$row['SECTIONNAME'].'</td></tr>';				
						// 	}
						// }
					}
					echo '</table>';
				}
				
				/*$sql = "UPDATE attendances SET Entry = ? WHERE Id = ?";
				$stmt = mysqli_stmt_init($conn);
				
				if (!mysqli_stmt_prepare($stmt, $sql)) {
					echo "Attendance update failed: sqlerror";
					exit();
				}
				else{
					mysqli_stmt_bind_param($stmt, "ii", $attEntry, $attId);
					mysqli_stmt_execute($stmt);
					
					echo "changed: ".$attId." to ".$attEntry;
					exit();
				}*/		
			}
		}
	else{
		echo "Attendance update failed: no post";
	}
}
else{
	echo "Attendance update failed: no session";
}

oci_close($conn);