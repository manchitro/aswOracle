<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<div class="today-title">
	<p class="todays-label">Today's classes</p>
</div>
<div class="todays-classes">
	<?php
	require '../../includes/oracleConn.php';
	include 'values.php';

	$sql = "SELECT * FROM classes WHERE sectionId IN (SELECT Id from sections where facultyId = :fid) AND ClassDate = :classDate";
	$stmt = oci_parse($conn, $sql);
	if (!$stmt) {
		echo '<p class="error-msg">Error retrieving data</p>';
	}
	else{
		$today = date("d-m-Y");
		oci_bind_by_name($stmt, ':fid', $_SESSION['userId']);
		oci_bind_by_name($stmt, ':classDate', $today);
		oci_execute($stmt);
		$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
		if($nrows == 0){
			echo '<p class="no-class">No classes today</p>';
		}
		else{
			$index = 1;
			oci_execute($stmt);
			while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				$sql2 = "SELECT SectionName FROM sections WHERE Id = :sid";
				$stmt2 = oci_parse($conn, $sql2);
				if (!$stmt2) {
					echo '<p class="error-msg">Error retrieving data</p>';
				}
				else{
					oci_bind_by_name($stmt2, ':sid', $row['SECTIONID']);
					oci_execute($stmt2);
					$row2 = oci_fetch_array($stmt2, OCI_ASSOC);

					if($row2){
						if($index%2==0) echo '<div class="today-class-box">'; else echo '<div class="today-class-box-even">';
						echo '<table class="today-class-table">';
						echo '<tr><td><p class="section-name">'.$row2['SECTIONNAME'].'</p></td><td><p class="class-room">at '.$row['ROOMNO'].'</p></td></tr>';
						echo '<tr><td><p class="class-time">'.$classtime[$row['STARTTIMEID']].' - '.$classtime[$row['ENDTIMEID']].' ['.$classtype[$row['CLASSTYPE']].']'.'</p></td><td class=qr-td><button class="qr-button" id="'.$row['ID'].'"><img src="../../images/qr.png"></button></td></tr>';
						echo '</table>';
						echo '</div>';
					}
					else{
						echo '<p class="error-msg">Error retrieving data</p>';
					}
				}
				$index++;
			}
		}
	}

	?>
	<script type="text/javascript" src="../js/qrdisplay.js"></script>
	<script type="text/javascript" src="../js/qrcode.min.js"></script>
	

</div>
<div id="qrcode"></div>