<?php
if(isset($_POST['login-submit']))
{
	$uid = $_POST['email'];
	$password = $_POST['password'];



	//uid matches faculty
	if (preg_match("/^\d{4}-\d{4}-[1-3]$/", $uid) || preg_match("/^[a-zA-Z0-9\.]+@aiub\.edu$/", $uid)) {
		require 'oracleConn.php';
		$sql ="SELECT * FROM users WHERE Email=:email OR AcademicId=:aid";
		$stuser = oci_parse($conn, $sql);

		if (!$stuser) {
			$e = oci_error($conn);
			trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
			header("Location: ../login.php?error=sqlerror");
			exit();
		}
		else{
			oci_bind_by_name($stuser, ':email', $uid);
			oci_bind_by_name($stuser, ':aid', $uid);
			oci_execute($stuser);
			$row = oci_fetch_array($stuser, OCI_ASSOC);
			if ($row) {
				if ($row['PASSWORD'] != $password) {
					header("Location: ../login.php?error=badcredp1");
					print_r($row);
					exit();
				}
				elseif ($row['PASSWORD'] == $password) {
					session_start();
					$_SESSION['userId'] = $row['ID'];
					$_SESSION['userAcademicId'] = $row['ACADEMICID'];
					$_SESSION['userFirstName'] = $row['FIRSTNAME'];
					$_SESSION['userLastName'] = $row['LASTNAME'];
					$_SESSION['userEmail'] = $row['EMAIL'];
					$_SESSION['userType'] = $row['USERTYPE'];
					$_SESSION['userCreatedAt'] = $row['CREATEDAT'];

					header("Location: ../faculty/sections/sections.php");
				}
				else{
					header("Location: ../login.php?error=badcredp2");
					exit();
				}
			}
			else{
				header("Location: ../login.php?error=badcredu");
				exit();
			}
		}
	}
	//uid matches student
	elseif (preg_match("/^\d{2}-\d{5}-[1-3]$/", $uid)) {
		include_once('../libs/simple_html_dom.php');
		//building post string
		$data = array("UserName"=>$uid, "Password"=>$password);
		$postString = http_build_query($data);
		$user_agent = "Mozilla/5.0 (X11; Linux i686; rv:24.0) Gecko/20140319 Firefox/24.0 Iceweasel/24.4.0";

		//curl to post to portal.aiub.edu
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'https://portal.aiub.edu/');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		CURL_SETOPT($curl, CURLOPT_RETURNTRANSFER,True);
		CURL_SETOPT($curl, CURLOPT_FOLLOWLOCATION,True);
		CURL_SETOPT($curl, CURLOPT_POST,True);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);
		CURL_SETOPT($curl, CURLOPT_COOKIEFILE,"cookie.txt");
		CURL_SETOPT($curl, CURLOPT_COOKIEJAR,"cookie.txt");
		CURL_SETOPT($curl, CURLOPT_CONNECTTIMEOUT,30);
		CURL_SETOPT($curl, CURLOPT_TIMEOUT,30);

		$result = curl_exec($curl);

		if(!empty($result)){
			if((strpos($result, 'Academics')!==false) && (strpos($result, 'Grade Reports')!==false)){
				try{
					$html = str_get_html($result);
					$name = $html->find('a[href=/Student/Home/Profile] small text',0);

					if($name == 'Array'){
						$name = 'Student';
					}
				}catch(Exception $e){
					$name = "Student";
				}

				session_start();
				$_SESSION['userAcademicId'] = $uid;
				$_SESSION['userFullName'] = (string)$name;
				//echo $_SESSION['userAcademicId']." ".$_SESSION['userFullName'];
				header("Location: ../student/dashboard.php");
				exit();
			}
			else{
				header("Location: ../login.php?error=vuesvalfailed");
				exit();
			}
		}
		else{
			header("Location: ../login.php?error=vuesvalfailed");
			exit();
		}

	}
	//uid matches admin
	elseif ($uid == 'admin') {
		require 'oracleConn.php';
		$sql ="SELECT * FROM users WHERE Email=:email";
		$stuser = oci_parse($conn, $sql);

		if (!$stuser) {
			$e = oci_error($conn);
			trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
			header("Location: ../login.php?error=sqlerror");
			exit();
		}
		else{
			oci_bind_by_name($stuser, ':email', $uid);
			oci_execute($stuser);
			$row = oci_fetch_array($stuser, OCI_ASSOC);
			if ($row) {
				if ($row['PASSWORD'] != $password) {
					header("Location: ../login.php?error=badcredap1");
					print_r($row);
					exit();
				}
				elseif ($row['PASSWORD'] == $password) {
					session_start();
					$_SESSION['userId'] = $row['ID'];
					$_SESSION['userAcademicId'] = $row['ACADEMICID'];
					$_SESSION['userFirstName'] = $row['FIRSTNAME'];
					$_SESSION['userLastName'] = $row['LASTNAME'];
					$_SESSION['userEmail'] = $row['EMAIL'];
					$_SESSION['userType'] = $row['USERTYPE'];
					$_SESSION['userCreatedAt'] = $row['CREATEDAT'];

					header("Location: ../admin/profile.php");
				}
				else{
					header("Location: ../login.php?error=badcredap2");
					exit();
				}
			}
			else{
				header("Location: ../login.php?error=badcredau");
				exit();
			}
		}
	}
	// uid matches nothing
	else{
		header("Location: ../login.php?error=invalidlogin");
		exit();
	}
}
else{
	header("Location: ../login.php?error=directaccess");
	exit();
}