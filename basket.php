<?php
//Access: Registered Users
include("variables_file.php");
include("checkUser.php");
echo '
    <!DOCTYPE html>
    <html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
// Στη μεταβλητή $_SESSION['equipmentToBorrow'] αποθηκεύονται τα εξαρτήματα που υπάρχουν στον καλάθι 
$_SESSION['equipmentToBorrow'] = "";
$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :idUser";
$basketQuerySTMT = $db->prepare($basketQuerySQL); 
$basketQuerySTMT->bindParam(':idUser', $id, PDO::PARAM_INT);
$basketQuerySTMT->execute();
echo '
    <div class="container" id="baskePageContainer">
    <h3>Το καλάθι μου</h3>
';
$basketQuerySTMTResult=$basketQuerySTMT->rowCount();
if ($basketQuerySTMTResult == 0){
  echo '<div class="container"><p class="alert alert-info">Αυτή τη στιγμή το καλάθι είναι άδειο.</p></div>';
}else{
    echo '  
        <table class="table table-bordered" id="baskePageTable">
        <thead class="thead-dark">
        <tr>
        <th>ID</th>
        <th>Ονομασία</th>
        <th>Αφαίρεση</th>
        </tr>
        </thead>
    ';
    echo '
        <div class="basketPageButtons">
        <button type="submit" class="btn btn-primary" id="clear" id_user_basket='.$_SESSION['id'].'>Καθαρισμός</button>
        <button type="submit" class="btn btn-primary" id="complete">Ολοκλήρωση</button>
        </div><br>
    ';
}

while($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
    $_SESSION['equipmentToBorrow'] .= $basketQuerySTMTResult['name_basket'].',';
    echo '
        <tbody>
        <tr>
        <td>'.$basketQuerySTMTResult['id_equip_basket'].'</td>
        <td>'.$basketQuerySTMTResult['name_basket'].'</td>
        <td><a href="actions.php?action=removeFromBasket&id_basket='.$basketQuerySTMTResult['id_basket'].'&id_user_basket='.$_SESSION['id'].'&id_equip_basket='.$basketQuerySTMTResult['id_equip_basket'].'"><p><span class="fa fa-minus-circle"></span></p></a></td>
        </tr>
        </tbody>
    ';
}  
echo '</table>';
    
include("views/footer.php");
echo '
    </body>
    </html>
';
?>