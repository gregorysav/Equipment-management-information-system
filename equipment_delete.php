<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");

	
	$idToDelete=$_GET['id_equip'];
	$deleteQuery = "DELETE  FROM equip_svds WHERE id_equip=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_equip', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();

	$deleteQuery = "DELETE  FROM department_svds WHERE id_dep=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_dep', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();
 	
 	$deleteQuery = "DELETE  FROM provider_svds WHERE id_p=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_p', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();

	$deleteQuery = "DELETE  FROM description_svds WHERE id_desc=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_desc', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();

	$deleteQuery = "DELETE  FROM comments_svds WHERE id_comment=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_comment', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();

	header("Location: equipment_manage.php");


	include("views/footer.php");

?>
