<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");


$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :idUser";
$basketQuerySTMT = $db->prepare($basketQuerySQL); 
$basketQuerySTMT->bindParam(':idUser', $_SESSION['id'], PDO::PARAM_INT);
$basketQuerySTMT->execute();
echo '
    <div class="container" id="basket_container">
    <h3>Το καλάθι μου</h3>
';
$basketQuerySTMTResult=$basketQuerySTMT->rowCount();
if ($basketQuerySTMTResult == 0){
  echo "Αυτή τη στιγμή το καλάθι είναι άδειο.";
}else{
  echo '  
    <table class="table table-bordered" id="basket">
    <thead class="thead-dark">
    <tr>
    <th>ID</th>
    <th>Ονομασία</th>
    <th>Αφαίρεση</th>
    </tr>
    </thead>
';
echo '
    <div class="basket_buttons">
    <button type="submit" class="btn btn-primary" id="clear" id_user_basket='.$_SESSION['id'].'>Καθαρισμός</button>
    <button type="submit" class="btn btn-primary" id="complete">Ολοκλήρωση</button>
    </div>
';
}

while($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){

  echo '
    <tbody>
    <td>'.$basketQuerySTMTResult['id_equip_basket'].'</td>
    <td>'.$basketQuerySTMTResult['name_basket'].'</td>
    <td><a href="functions.php?function=remove&id_basket='.$basketQuerySTMTResult['id_basket'].'&id_user_basket='.$_SESSION['id'].'"><p><span class="fa fa-minus-circle"></span></p></a></td>
  ';
}  


include("views/footer.php");
?>