<?php
session_start();
if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
} else if (array_key_exists("email", $_SESSION)){
	header("Location: logged.php");
}


if(isset($_POST['email'])){

try{
include("connection.php"); 


    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND password = :password"); 
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':password', $_POST['password']);

    $stmt->execute();


     
    if($result=$stmt->fetch(PDO::FETCH_ASSOC)){
    	$_SESSION['email'] = $_POST['email'];
    	header("Location: logged.php");     
	} else {
		echo '<p class="p-3 mb-2 bg-danger text-white">There was a problem. Try again later.</p>';
	} 




}
catch(PDOException $e)
    {
    echo "Error: " . $e->getMessage();
    }

$conn = null;

}
 include("header.php"); 

 	echo '  
    <div class="container">
        <form method="POST">
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
          </div>
          <button name ="logout" value=1 type="submit" class="btn btn-primary">Log In</button>
        </form>
    </div> '; 


include("footer.php"); 
?>
