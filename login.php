<?php
session_start();
if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
} else if (array_key_exists("email", $_SESSION)){
	header("Location: index.php");
}


if(isset($_POST['email'])){

try{
include("views/connection.php"); 


    $stmt = $db->prepare("SELECT * FROM users_svds WHERE email = :email AND password = :password"); 
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':password', $_POST['password']);

    $stmt->execute();


     
    if($result=$stmt->fetch(PDO::FETCH_ASSOC)){
    	$_SESSION['email'] = $_POST['email'];
      $_SESSION['password'] = $_POST['password'];
    	header("Location: index.php");     
	} else {
		echo '<p class="p-3 mb-2 bg-danger text-white">Πρόβλημα εισόδου. Δοκιμάστε σε λίγο.</p>';
	} 




}
catch(PDOException $e)
    {
    echo "Error: " . $e->getMessage();
    }

$db = null;

}
 include("views/header.php"); 

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
    </div> '; 


include("views/footer.php"); 
?>