<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");

	
	$idToDelete=$_GET['id_borrow'];
	$deleteQuery = "DELETE  FROM borrow_svds WHERE id_borrow=  $idToDelete";
	$deleteQuery_stmt = $db->prepare($deleteQuery);
	$deleteQuery_stmt->bindParam(':id_borrow', $idToDelete, PDO::PARAM_INT);
	$deleteQuery_stmt->execute();

	header("Location: active.php");


	include("views/footer.php");

?>
