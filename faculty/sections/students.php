<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['sectionId']) && $_POST['sectionId'] !== ""){
		$sectionId = $_POST['sectionId'];
		$sectionName = $_POST['sectionName'];
	}
	else if(isset($_SESSION['sectionId']) && $_SESSION['sectionId'] !== ""){
		$sectionId = $_SESSION['sectionId'];
		$sectionName = $_SESSION['sectionName'];
		$_POST['sectionId'] = $sectionId;
		$_POST['sectionName'] = $sectionName;
	}
	else{
		header("Location: sections.php");
		exit();
	}
}
else{
	header("Location: ../login.php?error=nosession");
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Students</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../assets/css/faculty-dashboard.css">
	<link rel="icon" href="../../favicon.png">
	<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
</head>
<body>
	<?php include '../header.php';?>
	<div class="main">
		<?php include '../navigation.php'?>
		<div class="right-panel">
			<div class="page-title">
				<a href="sections.php"><button class="back-button"><img src="../../images/back.png"></button></a>
				<p>Student list of <?php echo $_POST['sectionName']?></p>
				<?php
				echo '<form method="post" action="addstudent.php">';
				echo '<input type="hidden" name="sectionId" value="'.$sectionId.'" />';
				echo '<input type="hidden" name="sectionName" value="'.$sectionName.'" />';
				echo '<input type="submit" class="addsection-button" value="Add Students">';
				echo '</form>';
				echo '<form method="post" action="removestudents.php">';
				echo '<input type="hidden" name="sectionId" value="'.$sectionId.'" />';
				echo '<input type="hidden" name="sectionName" value="'.$sectionName.'" />';
				echo '<input type="submit" class="addsection-button" value="Remove Students">';
				echo '</form>';
				?>
			</div>
			<div class="main-container-table">
				<table class="student-table">
					<?php
					$index = 1;
					require '../../includes/oracleConn.php';

					$sql2 = "SELECT * FROM classes where SectionId = :sid ORDER BY classDate";
					$stmt2 = oci_parse($conn, $sql2);
					if (!$stmt2) {
						echo '<p class="error-msg">Error retrieving data</p>';
					}
					else{
						oci_bind_by_name($stmt2, ':sid', $sectionId);
						oci_execute($stmt2);
						
						echo '<tr>';
						echo '<th>#</th><th>ID</th><th>Name</th>';
						while (($row = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
							$formattedDate = date("M d", strtotime($row['CLASSDATE']));
							echo '<th>'.$formattedDate.'</th>';
						}
						echo '</tr>';
					}

					$sql = "SELECT * FROM users where Id in (SELECT studentId from sectionstudents where sectionId = :sid)";
					$stmt = oci_parse($conn, $sql);
					if (!$stmt) {
						echo '<p class="error-msg">Error retrieving data</p>';
					}
					else{
						oci_bind_by_name($stmt, ':sid', $sectionId);
						oci_execute($stmt);
						while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
							echo '<tr>';
							echo '<td>'.$index.'</td>'.'<td>'.$row['ACADEMICID'].'</td>'.'<td>'.$row['FIRSTNAME'].' '.$row['LASTNAME'].'</td>';
						//for each student
						//echo $stu_id.' '.$stu_academicId.' '.$stu_firstname.' '.$stu_lastname.' '.$stu_email.' '.$stu_pass.' '.$stu_userType.' '.$stu_createdAt.'<br>';

							$sql2 = "SELECT * FROM classes where SectionId = :sid";
							$stmt2 = oci_parse($conn, $sql2);
							if (!$stmt2) {
								echo '<p class="error-msg">Error retrieving data</p>';
							}
							else{
								oci_bind_by_name($stmt2, ':sid', $sectionId);
								oci_execute($stmt2);
								while (($row2 = oci_fetch_array($stmt2, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
								//for each class
								//echo $class_id.' '.$class_sectionId.' '.$class_date.' '.$class_type.' '.$class_startTimeId.' '.$class_endTimeId.' '.$class_roomNo.' '.$class_QRCode.' '.$class_QRCodeDisplayStartTIme.' '.$class_QRCodeDisplayEndTIme.' '.$class_createdAt.'<br>';

									$sql3 = "SELECT * FROM attendances where ClassId = :cid AND StudentId = :stuid ORDER BY StudentId";
									$stmt3 = oci_parse($conn, $sql3);
									if (!$stmt3) {
										echo '<p class="error-msg">Error retrieving data</p>';
									}
									else{
										oci_bind_by_name($stmt3, ':cid', $row2['ID']);
										oci_bind_by_name($stmt3, ':stuid', $row['ID']);
										oci_execute($stmt3);
										while (($row3 = oci_fetch_array($stmt3, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
										//echo $att_id.' '.$att_studentId.' '.$att_classId.' '.$att_entry.' '.$att_scanTime.' '.$att_createdAt.'<br>';
											echo '<td contenteditable class="att" id="'.$row3['ID'].'">'.$row3['ENTRY'].'</td>';
										}
									}
								}
							}
							echo '</tr>';
							$index++;
						}
					}
					?>
					<script type="text/javascript" src="../js/attedit.js"></script>
				</table>
				<div class="attMessage"></div>
			</div>
		</div>
	</body>
	</html>

	<?php oci_close($conn);?>
