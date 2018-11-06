<?php
  session_start();
  include("views/connection.php");
  include("views/header.php");
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
      $_SESSION['id'] = $result['id'];
      $_SESSION['type'] = $result['type'];
      $userID= $result['id'];
      $zero= 0;

      $borrowQuery = $db->prepare("SELECT * FROM borrow_svds WHERE aem_borrow= $userID"); 
      $borrowQuery->execute();
      while($borrowQueryResult=$borrowQuery->fetch(PDO::FETCH_ASSOC)){
            $Date = $borrowQueryResult['expire_date'];
      }

      $today= "";
      $today = date('Y-m-d');
      $start = date_create($today);
      $findEndDate = $Date;
      $end = date_create($findEndDate);
      $daysToEnd = date_diff($start,$end)->format('%a');
      $dateChangQuery = $db->prepare("UPDATE borrow_svds SET notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE aem_borrow= $userID");
        $dateChangQuery->bindParam(':notify30', $daysToEnd);
        $dateChangQuery->bindParam(':notify20', $daysToEnd);
        $dateChangQuery->bindParam(':notify10', $daysToEnd);
        $dateChangQuery->execute();
    
      $dateQuery = $db->prepare("SELECT * FROM borrow_svds WHERE aem_borrow= $userID"); 
      $dateQuery->execute();
      while($dateQueryResult=$dateQuery->fetch(PDO::FETCH_ASSOC)){
            if ( $dateQueryResult['notify30'] == 30){
                $flagChangQuery = $db->prepare("UPDATE borrow_svds SET notify30= :notify30 WHERE aem_borrow= $userID");
                $flagChangQuery->bindParam(':notify30', $zero);
                $flagChangQuery->execute();
            } elseif ($dateQueryResult['notify30'] == 20) {      
                $flagChangQuery = $db->prepare("UPDATE borrow_svds SET notify20= :notify20 WHERE aem_borrow= $userID");
                $flagChangQuery->bindParam(':notify20', $zero);
                $flagChangQuery->execute();
            } elseif ($dateQueryResult['notify30'] == 10){       
                $flagChangQuery = $db->prepare("UPDATE borrow_svds SET notify10= :notify10 WHERE aem_borrow= $userID");
                $flagChangQuery->bindParam(':notify10', $zero);
                $flagChangQuery->execute();
            } elseif ($dateQueryResult['notify30'] == 0){       
                echo "O daneismos me id: " .$dateQueryResult['id_borrow']. "elikse<br>";
            }
      }          
    echo '
      <div class="container">
      <a href="index.php"><img src="images/uowmicon.jpg" id="uowmicon"></a><h1 id="welcome">Καλώς ήλθατε στην Ηλεκτρονική Σελίδα Δανεισμού το ΠΔΜ</h1>
      </div>';  
    echo '
      <div class="container">
      <p class="p-3 mb-2 bg-success text-white">Καλώς ήρθες ' .$_SESSION['first_name']. '</p>
      </div>';
  } else {
    header("Location: login.php");
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>

  <div class="container">  
    <div class="row">
        <div class="col-md-3">
          <div class="thumbnail">
            <a href="account.php">
              <img src="images/customericon.png" alt="Lights" style="width:80%">
              <div class="caption">
                <p>Προφίλ</p>
              </div>
            </a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="thumbnail">
            <a href="equipment.php">
              <img src="images/componentsicon.png" alt="Lights" style="width:80%">
              <div class="caption">
                <p>Εξαρτήματα</p>
              </div>
            </a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="thumbnail">
            <a href="active.php">
              <img src="images/borrowicon.png" alt="Nature" style="width:80%">
              <div class="caption">
                <p>Δανεισμοί</p>
              </div>
            </a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="thumbnail">
            <a href="search.php">
              <img src="images/searchicon.png" alt="Fjords" style="width:80%">
              <div class="caption">
                <p>Αναζήτηση</p>
              </div>
            </a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="thumbnail">
            <a href="logout.php">
              <img src="images/logouticon.png" alt="Fjords" style="width:80%">
              <div class="caption">
                <p>Αποσύνδεση</p>
              </div>
            </a>
          </div>
        </div>
    </div>
  </div>  
</body>
</html>

<?php
  include("views/footer.php");
?>