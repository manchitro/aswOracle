<?php
require '../includes/oracleConn.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['qbox']) ) {
  $getQuery = $_POST['qbox'];

  $sql = $getQuery;
  $stmt = oci_parse($conn, $sql);

  if (!$stmt) {
		$e = oci_error($conn);
			trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR); 
			//header("Location: profile.php?error=sqlerror");
			exit();
	}else{
    $r = oci_execute($stmt);

    if(!$r) {    
    $e = oci_error($s);
  	trigger_error('Could not execute statement:'. $e['message'], E_USER_ERROR); 
    }
    else{
      if (oci_num_fields($stmt) != 0) {
        echo "<table border='1'>\n"; $ncols = oci_num_fields($stmt); 
        echo "<tr>\n"; 
        for ($i = 1; $i <= $ncols; ++$i) {
          $colname = oci_field_name($stmt, $i);    
          echo "  <th><b>".htmlentities($colname, ENT_QUOTES)."</b></th>\n"; 
        } 
        echo "</tr>\n"; 
        while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) 
        {    
          echo "<tr>\n";
          foreach ($row as $item) 
          {        
            echo "<td>".($item!==null?htmlentities($item, ENT_QUOTES):"&nbsp;")."</td>\n"; 
          } 
          echo "</tr>\n"; 
        }
        echo "</table>\n";
      }
      else{
        echo "Query succeeded";
      }
    }
  }
} else {
  echo "Write Query !!";
}

?>