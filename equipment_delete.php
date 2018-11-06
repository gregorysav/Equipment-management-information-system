<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");

	
	$idToDelete=$_GET['id_equip'];
	$deleteQuery = "DELETE  FROM borrow_svds WHERE id_equip_borrow=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_equip_borrow', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();
	
	$deleteQuery = "DELETE  FROM equip_svds WHERE id_equip=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_equip', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();

	
 	

	header("Location: equipment_manage.php");


	include("views/footer.php");

?>
