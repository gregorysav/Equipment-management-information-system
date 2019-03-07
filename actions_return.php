<?php
//Access: Administrator
include("variables_file.php");
include("checkUser.php");

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "equipmentNameQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT DISTINCT name_e FROM equip_svds WHERE name_e LIKE '%$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['name_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "borrowerAEMQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_NUMBER_FLOAT);
        $searchAEMQuerySQL = "SELECT last_name, first_name, aem FROM users_svds WHERE aem LIKE '$search%'";  
        $searchAEMQuerySTMT = $db->prepare($searchAEMQuerySQL);
        $searchAEMQuerySTMT->execute();
        $data=array();
            while ($searchAEMQuerySTMTResult=$searchAEMQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $searchAEMQuerySTMTResult['last_name'].' '.$searchAEMQuerySTMTResult['first_name'].' '.$searchAEMQuerySTMTResult['aem'];
            }           
            echo json_encode($data);
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "borrowerNameQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT DISTINCT last_name, first_name, aem FROM users_svds WHERE last_name LIKE '%$search%' OR first_name LIKE '%$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['last_name'].' '.$searchQuerySTMTResult['first_name'].' '.$searchQuerySTMTResult['aem'];
        }           
        echo json_encode($data);
  
}


?>