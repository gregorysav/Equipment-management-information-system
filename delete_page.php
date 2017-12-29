<?php
	session_start();
	include("connection.php");
	include("header.php");
	include("navbar.php");

	
	$deleteQuery = "DELETE FROM equip_svds WHERE id_equip=  :id_equip";
	$stmt = $db->prepare($deleteQuery);
	$stmt->bindParam(':id_equip', $_GET['id_equip'], PDO::PARAM_INT);   
	$stmt->execute();
 	header("Location: equipment_manage.php");


	include("footer.php");

?>
