<?php
include("views/connection.php");
include("variables_file.php");


	// $activeBorrowSQL = "SELECT * FROM borrow_svds";
	// $activeBorrowSTMT = $db->prepare($activeBorrowSQL); 
	// $activeBorrowSTMT->execute();
	// echo '<p>Αυτή τη στιγμή υπάρχουν '.$activeBorrowSTMT->rowCount() .' ενεγοί δανεισμοί εκ των οποίων.</p><ol>';
	
	// while($activeBorrowSTMTResult=$activeBorrowSTMT->fetch(PDO::FETCH_ASSOC)){
	// 	if ($activeBorrowSTMTResult['notify10'] == 0){
	// 		$notActiveBorrowSQL = "SELECT * FROM users_svds WHERE aem = :borrower";
	// 		$notActiveBorrowSTMT = $db->prepare($notActiveBorrowSQL);
	// 		$notActiveBorrowSTMT->bindParam(':borrower', $activeBorrowSTMTResult['aem_borrow'], PDO::PARAM_INT);
	// 		$notActiveBorrowSTMT->execute();
	// 		while ($notActiveBorrowSTMTResult=$notActiveBorrowSTMT->fetch(PDO::FETCH_ASSOC)) {
	// 			echo '<li>Για τον δανεισμό με id '.$activeBorrowSTMTResult['id_borrow'].' έχει σταλθεί στον '.$notActiveBorrowSTMTResult['last_name'] .' '.$notActiveBorrowSTMTResult['first_name'] .' (ΑΕΜ = '.$notActiveBorrowSTMTResult['aem'] .') προειδοποίηση για το πέρας του χρόνου δανεισμού. </li>';
	// 		}	
	// 	}
	// }

	// echo '</ol>';


	$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip INNER JOIN users_svds on borrow_svds.aem_borrow = users_svds.id"; 
	$baseQuerySTMT = $db->prepare($baseQuerySQL);
	$baseQuerySTMT->execute();
	echo '<p>Αυτή τη στιγμή υπάρχουν '.$baseQuerySTMT->rowCount() .' ενεγοί δανεισμοί εκ των οποίων.</p><ol>
		<table>
		<tr>
    		<th>Ονοματεπώνυμο</th>
    		<th>Εξάρτημα</th>
    		<th>Ημερ. Έναρξης</th>
    		<th>Περιθώριο</th>
    		<th>Επιβεβαιώθηκε</th>
  		</tr>
	';	

	while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		echo '
			<tr>
	    	<td>'.$baseQuerySTMTResult['last_name'] .' '.$baseQuerySTMTResult['first_name'] .'</td>
	    	<td>'.$baseQuerySTMTResult['name_e'] .'</td>
	    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).'</td>
	    	<td>'.$baseQuerySTMTResult['notify10'] .' ημέρες</td>
	    ';
	    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
	    	echo '<td>Επιβεβαιώθηκε</td>';
	  	}else{
	  		echo '<td>Δεν επιβεβαιώθηκε</td>';
	  	}
	  	echo '	  	
	  		</tr>

		';	
	}
	echo '
		</table>
	';

?>