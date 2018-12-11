<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
}
include("views/connection.php");
include("views/header.php");
if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
} else if (array_key_exists("email", $_SESSION)){
	header("Location: index.php");
  die("Δεν έχετε συνδεθεί");
}


if(isset($_POST['email'])){

try{
 
    $logInQuerySQL = "SELECT * FROM users_svds WHERE email = :email AND password = :password"; 
    $logInQuerySTMT = $db->prepare($logInQuerySQL);
    $logInQuerySTMT->bindParam(':email', $_POST['email'], PDO::PARAM_INT);
    $logInQuerySTMT->bindParam(':password', $_POST['password'], PDO::PARAM_INT); 
    $logInQuerySTMT->execute();

    if($logInQuerySTMTResult=$logInQuerySTMT->fetch(PDO::FETCH_ASSOC)){
    	$_SESSION['email'] = $logInQuerySTMTResult['email'];
    	$_SESSION['username'] = $logInQuerySTMTResult['username'];
    	$_SESSION['aem'] = $logInQuerySTMTResult['aem'];
    	$_SESSION['last_name'] = $logInQuerySTMTResult['last_name'];
    	$_SESSION['first_name'] = $logInQuerySTMTResult['first_name'];
    	$_SESSION['type'] = $logInQuerySTMTResult['type'];
    	$_SESSION['telephone'] = $logInQuerySTMTResult['telephone'];
    	$_SESSION['type'] = $logInQuerySTMTResult['type'];
    	$_SESSION['id'] = $logInQuerySTMTResult['id'];
    	$userID= $logInQuerySTMTResult['id'];
    	header("Location: index.php");     
      die("Δεν έχετε συνδεθεί");
	} else {
		echo '<div class="container"><div class="p-3 mb-2 bg-danger text-white">Πρόβλημα εισόδου. Παρακαλώ εισάγετε τα σωστά στοιχεία.</div></div>';
	} 




}
catch(PDOException $e)
{
  echo "Error: " . $e->getMessage();
}

$db = null;

}
  

 	echo '
    <div id="logInForm" class="container">
        <form method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Εισαγωγή email">
          </div>
          <div class="form-group">
            <label for="password">Κωδικός</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Κωδικός">
          </div>
          <button name ="logout" value=1 type="submit" class="btn btn-primary">Σύνδεση</button>
        </form>
    </div> 
  '; 


include("views/footer.php"); 
?>