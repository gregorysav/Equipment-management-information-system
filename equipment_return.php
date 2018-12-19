<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	if ($type == 1){
		echo '<div class="container">';	
			$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip INNER JOIN users_svds on borrow_svds.aem_borrow = users_svds.id"; 
			$baseQuerySTMT = $db->prepare($baseQuerySQL);
			$baseQuerySTMT->execute();
			if ($baseQuerySTMT->rowCount() == 0){
				echo "Η λίστα δανεισμών είναι κενή.";
			} else {
				echo '<p>Αυτή τη στιγμή υπάρχουν '.$baseQuerySTMT->rowCount() .' ενεγοί δανεισμοί.<a href=new_borrow.php><button>  Επιστροφή</button></a></p>
					<table border="1"></p>
					<tr>
			    		<th>Ονοματεπώνυμο</th>
			    		<th>ΑΕΜ</th>
			    		<th>Εξάρτημα</th>
			    		<th>Ημερ. Έναρξης</th>
			    		<th>Περιθώριο</th>
			    		<th>Επιβεβαιώθηκε</th>
			    		<th>Αιτιολόγηση</th>
			    		<th>Επιστροφή Εξοπλισμού</th>
			  		</tr>
				';	

				while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					echo '
						<tr>
				    	<td>'.$baseQuerySTMTResult['last_name'] .' '.$baseQuerySTMTResult['first_name'] .'</td>
				    	<td>'.$baseQuerySTMTResult['aem'] .'</td>
				    	<td>'.$baseQuerySTMTResult['name_e'] .'</td>
				    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).'</td>
				    	<td>'.$baseQuerySTMTResult['notify10'] .' ημέρες</td>
				    ';
				    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
				    	echo '<td>Επιβεβαιώθηκε</td>';
				  	}else{
				  		echo '<td><a href=confirmation.php>Δεν επιβεβαιώθηκε</a></td>';
				  	}
				  	if ($baseQuerySTMTResult['borrow_reason'] != NULL){
				  		echo '	 
				  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
				  	 	';	
				  	}else{
				  		echo '	 
				  			<td>Δεν υπάρχει λόγος δανειμού.</td>
				  	 	';
				  	}
				  	echo '
				  	 	<td><button type="submit" class="returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark">Λήξη δανειμού</a></button></td>
				  		</tr>
					';	
				}
				echo '
					</table>
				';
				include("views/footer.php");
			}	
			echo '</div>';
	}else {
		header("Location: index.php");
		die("Δεν έχετε συνδεθεί");
	}
	
?>