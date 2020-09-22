<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['attId']) && isset($_POST['attEntry'])){
		$attId = $_POST['attId'];
		$attEntry = $_POST['attEntry'];

		require '../../../includes/oracleConn.php';

		$sql = "UPDATE attendances SET Entry = :entry WHERE Id = :attid";
		$stmt = oci_parse($conn, $sql);
		if (!$stmt) {
			echo "Attendance update failed: sqlerror";
			exit();
		}
		else{
			oci_bind_by_name($stmt, ':entry', $attEntry);
			oci_bind_by_name($stmt, ':attid', $attId);
			oci_execute($stmt);

			echo "changed: ".$attId." to ".$attEntry;
			exit();
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