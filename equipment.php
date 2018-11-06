<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		if ($_SESSION['type'] == 1){
			echo '
				<div class="container">
				<h2>Σελίδα Διαχείρισης Εξαρτημάτων</h2>
			    <button type="submit" id="add_equipment" class="btn btn-success btn-info">Προσθήκη Νέου Εξαρτήματος</button>
		        <button type="submit" id="modify_equipment"class="btn btn-primary btn-danger">Διαγραφή / Τροποποίηση Εξαρτήματος</button>
				</div>
			';
		}

		$start = 0;
        $limit = 2;
        $page = 1;

        if (isset($_GET['p'])){
            $page = $_GET['p'];
            $start = ($page - 1) * $limit;
        }

		$equipQuery = $db->prepare("SELECT * FROM equip_svds LIMIT $start, $limit"); 
	 	$equipQuery->execute();
	 	echo '
 					  <div class="container">
 						<table class="table table-bordered">
						  <thead class="thead-dark">
						    <tr>
						      <th scope="col">Εικόνα</th>
						      <th scope="col">Όνομα</th>
						      <th scope="col">Έτος απόκτησης</th>
						      <th scope="col">Ποσότητα</th>
						      <th scope="col">Τοποθεσία</th>
						    </tr>
						  </thead>
						  ';
	 	while($equipQueryResult=$equipQuery->fetch(PDO::FETCH_ASSOC)){
	 		if (($equipQueryResult['quantity']) > 0 ){
	 		echo '
 						  <tbody>
					      <td><img src="uploadedImages/'.$equipQueryResult['real_filename'].'"/></td>
					      <td><a href=equipment_details.php?id_equip='.$equipQueryResult['id_equip'].'>'.$equipQueryResult['name_e'].'</a></td>
					      <td>'.$equipQueryResult['buy_year_e'].'</td>
					      <td>'.$equipQueryResult['quantity'].'</td>
					      <td>'.$equipQueryResult['location_e'].'</td>
					      </div> ';
			}
					    
					}
			$query = $db->prepare("SELECT * FROM equip_svds");
	 		$query->execute();		
			$rows = $query->rowCount();
	        $total = ceil($rows/$limit);

	        echo '
	        	<div style="display: flex; padding-left: 0px; list-style: none; justify-content: center;">
	        	 <ul class="pagination" style="position: fixed; bottom: 10px;">
	        ';		
	        for ($i=1; $i <= $total; $i++) { 
                if ($page == $i){
                    echo "<li class='active'><a href=equipment.php?p=".$i.">".$i."</a></li>";                    
                }else {

                echo "<li><a href=equipment.php?p=".$i.">".$i."</a></li>";
                }
            }
            echo '
            	</ul>
            	</div>
            ';


	} else {
		header("Location: index.php");
	}

	include("views/footer.php");
?>