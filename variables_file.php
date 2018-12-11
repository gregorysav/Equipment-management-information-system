<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
}
include("views/connection.php");
include("checkUser.php");

    $zero= 0;
	$one = 1;
	$two = 2;
	$id = $_SESSION['id'];
	$username = $_SESSION['email'];
	$aem = $_SESSION['aem'];
	$type = $_SESSION['type'];
	$last_name = $_SESSION['last_name'];
	$first_name = $_SESSION['first_name'];
	$email = $_SESSION['email'];
	$telephone = $_SESSION['telephone'];
	$type = $_SESSION['type'];
	$isA = "Σπουδαστής";
	if ($type == 1) {
		$isA = "Διδάσκοντας";
	}
	$today = date('d-m-Y');
	$newTodayFormat = date("Y-m-d", strtotime($today));
    $startToday = date_create($today);
    $newEndDayFormat = date("Y-m-d", strtotime("+3 months"));
    $departmentID = 0;
    $descriptionID = 0;
    $commentID = 0;
    $providerID = 0;
    $startPagination = 0;
    $limitPagination = 3;
    $pageOfPagination = 1;
    $depID = 0;
	$descID = 0;
	$comID = 0;
	$provID = 0;
	$condition = "Ενεργό";
	$equipNames = array();
	$borrowedItem = "";
	$warningMessage = "";
	$departmentsNameArray = array();
	$providersNameArray = array();
	$noImageToDisplay = "noimage.jpg";
	$basketItems = 0;

?>    