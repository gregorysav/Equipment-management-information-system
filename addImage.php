<?php
session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	


	if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
	}
	if (!isset($_SESSION['email'])){

		header("Location: index.php");
	}	
	
	$idToChange=$_GET['id_equip'];	
	
	


include("views/footer.php");

?>




<!DOCTYPE html>
<html>
<body>

<form action="upload.php?id_equip=<?php echo $idToChange; ?>" method="post" enctype="multipart/form-data">
    Επιλέξτε εικόνα για ανέβασμα:
    <input type="file" name="filename" id="filename"><br>
    <input type="submit" id="imageUpload" value="Ανεβάστε Εικόνα" name="submit">
</form>

</body>
</html>

