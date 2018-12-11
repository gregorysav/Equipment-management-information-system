<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	$idToDelete= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
	

	$deleteQueryBorrowSQL = "DELETE  FROM borrow_svds WHERE id_equip_borrow=  :idToDelete";
	$deleteQueryBorrowSTMT = $db->prepare($deleteQueryBorrowSQL);
	$deleteQueryBorrowSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQueryBorrowSTMT->execute();
	
	$deleteQueryEquipSQL = "DELETE  FROM equip_svds WHERE id_equip=  :idToDelete";
	$deleteQueryEquipSTMT = $db->prepare($deleteQueryEquipSQL);
	$deleteQueryEquipSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQueryEquipSTMT->execute();
	
	header("Location: equipment_manage.php");
	die();	
	
	
include("views/footer.php");
?>
