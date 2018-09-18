<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous"></script>

<script type="text/javascript">
	$("#logout").click(function() {
  		window.location = "logout.php";
	});

	$("#add_equipment").click(function() {
  		window.location = "equipment_add.php";
	});


	$("#modify_equipment").click(function() {
  		window.location = "equipment_manage.php";
	});

	$("#modify").click(function() {
  		window.location = "equipment_modify.php";
	});


	$("#add").click(function() {
			window.alert("Οι αλλαγές έγιναν με επιτυχία.Επιστροφή στην σελίδα εξαρτημάτων");
	});

	$("#active_borrows").click(function() {
			window.location = "active_borrows.php";
	});

	$("#new_borrow").click(function() {
			window.location = "new_borrow.php";
	});

	$(".add_to_basket").click(function() {
		
			$.ajax({
            type: "POST",
            url: "functions.php?function=borrow",
            data: "aem_borrow=" + $(this).attr("aem_borrow") + "&id_equip_borrow=" + $(this).attr("id_equip_borrow") ,
            success: function(result) {
                    window.location.reload();
                }
                 
           })
	});
	 
	$("#clear").click(function() {
		
			$.ajax({
            type: "POST",
            url: "functions.php?function=clear",
            data: "aem_borrow=" + $(this).attr("aem_borrow"),
            success: function(result) {
                    window.location.reload();
                }
                 
           })
	});
	  

	$("#finish").click(function() {
			window.location = "finish.php";
	});	

	

	

</script>
      
</body>
</html>