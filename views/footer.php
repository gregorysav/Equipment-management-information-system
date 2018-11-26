<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
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

	$("#new_borrow").click(function() {
			window.location = "new_borrow.php";
	});

	$(".add_to_basket").click(function() {
		$.ajax({
            type: "POST",
            url: "functions.php?function=basket",
            data: "name_basket=" + $(this).attr("name_basket") + "&id_equip_basket=" + $(this).attr("id_equip_basket") + "&id_user_basket=" + $(this).attr("id_user_basket"),
            success: function(result) {
                    window.location.reload();
                }
                 
        })
	});
	 
	$("#clear").click(function() {
		$.ajax({
            type: "POST",
            url: "functions.php?function=clear",
            data: "id_user_basket=" + $(this).attr("id_user_basket"),
            success: function(result) {
                    window.location.reload();
                }
                 
        })
	});

	$("#complete").click(function() {
  		window.location = "finish.php";
	});
	  

	$("#finish").click(function() {
			window.location = "finish.php";
	});
	
	$(".confirm").click(function() {
			$.ajax({
            type: "POST",
            url: "functions.php?function=confirm",
            data: "id_to_confirm=" + $(this).attr("id_to_confirm"),
            success: function(result) {
                    window.location.reload();
                }
                 
           })
	});	



	$("#imageDelete").click(function() {
			$.ajax({
            type: "GET",
            url: "functions.php?function=imageDelete",
            data: "id_equip=" + $(this).attr("id_equip") + "&image_name=" + $(this).attr("image_name"),
            success: function(result) {
            		alert("Η εικόνα αφαιρέθηκε επιτυχώς");
                    window.location.reload();
                }     
           })
	});	

	$(document).ready(function(){
 
		$('#equipmentName').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"functions.php?function=nameQuery",
				    method:"POST",
				    data:{query:query},
				    dataType:"json",
				    success:function(data)
				    {
				     result($.map(data, function(item){
				      return item;
				     }));
	    			}
	   			})
	  		}
	 	});
	 
	});


	$(document).ready(function(){
 
		$('#yearOfBuy').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"functions.php?function=dateQuery",
				    method:"POST",
				    data:{query:query},
				    dataType:"json",
				    success:function(data)
				    {
				     result($.map(data, function(item){
				      return item;
				     }));
	    			}
	   			})
	  		}
	 	});
	 
	});	

	$(document).ready(function(){
 
		$('#locationName').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"functions.php?function=locationQuery",
				    method:"POST",
				    data:{query:query},
				    dataType:"json",
				    success:function(data)
				    {
				     result($.map(data, function(item){
				      return item;
				     }));
	    			}
	   			})
	  		}
	 	});
	 
	});
	

	$(".extraComment").click(function() {
		$("#newComment").css("visibility", "visible");
		$(".extraComment").css("visibility", "hidden");
	});

	
</script>

<div class="container">
	<div id="footerMessage">
	    &#169; <a href="#"> </a>Developed by Savvidis Grigorios. Supervised by <a href="http://arch.icte.uowm.gr/mdasyg"> Minas Dasygenis  </a> , <a href="http://www.uowm.gr"> UOWM-Department of Informatics and Telecommunications Engineering </a> 
	</div>
</div>	
</body>
</html>