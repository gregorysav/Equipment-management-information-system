<?php
	if(isset($_POST['email'])){
	try{
		include("connection.php"); 
		$stmt = $db->prepare("SELECT * FROM users"); 
		$stmt->execute();
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
			if ( $result['email'] ==  $_POST['email'] ){
				if ( $result['password'] == $_POST['password']) {
				echo '<p class="p-3 mb-2 bg-success text-white">You are logged in</p>';
				} else {
				echo '<p class="p-3 mb-2 bg-danger text-white">There was a problem. Try again later.</p>';
				}
				} else {
				echo '<p class="p-3 mb-2 bg-danger text-white">There was a problem. Try again later.</p>';
				}
			}
	}
	catch(PDOException $e){
	echo "Error: " . $e->getMessage();
	}
	$conn = null;
	}
?>
<?php include("header.php"); ?>
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
				<button type="submit" class="btn btn-primary">Log In</button>
			</form>
		</div> 
<?php include("footer.php"); ?>
