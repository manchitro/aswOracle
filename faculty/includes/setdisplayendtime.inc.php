<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['classId'])){
		require '../../includes/oracleConn.php';
		
		$sql = "SELECT * FROM classes WHERE Id = :cid";
		$stmt = oci_parse($conn, $sql);
		if (!$stmt) {
			echo '<p class="error-msg">Error retrieving data</p>';
		}
		else{
			oci_bind_by_name($stmt, ':cid', $_POST['classId']);
			oci_execute($stmt);
			if (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				
				$sql2 = "UPDATE classes SET QRCodeDisplayEndTime = current_timestamp(2) WHERE Id = :cid";
				$stmt2 = oci_parse($conn, $sql2);
				if (!$stmt2) {
					echo '<p class="error-msg">Error retrieving data</p>';
				}
				else{
					oci_bind_by_name($stmt2, ':cid', $_POST['classId']);
					oci_execute($stmt2);
					
					//echo date("Y-m-d H:i:s");
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