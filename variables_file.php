<?php
//Access: Registered Users
if(!isset($_SESSION)) 
{ 
    session_start(); 
}
include("checkUser.php");
include("views/connection.php");

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
	$isA = "Φοιτητής";
	$fullName = $_SESSION['fullName']; 
	if ($type == 1) {
		$isA = "Διδάσκοντας";
	}elseif ($type == 2) {
		$isA = "Μέλος ΔΕΠ";
	}elseif ($type == 3) {
		$isA = "Μέλος Ε.Ε.ΔΙ.Π.";
	}
	date_default_timezone_set('Europe/Athens');
    setlocale(LC_TIME, 'el_GR.UTF-8');
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
	$basketItems = 0;
	$nothing = NULL;
	$null= NULL;

?>    