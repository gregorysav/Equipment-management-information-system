<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
		
	$idToChange=$_GET['id_equip'];	

	$imageToDisplaySQL = "SELECT * FROM equip_svds WHERE id_equip = :id_equip";
	$imageToDisplaySTMT = $db->prepare($imageToDisplaySQL);
	$imageToDisplaySTMT->bindParam(':id_equip', $idToChange, PDO::PARAM_INT);
	$imageToDisplaySTMT->execute();
	while($imageToDisplaySTMTResult=$imageToDisplaySTMT->fetch(PDO::FETCH_ASSOC)){
	 	$imageRealName = $imageToDisplaySTMTResult['real_filename'];
	 	if (!$imageToDisplaySTMTResult['hash_filename']){
	 		$imageHashedName = "noimage.jpg";	
	 	}else {
	 		$imageHashedName = $imageToDisplaySTMTResult['hash_filename'];
	 	}
	 	
	}

echo '
	<div class="container">
		<h3>Υπάρχουσα φωτογραφία</h3>
			<img src="uploadedImages/'.$imageHashedName.'"/>
			<form action="upload.php?id_equip='.$idToChange.'" method="post" enctype="multipart/form-data">
			    <input type="file" name="filename" id="filename"><br>
		    <input type="submit" id="imageUpload" class="btn btn-primary" value="Ανεβάστε Εικόνα" name="submit">
			</form>
			<button type="submit" id="imageDelete" class="btn btn-primary btn-danger" id_equip='.$idToChange.' image_name='.$imageRealName.'>Διαγραφή Εικόνας</button>		
	</div>	
';

include("views/footer.php");
?>