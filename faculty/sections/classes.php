<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	unset($_SESSION['classId']);
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

include '../values.php';
?>

<!DOCTYPE html>
<html>
<head>
	<title>Classes</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../assets/css/faculty-dashboard.css">
	<link rel="icon" href="../../favicon.png">
</head>
<body>
	<?php include '../header.php';?>
	<div class="main">
		<?php include '../navigation.php'?>
		<div class="right-panel">
			<div class="page-title">
				<a href="sections.php"><button class="back-button"><img src="../../images/back.png"></button></a>
				<p>Classes of <?php echo $_POST['sectionName']?></p>
				<?php
				echo '<form method="post" action="createclass.php">';
				echo '<input type="hidden" name="sectionId" value="'.$sectionId.'" />';
				echo '<input type="hidden" name="sectionName" value="'.$sectionName.'" />';
				echo '<input type="submit" class="addsection-button" value="Create Class">';
				echo '</form>';
				?>
			</div>
			<div class="main-container">
				<?php
				require '../../includes/oracleConn.php';
				$sql = "SELECT * FROM classes where sectionId = :sid ORDER BY ClassDate";
				$stmt = oci_parse($conn, $sql);
				if (!$stmt) {
					echo '<p class="error-msg">Error retrieving data</p>';
				}
				else{
					oci_bind_by_name($stmt, ':sid', $sectionId);
					oci_execute($stmt);
					$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
					if($nrows == 0){
						echo "<p>No classes found. Add a class using the button above.</p>";
					}
					else{
						oci_execute($stmt);
						while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
							echo '<div class="class-box">';
								echo '<p class="class-name">'.$sectionName.' - '.$classtype[$row['CLASSTYPE']].'</p>';
								echo '<div class="class-times">';
									echo '<p class="class-time">'.'on '.date("M d", strtotime($row['CLASSDATE'])).'</p>';
									echo '<p class="class-time">from '.$classtime[$row['STARTTIMEID']].' to '.$classtime[$row['ENDTIMEID']].' at '.$row['ROOMNO'].'</p>';
									echo '<div class="class-menu">';
									echo '<form method="post" action="classattendance.php">';
										echo '<input type="hidden" name="sectionId" value="'.$sectionId.'" />';
										echo '<input type="hidden" name="sectionName" value="'.$sectionName.'" />';
										echo '<input type="hidden" name="classId" value="'.$row['ID'].'" />';
										echo '<input type="submit" class="students" value="Attendances">';
									echo '</form>';
									echo '<form method="post" action="editclass.php">';
										echo '<input type="hidden" name="sectionId" value="'.$sectionId.'" />';
										echo '<input type="hidden" name="sectionName" value="'.$sectionName.'" />';
										echo '<input type="hidden" name="classId" value="'.$row['ID'].'" />';
										echo '<input type="submit" class="classes" value="Edit">';
									echo '</form>';
								echo '</div>';
								echo '</div>';
								
							echo '</div>';
						}
					}
				}
				?>
			</div>
		</div>
	</body>
	</html>