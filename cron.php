<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("functions.php");

	if ($type == 1){
		echo '<div class="container">';	
			$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip INNER JOIN users_svds on borrow_svds.aem_borrow = users_svds.id"; 
			$baseQuerySTMT = $db->prepare($baseQuerySQL);
			$baseQuerySTMT->execute();
			if ($baseQuerySTMT->rowCount() == 0){
				echo "Η λίστα δανεισμών είναι κενή.";
			}else {
				echo '<p>-Συνολικοί Ενεργοί Δανεισμοί :  '.$baseQuerySTMT->rowCount() .' </p>
					  <p>-Τωρινή Ημερομηνία : '.date("d/m/Y").'</p>
				';

				while ($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					adminDisplayInformation($baseQuerySTMTResult['id_borrow'], $baseQuerySTMTResult['aem_borrow'], $baseQuerySTMTResult['email'], $baseQuerySTMTResult['expire_date']);
				}
			}		
				
	}else {
		header("Location: index.php");
		die("Δεν έχετε συνδεθεί");
	}
	
?>