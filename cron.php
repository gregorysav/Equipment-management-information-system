<?php
//Access: Administrator
include("views/connection.php");
include("function_cron.php");
$one = 1;
	date_default_timezone_set('Europe/Athens');
	$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds ON borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE history_flag= :history_flag"; 
	$baseQuerySTMT = $db->prepare($baseQuerySQL);
	$baseQuerySTMT->bindParam(':history_flag', $one, PDO::PARAM_INT);
	$baseQuerySTMT->execute();
	if ($baseQuerySTMT->rowCount() == 0){
	echo "Η λίστα δανεισμών είναι κενή.";
	}else {
	echo '-Συνολικοί Ενεργοί Δανεισμοί :  '.$baseQuerySTMT->rowCount() .' <br>
		  -Τωρινή Ημερομηνία : '.date("d/m/Y h:i:s").'<br>
	';
	while ($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		$nameQuerySQL = "SELECT * FROM users_svds WHERE id= :borrowUser"; 
		$nameQuerySTMT = $db->prepare($nameQuerySQL);
		$nameQuerySTMT->bindParam(':borrowUser', $baseQuerySTMTResult['id_user_borrow']);
		$nameQuerySTMT->execute();
		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$last_name= $nameQuerySTMTResult['last_name'];
			$first_name= $nameQuerySTMTResult['first_name'];
			$aem = $nameQuerySTMTResult['aem'];
			$telephone= $nameQuerySTMTResult['telephone'];
			$email= $nameQuerySTMTResult['email'];
		}	
		$borrowerFullName = $last_name.' '.$first_name; 
		adminDisplayInformation($baseQuerySTMTResult['id_borrow'], $aem, $borrowerFullName, $email, $baseQuerySTMTResult['expire_date'], $baseQuerySTMTResult['notify30'], $baseQuerySTMTResult['notify20'], $baseQuerySTMTResult['notify10']);
	}
}		
	
?>