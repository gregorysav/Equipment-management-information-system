<div class="container">	
	<nav class="navbar navbar-dark bg-dark" style="background-color: black;">
			  <ul class="nav navbar-nav">
			    <li class="nav-item">
			      <a class="nav-link" href="index.php">Αρχική Σελίδα</a>
			    </li>
			    <li class="nav-item">
			      <a class="nav-link" href="account.php">Προφίλ</a>
			    </li>
			    <li class="nav-item">
			      <a class="nav-link" href="equipment.php">Εξαρτήματα</a>
			    </li>
			    <li class="nav-item">
			      <a class="nav-link" href="active.php">Ενεργοί Δανεισμοί</a>
			    </li>
			    <li class="nav-item">
			      <a class="nav-link" href="search.php">Αναζήτηση</a>
			    </li>
			    <?php if ($_SESSION['type'] == 1) {?>
			    <li class="nav-item">
			      <a class="nav-link" href="confirmation.php">Επιβεβαίωση Δανεισμών</a>
			    </li>
				<?php } ?>
			  </ul>
			  <div class="form-inline pull-xs-right">
			  	<a href="account.php" class="btn btn-outline-light my-2 my-sm-0">Hi, <?php echo $_SESSION['first_name']; ?></a>
			    <button id="logout" class="btn btn-secondary" data-toggle="modal" data-target="#myModal">Αποσύνδεση</button>
	          </div>
	</nav>
</div>
