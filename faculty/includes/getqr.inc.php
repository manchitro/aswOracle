<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['classId'])){
		require '../../includes/oracleConn.php';

		$sql = "SELECT classes.Id, sections.sectionName, classes.classDate from classes, sections where classes.Id = :cid AND classes.SectionId = sections.Id";
		$stmt = oci_parse($conn, $sql);
		if (!$stmt) {
			echo '<p class="error-msg">Error retrieving data</p>';
		}
		else{
			oci_bind_by_name($stmt, ':cid', $_POST['classId']);
			oci_execute($stmt);
			if (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				if(empty($row['QRCODE']) == 1){
					$sql2 = "UPDATE classes SET QRCode = :qrcode WHERE Id = :cid";
					$stmt2 = oci_parse($conn, $sql2);
					if (!$stmt2) {
						echo '<p class="error-msg">Error retrieving data</p>';
					}
					else{
						$newQR = $row['ID'].",".$row['SECTIONNAME'].",".$row['CLASSDATE'];
						$newEncodedQR = base64_encode($newQR);
						oci_bind_by_name($stmt2, ':qrcode', $newEncodedQR);
						oci_bind_by_name($stmt2, ':cid', $_POST['classId']);
						oci_execute($stmt2);

						echo $newEncodedQR;
					}
				}
				else{
					echo $row['QRCODE'];
				}
			}
			else{
				echo "no qr found";
			}
		}
	}
	else{
		echo "Get QR failed: no post";
	}
}
else{
	echo "Get QR failed: no session";
}

oci_close($conn);