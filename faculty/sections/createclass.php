<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['sectionId']) && $_POST['sectionId'] !== ""){
		$sectionId = $_POST['sectionId'];
		$sectionName = $_POST['sectionName'];
	}
	else if (isset($_SESSION['sectionId'])){
		$sectionId = $_POST['sectionId'] = $_SESSION['sectionId'];
		$sectionName = $_POST['sectionName'] = $_SESSION['sectionName'];
	}
	else{
		header("Location: sections.php");
		exit();
	}

	require '../../includes/oracleConn.php';

	$sectionId = $_POST['sectionId'];
	$sectionName = $_POST['sectionName'];

	$sql = "SELECT * FROM sectionTimes WHERE sectionId = :sid";
	$stmt = oci_parse($conn, $sql);
	if (!$stmt) {
		echo '<p class="error-msg">Error retrieving your data. Please go back and try again!</p>';
	}
	else{
		oci_bind_by_name($stmt, ':sid', $sectionId);
		oci_execute($stmt);
		$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
		if($nrows == 0){
			echo "<p>Error: No section time found</p>";
		}
		else{
			oci_execute($stmt);
			//record section time 1
			if (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				$time1id = $row['ID'];
				$st1 = $row['STARTTIMEID'];
				$et1 = $row['ENDTIMEID'];
				$wt1 = $row['WEEKDAYID'];
				$ct1 = $row['CLASSTYPE'];
				$room1 = $row['ROOMNO'];
			}
			else{
				$time1id = -1;
				$st1 = 0;
				$et1 = 0;
				$wt1 = 0;
				$ct1 = 0;
				$room1 = "";
			}

			//record section time 2
			if (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				$time2id = $row['ID'];
				$st2 = $row['STARTTIMEID'];
				$et2 = $row['ENDTIMEID'];
				$wt2 = $row['WEEKDAYID'];
				$ct2 = $row['CLASSTYPE'];
				$room2 = $row['ROOMNO'];

				$oneClass = false;
			}
			else{
				$time2id = -1;
				$st2 = 0;
				$et2 = 0;
				$wt2 = 0;
				$ct2 = 0;
				$room2 = "";

				$oneClass = true;
			}
		}
	}
}
else{
	header("Location: ../../login.php?error=nosession");
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Create Class</title>
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
				<a href="students.php"><button class="back-button"><img src="../../images/back.png"></button></a>
				<p>Create a class for <?php echo $_POST['sectionName']?></p>
			</div>
			<div class="main-container">
				<form method="post" action="includes/create-class.inc.php">
					<div class="section-time">
						<p>Enter class's date, time, type and place (the default is autofilled with section time 1)</p>
						<div class="error1">
							<?php
							if (isset($_GET['error'])) {
								if ($_GET['error'] == 'neglen') {
									echo "<p>Class duration has to between 1 to 3 hours</p>";
								}
								if ($_GET['error'] == 'minlen1h') {
									echo "<p>Class duration has to between 1 to 3 hours</p>";
								}
								if ($_GET['error'] == 'maxlen3h') {
									echo "<p>Class duration has to between 1 to 3 hours</p>";
								}
								if ($_GET['error'] == 'sqlerror') {
									echo "<p>Your request could not be processed!</p>";
								}
							}
							?>
						</div>	
						Date: <input type="date" name="class-date" value='<?php echo date("Y\-m\-d")?>' min='<?php echo date("Y\-m\-d")?>' max='<?php echo date("Y\-m\-d", strtotime("+1 year"))?>'>
						from <select name="start-time">
							<option value="0" <?php if ( $st1 == 0 ) echo 'selected' ; ?> >8:00</option>
							<option value="1" <?php if ( $st1 == 1 ) echo 'selected' ; ?> >8:30</option>
							<option value="2" <?php if ( $st1 == 2 ) echo 'selected' ; ?> >9:00</option>
							<option value="3" <?php if ( $st1 == 3 ) echo 'selected' ; ?> >9:30</option>
							<option value="4" <?php if ( $st1 == 4 ) echo 'selected' ; ?> >10:00</option>
							<option value="5" <?php if ( $st1 == 5 ) echo 'selected' ; ?> >10:30</option>
							<option value="6" <?php if ( $st1 == 6 ) echo 'selected' ; ?> >11:00</option>
							<option value="7" <?php if ( $st1 == 7 ) echo 'selected' ; ?> >11:30</option>
							<option value="8" <?php if ( $st1 == 8 ) echo 'selected' ; ?> >12:00</option>
							<option value="9" <?php if ( $st1 == 9 ) echo 'selected' ; ?> >12:30</option>
							<option value="10" <?php if ( $st1 == 10 ) echo 'selected' ; ?> >1:00</option>
							<option value="11" <?php if ( $st1 == 11 ) echo 'selected' ; ?> >1:30</option>
							<option value="12" <?php if ( $st1 == 12 ) echo 'selected' ; ?> >2:00</option>
							<option value="13" <?php if ( $st1 == 13 ) echo 'selected' ; ?> >2:30</option>
							<option value="14" <?php if ( $st1 == 14 ) echo 'selected' ; ?> >3:00</option>
							<option value="15" <?php if ( $st1 == 15 ) echo 'selected' ; ?> >3:30</option>
							<option value="16" <?php if ( $st1 == 16 ) echo 'selected' ; ?> >4:00</option>
							<option value="17" <?php if ( $st1 == 17 ) echo 'selected' ; ?> >4:30</option>
							<option value="18" <?php if ( $st1 == 18 ) echo 'selected' ; ?> >5:00</option>
							<option value="19" <?php if ( $st1 == 19 ) echo 'selected' ; ?> >5:30</option>
							<option value="20" <?php if ( $st1 == 20 ) echo 'selected' ; ?> >6:00</option>
							<option value="21" <?php if ( $st1 == 21 ) echo 'selected' ; ?> >6:30</option>
							<option value="22" <?php if ( $st1 == 22 ) echo 'selected' ; ?> >7:00</option>
							<option value="23" <?php if ( $st1 == 23 ) echo 'selected' ; ?> >7:30</option>
							<option value="24" <?php if ( $st1 == 24 ) echo 'selected' ; ?> >8:00</option>
						</select>
						to <select name="end-time">
							<option value="0" <?php if ( $et1 == 0 ) echo 'selected' ; ?> >8:00</option>
							<option value="1" <?php if ( $et1 == 1 ) echo 'selected' ; ?> >8:30</option>
							<option value="2" <?php if ( $et1 == 2 ) echo 'selected' ; ?> >9:00</option>
							<option value="3" <?php if ( $et1 == 3 ) echo 'selected' ; ?> >9:30</option>
							<option value="4" <?php if ( $et1 == 4 ) echo 'selected' ; ?> >10:00</option>
							<option value="5" <?php if ( $et1 == 5 ) echo 'selected' ; ?> >10:30</option>
							<option value="6" <?php if ( $et1 == 6 ) echo 'selected' ; ?> >11:00</option>
							<option value="7" <?php if ( $et1 == 7 ) echo 'selected' ; ?> >11:30</option>
							<option value="8" <?php if ( $et1 == 8 ) echo 'selected' ; ?> >12:00</option>
							<option value="9" <?php if ( $et1 == 9 ) echo 'selected' ; ?> >12:30</option>
							<option value="10" <?php if ( $et1 == 10 ) echo 'selected' ; ?> >1:00</option>
							<option value="11" <?php if ( $et1 == 11 ) echo 'selected' ; ?> >1:30</option>
							<option value="12" <?php if ( $et1 == 12 ) echo 'selected' ; ?> >2:00</option>
							<option value="13" <?php if ( $et1 == 13 ) echo 'selected' ; ?> >2:30</option>
							<option value="14" <?php if ( $et1 == 14 ) echo 'selected' ; ?> >3:00</option>
							<option value="15" <?php if ( $et1 == 15 ) echo 'selected' ; ?> >3:30</option>
							<option value="16" <?php if ( $et1 == 16 ) echo 'selected' ; ?> >4:00</option>
							<option value="17" <?php if ( $et1 == 17 ) echo 'selected' ; ?> >4:30</option>
							<option value="18" <?php if ( $et1 == 18 ) echo 'selected' ; ?> >5:00</option>
							<option value="19" <?php if ( $et1 == 19 ) echo 'selected' ; ?> >5:30</option>
							<option value="20" <?php if ( $et1 == 20 ) echo 'selected' ; ?> >6:00</option>
							<option value="21" <?php if ( $et1 == 21 ) echo 'selected' ; ?> >6:30</option>
							<option value="22" <?php if ( $et1 == 22 ) echo 'selected' ; ?> >7:00</option>
							<option value="23" <?php if ( $et1 == 23 ) echo 'selected' ; ?> >7:30</option>
							<option value="24" <?php if ( $et1 == 24 ) echo 'selected' ; ?> >8:00</option>
						</select>
						<br>
						Class Type:
						<input type="radio" id="lab" name="class-type" value="0" <?php if ( $ct1 == 0 ) echo 'checked = "true"' ; ?> >
						<label for="lab">Lab</label>
						<input type="radio" id="theory" name="class-type" value="1" <?php if ( $ct1 == 1 ) echo 'checked = "true"' ; ?> >
						<label for="theory">Theory</label>
						<br>
						Room: <input type="text" name="room" placeholder="i.e. 1115/D0203" required="" value="<?php echo $room1; ?>">
					</div>
					<input type="hidden" name="sectionId" value="<?php echo $sectionId;?>">
					<input type="hidden" name="sectionName" value="<?php echo $sectionName;?>">
					<div class="buttons">
						<input type="submit" name="submit" value="Create" class="save-button">
					</div>
				</form>
			</div>
		</div>
	</body>
	</html>

	<?php oci_close($conn);?>