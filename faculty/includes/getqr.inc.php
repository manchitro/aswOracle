<?php
session_start();
if (isset($_SESSION['userId']) && $_SESSION['userId']!== "") {
	if(isset($_POST['classId'])){
		require '../../includes/dbh.inc.php';

		$sql = "SELECT classes.Id, sections.sectionName, classes.classDate from classes, sections where classes.Id = ? AND classes.SectionId = sections.Id";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			echo '<p class="error-msg">Error retrieving data</p>';
		}
		else{
			mysqli_stmt_bind_param($stmt, "s", $_POST['classId']);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $classId, $sectionName, $classDate);

			if (mysqli_stmt_fetch($stmt)) {
				if(empty($classQRCode) == 1){
					$sql2 = "UPDATE classes SET QRCode = ? WHERE Id = ?;";
					$stmt2 = mysqli_stmt_init($conn);
					if (!mysqli_stmt_prepare($stmt2, $sql2)) {
						echo '<p class="error-msg">Error retrieving data</p>';
					}
					else{
						$newQR = $classId.",".$sectionName.",".$classDate;
						$newEncodedQR = base64_encode($newQR);
						mysqli_stmt_bind_param($stmt2, "ss", $newEncodedQR, $classId);
						mysqli_stmt_execute($stmt2);

						echo $newEncodedQR;
					}
				}
				else{
					echo $classQRCode;
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