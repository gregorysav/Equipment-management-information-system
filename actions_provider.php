<?php
//Access: Administrator
include("variables_file.php");
include("checkUser.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "add") {
//  Η μεταβλητή $type έχει τεθεί από το $_SESSION['type'] και ελέγχει το επίπεδο δικαιωμάτων του συνδεδεμένου χρήστη	
	if ($type == 1 OR $type == 2 OR $type == 3){


		if(isset($_POST['add'])){
		
			$name_p = filter_var($_POST['name_p'],FILTER_SANITIZE_STRING);
			$telephone_p = filter_var($_POST['telephone_p'],FILTER_SANITIZE_NUMBER_FLOAT);
			$comments_p = filter_var($_POST['comments_p'],FILTER_SANITIZE_STRING);
			$website_p = filter_var($_POST['website_p'],FILTER_SANITIZE_STRING);
			$support_p = filter_var($_POST['support_p'],FILTER_SANITIZE_STRING);
			
			try{

				$newProviderSQL = "INSERT INTO provider_svds (name_p, telephone_p, website_p, email_p, support_p, comments_p) 
    VALUES (:name_p, :telephone_p, :website_p, :email_p, :support_p, :comments_p)";
				$newProviderSTMT = $db->prepare($newProviderSQL);
				$newProviderSTMT->bindParam(':name_p', $name_p);
				$newProviderSTMT->bindParam(':telephone_p', $telephone_p);
				$newProviderSTMT->bindParam(':website_p', $website_p);
				$newProviderSTMT->bindParam(':email_p', $_POST['email_p']);
				$newProviderSTMT->bindParam(':support_p', $support_p);
				$newProviderSTMT->bindParam(':comments_p', $comments_p);
				if ($newProviderSTMT->execute()) {
					echo '<p class="alert alert-success">Επιτυχής καταχώρηση αποτελεσμάτων<br></p>';
				   	header("Location: provider.php");
				   	die();	
				}else {
					echo '<p class="alert alert-warning">Παρουσιάστηκε πρόβλημα κατά την καταχώρηση αποτελεσμάτων<br></p>';
				   	header("Location: provider.php");
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
			        <h4>Πληροφορίες Νέου Προμηθευτή</h4>
			        <div class="form-group">	
						<label for="name_p">Όνομα Προμηθευτή:</label>
						<input type="text" class="form-control" id="name_p" name="name_p" required>

						<label for="telephone_p">Τηλέφωνο:</label>
						<input type="text" class="form-control" id="telephone_p" name="telephone_p" required>

						<label for="website_p">Ιστοσελίδα Προμηθευτή:</label>
						<input type="text" class="form-control" id="website_p" name="website_p" required>


						<label for="email_p">Email Επικοινωνίας:</label>
						<input type="email" class="form-control" id="email_p" name="email_p" required>


						<label for="support_p">Υποστήριξη Προμηθευτή:</label>
						<input type="text" class="form-control" id="support_p" name="support_p">


						<label for="comments_p">Σχόλια Προμηθευτή:</label>
						<input type="text" class="form-control" id="comments_p" name="comments_p">
					</div>
				<button id="add" name ="add" type="submit" class="btn btn-primary">Προσθήκη Προμηθευτή</button>
				</form>
			</div> 
		';

		
	}else{
		header("Location: provider.php");
		die("Δεν δόθηκε σωστό ID προμηθευτή.");
	}
 
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "delete") {
	if (isset($_GET['id_p'])){
		$idToDelete= filter_var($_GET['id_p'],FILTER_SANITIZE_NUMBER_FLOAT);

		$providerCheckQuerySQL = "SELECT * FROM equip_svds WHERE provider_e= :idToDelete";
	 	$providerCheckQuerySTMT = $db->prepare($providerCheckQuerySQL);
	 	$providerCheckQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	 	$providerCheckQuerySTMT->execute();
	 	if ($providerCheckQuerySTMT->rowCount() > 0){
	 		$_SESSION['unableToDeleteProvider']='<p class="alert alert-warning unableToDeleteProvider">Ο προμηθευτής δεν μπορεί να διαγραφεί γιατί χρησιμοποιείτε για κάποιο εξάρτημα.<br></p>';
	 		header("Location: provider.php");
	 	} else {

			$deleteQueryBorrowSQL = "DELETE  FROM provider_svds WHERE id_p= :idToDelete";
			$deleteQueryBorrowSTMT = $db->prepare($deleteQueryBorrowSQL);
			$deleteQueryBorrowSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
			if ($deleteQueryBorrowSTMT->execute()) {
				echo '<p class="alert alert-success">Η διαγραφή προμηθευτή έγινε με επιτυχία.<br></p>';
				header("Location: provider.php");
				die();
			}else {
				echo '<p class="alert alert-success">Παρουσιάστηκε πρόβλημα κατά την διαγραφή προμηθευτή.<br></p>';
				header("Location: provider.php");
				die();
			}
		}	
	}else{
		header("Location: provider.php");
		die("Δεν δόθηκε σωστό ID προμηθευτή.");
	}
}


if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "update") {
	if (isset($_GET['id_p'])){

		$idToChange= filter_var($_GET['id_p'],FILTER_SANITIZE_NUMBER_FLOAT);
		$providerChangeQuerySQL = "SELECT * FROM provider_svds WHERE id_p= :idToChange";
	 	$providerChangeQuerySTMT = $db->prepare($providerChangeQuerySQL);
	 	$providerChangeQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
	 	$providerChangeQuerySTMT->execute();
	 	while($providerChangeQuerySTMTResult=$providerChangeQuerySTMT->fetch(PDO::FETCH_ASSOC)){

			echo '
				<div class="container col-md-4">
					<form method="post">
				        <h4>Πληροφορίες Προμηθευτή</h4>
				        <div class="form-group">	
							<label for="name_p">Όνομα Προμηθευτή:</label>
							<input type="text" class="form-control" id="name_p" name="name_p" value="'.$providerChangeQuerySTMTResult['name_p'].'" required>

							<label for="telephone_p">Τηλέφωνο:</label>
							<input type="text" class="form-control" id="telephone_p" name="telephone_p" value="'.$providerChangeQuerySTMTResult['telephone_p'].'" required>

							<label for="website_p">Ιστοσελίδα Προμηθευτή:</label>
							<input type="text" class="form-control" id="website_p" name="website_p" value="'.$providerChangeQuerySTMTResult['website_p'].'" required>


							<label for="email_p">Email:</label>
							<input type="email" class="form-control" id="email_p" name="email_p" value="'.$providerChangeQuerySTMTResult['email_p'].'" required>


							<label for="support_p">Υποστήριξη Προμηθευτή:</label>
							<input type="text" class="form-control" id="support_p" name="support_p" value="'.$providerChangeQuerySTMTResult['support_p'].'" required>


							<label for="comments_p">Σχόλια Προμηθευτή:</label>
							<input type="text" class="form-control" id="comments_p" name="comments_p" value="'.$providerChangeQuerySTMTResult['comments_p'].'" required>
						</div>
					<button id="add" name ="add" type="submit" class="btn btn-primary">Ανανέωση Πληροφορίων</button>
					</form>
				</div> <br><br><br>

			';
		}	
		if(isset($_POST['add'])){
		
			$name_p = filter_var($_POST['name_p'],FILTER_SANITIZE_STRING);
			$telephone_p = filter_var($_POST['telephone_p'],FILTER_SANITIZE_NUMBER_FLOAT);
			$comments_p = filter_var($_POST['comments_p'],FILTER_SANITIZE_STRING);
			$website_p = filter_var($_POST['website_p'],FILTER_SANITIZE_STRING);
			$support_p = filter_var($_POST['support_p'],FILTER_SANITIZE_STRING);
			
			try{

				$providerUpdateSQL = "UPDATE provider_svds SET name_p= :name_p, telephone_p= :telephone_p, website_p= :website_p, email_p= :email_p, support_p= :support_p, comments_p= :comments_p WHERE id_p= :idToChange";
				$providerUpdateSTMT = $db->prepare($providerUpdateSQL);
				$providerUpdateSTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
				$providerUpdateSTMT->bindParam(':name_p', $name_p);
				$providerUpdateSTMT->bindParam(':telephone_p', $telephone_p);
				$providerUpdateSTMT->bindParam(':website_p', $website_p);
				$providerUpdateSTMT->bindParam(':email_p', $_POST['email_p']);
				$providerUpdateSTMT->bindParam(':support_p', $support_p);
				$providerUpdateSTMT->bindParam(':comments_p', $comments_p);
				if ($providerUpdateSTMT->execute()) {
					echo '<p class="alert alert-success">Επιτυχής καταχώρηση αποτελεσμάτων<br></p>';
			   		echo '<meta http-equiv="refresh" content="0; URL=provider.php">';	
				}else {
					echo '<p class="alert alert-warning">Πρόβλημα κατά την τροποποίηση του προμηθευτή.<br></p>';
			   		echo '<meta http-equiv="refresh" content="0; URL=provider.php">';
				}		

			   			    
			}
			catch(PDOException $e)
			{
			    echo "Error: " . $e->getMessage();
			} 

		}
	}else{
		header("Location: provider.php");
		die("Δεν δόθηκε σωστό ID προμηθευτή.");
	}
}	
include("views/footer.php");
echo '
	</body>
	</html>
';
?>  