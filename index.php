<?php
  session_start();
  include("views/connection.php");
  include("views/header.php");
  include("views/navbar.php");
  if ($_SESSION['email']){
    $current_email = $_SESSION['email'];
    $usersQuery = $db->prepare("SELECT * FROM users_svds WHERE email= :email"); 
    $usersQuery->bindParam(':email', $current_email);
      $usersQuery->execute();
      $result=$usersQuery->fetch(PDO::FETCH_ASSOC);
      $_SESSION['username'] = $result['username'];
      $_SESSION['aem'] = $result['aem'];
      $_SESSION['last_name'] = $result['last_name'];
      $_SESSION['first_name'] = $result['first_name'];
      $_SESSION['type'] = $result['type'];
      $_SESSION['telephone'] = $result['telephone'];
    echo '
    <div class="container">
    <p class="p-3 mb-2 bg-success text-white">Έχετε συνδεθεί επιτυχώς ως ' .$_SESSION['email']. ' με ΑΕΜ ' .$_SESSION['aem']. '</p>
    </div>';
  } else {
    header("Location: login.php");
  }
  include("views/footer.php");
?>

