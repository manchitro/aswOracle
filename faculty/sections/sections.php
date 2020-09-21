<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	unset($_SESSION['sectionId']);
	unset($_SESSION['sectionName']);
	unset($_SESSION['classId']);
}
else{
	header("Location: ../../login.php?error=nosession");
	exit();
}

include '../values.php';
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sections</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../assets/css/faculty-dashboard.css">
	
	<link rel="icon" href="../../favicon.png">
</head>
<body>
	<?php include '../header.php'?>
	<div class="main">
		<?php include '../navigation.php'?>
		<div class="right-panel">
			<div class="page-title">
				<p>Your Sections</p>
				<a href="addsection.php"><button class="addsection-button">Add Section</button></a>
			</div>
			<div class="main-container">
				<?php
				require '../../includes/oracleConn.php';
				$sql = "SELECT * FROM sections WHERE FacultyId = :fid";
				$stmt = oci_parse($conn, $sql);
				if (!$stmt) {
					$e = oci_error($conn);
					trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
					echo '<p class="error-msg">Error retrieving your data</p>';
				}
				else{
					oci_bind_by_name($stmt, ':fid', $_SESSION['userId']);
					oci_execute($stmt);
					$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
					if($nrows == 0){
						echo "<p>You have no sections as of now. Use the add section button to create a section.";
					}
					else{
						oci_execute($stmt);
						while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
							echo
							'<div class="section-box">
							<p class="sec-name">'.$row['SECTIONNAME'].'</p>';

							$sql2 = "SELECT * FROM sectionTimes WHERE sectionId = :sid";
							$stmt2 = oci_parse($conn, $sql2);
							if (!$stmt2) {
								echo '<p class="error-msg">Error retrieving your data - sectiontimes'.oci_error($stmt).'</p>';
							}
							else{
								oci_bind_by_name($stmt2, ':sid', $row['ID']);
								oci_execute($stmt2);
								$nrows = oci_fetch_all($stmt2, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
								if($nrows == 0){
									echo "<p>Error: No section time found</p>";
								}
								else{
									echo '<div class="sec-times">';
									oci_execute($stmt2);
									while (($row2 = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
										echo 
										'<p class="sec-time">'.$weekday[$row2['WEEKDAYID']]." ".$classtime[$row2['STARTTIMEID']]." - ".$classtime[$row2['ENDTIMEID']]." [".$classtype[$row2['CLASSTYPE']]."] at ".$row2['ROOMNO'].'</p>';
									}
									if($nrows == 1){
										echo '<p>2nd class of week not available/applicable</p>';
									}
									echo '</div>';
								}
								echo '<div class="section-menu">';
								echo '<form method="post" action="students.php">';
								echo '<input type="hidden" name="sectionId" value="'.$row['ID'].'" />';
								echo '<input type="hidden" name="sectionName" value="'.$row['SECTIONNAME'].'" />';
								echo '<input type="submit" class="students" value="Students">';
								echo '</form>';
								echo '<form method="post" action="classes.php">';
								echo '<input type="hidden" name="sectionId" value="'.$row['ID'].'" />';
								echo '<input type="hidden" name="sectionName" value="'.$row['SECTIONNAME'].'" />';
								echo '<input type="submit" class="classes" value="Classes">';
								echo '</form>';
								echo '<form method="post" action="editsection.php">';
								echo '<input type="hidden" name="sectionId" value="'.$row['ID'].'" />';
								echo '<input type="hidden" name="sectionName" value="'.$row['SECTIONNAME'].'" />';
								echo '<input type="submit" class="edit" value="Edit">';
								echo '</form>';
								echo '</div>';
							}
							echo '</div>';
						}
					}
				}
				?>
			</div>
		</div>
	</body>
	</html>