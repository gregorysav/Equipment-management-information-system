<?php
//Access: Administrator
include("variables_file.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "add") {
	if ($type == 1 OR $type == 2 OR $type == 3){


		if(isset($_POST['add'])){
		
			$name_dep = filter_var($_POST['name_dep'],FILTER_SANITIZE_STRING);
			$telephone_dep = filter_var($_POST['telephone_dep'],FILTER_SANITIZE_NUMBER_FLOAT);
			
			try{

				$newDepartmentSQL = "INSERT INTO department_svds (name_dep, telephone_dep) VALUES (:name_dep, :telephone_dep)";
				$newDepartmentSTMT = $db->prepare($newDepartmentSQL);
				$newDepartmentSTMT->bindParam(':name_dep', $name_dep);
				$newDepartmentSTMT->bindParam(':telephone_dep', $telephone_dep);
				if ($newDepartmentSTMT->execute()) {
					echo '<p class="alert alert-success">Επιτυχής καταχώρηση αποτελεσμάτων<br></p>';
			   		header("Location: departments.php");
			   		die();	
				}else {
					echo '<p class="alert alert-warning">Η καταχώρηση αποτελεσμάτων δεν ήταν επιτυχής<br></p>';
			   		header("Location: departments.php");
			   		die();
				}		

			   			    
			}
			catch(PDOException $e)
			{
			    echo "Error: " . $e->getMessage();
			} 

		}

		echo '
			<div class="container col-md-4">
				<form method="post">
			        <h4>Πληροφορίες Νέου Τμήματος</h4>
			        <div class="form-group">	
						<label for="name_p">Όνομα Τμήματος:</label>
						<input type="text" class="form-control" id="name_p" name="name_dep" required>

						<label for="telephone_p">Τηλέφωνο Τμήματος:</label>
						<input type="text" class="form-control" id="telephone_p" name="telephone_dep" required>
					</div>
				<button id="add" name ="add" type="submit" class="btn btn-primary">Προσθήκη Τμήματος</button>
				</form>
			</div> 
		';

		
	}else{
		header("Location: departments.php");
		die("Δεν δόθηκε σωστό ID τμήματος.");
	}
 
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "delete") {
	if (isset($_GET['id_dep'])){
		$idToDelete= filter_var($_GET['id_dep'],FILTER_SANITIZE_NUMBER_FLOAT);

		$departmenyCheckQuerySQL = "SELECT * FROM equip_svds WHERE department= :idToDelete";
	 	$departmenyCheckQuerySTMT = $db->prepare($departmenyCheckQuerySQL);
	 	$departmenyCheckQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	 	$departmenyCheckQuerySTMT->execute();
	 	if ($departmenyCheckQuerySTMT->rowCount() > 0){
	 		$_SESSION['unableToDeleteDepartment']='<p class="alert alert-warning unableToDeleteDepartment">Το τμήμα δεν μπορεί να διαγραφεί γιατί χρησιμοποιείτε για κάποιο εξάρτημα.<br></p>';
	 		header("Location: departments.php");
	 	} else {
			$deleteQueryBorrowSQL = "DELETE  FROM department_svds WHERE id_dep= :idToDelete";
			$deleteQueryBorrowSTMT = $db->prepare($deleteQueryBorrowSQL);
			$deleteQueryBorrowSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
			$deleteQueryBorrowSTMT->execute();
			
			header("Location: departments.php");
			die();
		}		
	}else{
		header("Location: departments.php");
		die("Δεν δόθηκε σωστό ID τμήματος.");
	}
}


if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "update") {
	if (isset($_GET['id_dep'])){
		$idToChange= filter_var($_GET['id_dep'],FILTER_SANITIZE_NUMBER_FLOAT);
		$departmentChangeQuerySQL = "SELECT * FROM department_svds WHERE id_dep= :idToChange";
	 	$departmentChangeQuerySTMT = $db->prepare($departmentChangeQuerySQL);
	 	$departmentChangeQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
	 	$departmentChangeQuerySTMT->execute();
	 	while($departmentChangeQuerySTMTResult=$departmentChangeQuerySTMT->fetch(PDO::FETCH_ASSOC)){

			echo '
				<div class="container col-md-4">
					<form method="post">
				        <h4>Πληροφορίες Τμήματος</h4>
				        <div class="form-group">	
							<label for="name_p">Όνομα Τμήματος:</label>
							<input type="text" class="form-control" id="name_p" name="name_dep" value="'.$departmentChangeQuerySTMTResult['name_dep'].'" required>

							<label for="telephone_p">Τηλέφωνο Τμήματος:</label>
							<input type="text" class="form-control" id="telephone_p" name="telephone_dep" value="'.$departmentChangeQuerySTMTResult['telephone_dep'].'" required>
						</div>
					<button id="add" name ="add" type="submit" class="btn btn-primary">Ανανέωση Πληροφορίων</button>
					</form>
				</div> <br><br><br>

			';
		}	
		if(isset($_POST['add'])){
		
			$name_dep = filter_var($_POST['name_dep'],FILTER_SANITIZE_STRING);
			$telephone_dep = filter_var($_POST['telephone_dep'],FILTER_SANITIZE_NUMBER_FLOAT);
			
			try{

				$departmentUpdateSQL = "UPDATE department_svds SET name_dep= :name_dep, telephone_dep= :telephone_dep WHERE id_dep= :idToChange";
				$departmentUpdateSTMT = $db->prepare($departmentUpdateSQL);
				$departmentUpdateSTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
				$departmentUpdateSTMT->bindParam(':name_dep', $name_dep);
				$departmentUpdateSTMT->bindParam(':telephone_dep', $telephone_dep);
				if ($departmentUpdateSTMT->execute()){
					echo '<p class="alert alert-success">Επιτυχής καταχώρηση αποτελεσμάτων<br></p>';
			  	 	echo '<meta http-equiv="refresh" content="0; URL=departments.php">';	
				}else {
					echo '<p class="alert alert-warning">Η καταχώρηση αποτελεσμάτων δεν έγινε με επιτυχία<br></p>';
			  	 	echo '<meta http-equiv="refresh" content="0; URL=departments.php">';
				}		

			   			    
			}
			catch(PDOException $e)
			{
			    echo "Error: " . $e->getMessage();
			} 

		}
	}else{
		header("Location: departments.php");
		die("Δεν δόθηκε σωστό ID τμήματος.");
	}
}	
include("views/footer.php");
echo '
	</body>
	</html>
';
?>  