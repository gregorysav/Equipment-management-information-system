<?php //Access: Registered Users ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"crossorigin="anonymous"></script>
<script>
	
	$("#logout").click(function() {
  		window.location = "logout.php";
	});

	$("#add_department").click(function() {
  		window.location = "actions_department.php?action=add";
	});


	$("#modify").click(function() {
  		window.location = "equipment_modify.php";
	});


	$("#new_borrow").click(function() {
		window.location = "new_borrow.php";
	});


	$(".add_to_basket").click(function() {
		$.ajax({
            type: "POST",
            url: "actions.php?action=basket",
            data: "name_basket=" + encodeURIComponent($(this).attr("name_basket")) + "&id_equip_basket=" + $(this).attr("id_equip_basket") + "&id_user_basket=" + $(this).attr("id_user_basket"),
            success: function(result) {
                window.location.reload("views/navbar.php");
                window.location.reload();
            }
                 
        })
	});
	 
	$("#clear").click(function() {
		$.ajax({
            type: "POST",
            url: "actions.php?action=clear",
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
        url: "actions.php?action=confirm",
        data: "id_to_confirm=" + $(this).attr("id_to_confirm"),
        success: function(result) {
                window.location.reload();
            }
             
       })
	});	



	$("#imageDelete").click(function() {
		$.ajax({
        type: "GET",
        url: "actions.php?action=imageDelete",
        data: "id_equip=" + $(this).attr("id_equip") + "&image_name=" + $(this).attr("image_name"),
        success: function(result) {
        		window.location.reload();
            }     
       })
	});
	

	$(document).ready(function(){
 
		$('#equipmentName').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"actions.php?action=nameQuery",
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
 
		$('#equipmentNameQuery').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"actions_return.php?action=equipmentNameQuery",
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
 
		$('#borrowerNameQuery').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"actions_return.php?action=borrowerNameQuery",
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
 
		$('#userName').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"actions_return.php?action=borrowerNameQuery",
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
				    url:"actions.php?action=dateQuery",
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
 
		$('#borrowerAEMQuery').typeahead({
	  		source: function(query, result)
	  		{
	   			$.ajax({
				    url:"actions_return.php?action=borrowerAEMQuery",
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
				    url:"actions.php?action=locationQuery",
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
 
		$('#aemBorrow').keyup(function(){ 
			var query = $(this).val();
			if (query != ''){
	   			$.ajax({
				    url:"actions.php?action=AEMQuery",
				    method:"POST",
				    data:{query:query},
				    success:function(data)
				    {	
				    	$('#aemTotal').fadeIn();
				    	$('#aemTotal').html(data);
				    	
	    			}
	   			});
	  		}else {
	  			$('#aemTotal').fadeOut();
	  			$('#aemTotal').html("");
	  		}
	 	});
	 	$(document).on('click', 'li', function(){
	 		$('#aemBorrow').val($(this).text());
	 		$('#aemTotal').fadeOut();
	 	});	
	 
	});

	$(document).ready(function(){
 
	  	$('#idToPrintPDF').keyup(function(){ 
			var query = $(this).val();
			if (query != ''){
	   			$.ajax({
				    url:"actions.php?action=AEMQuery",
				    method:"POST",
				    data:{query:query},
				    success:function(data)
				    {
				    	$('#aemTotal').fadeIn();
				    	$('#aemTotal').html(data);
	    			}
	   			});
	  		}else {
	  			$('#aemTotal').fadeOut();
	  			$('#aemTotal').html("");
	  		}
	 	});
	 	
	 	$(document).on('click', 'li', function(){
	 		$('#idToPrintPDF').val($(this).text());
	 		$('#aemTotal').fadeOut();
	 	});	 
	});
		

	$(".extraComment").click(function() {
		$("#newComment").css("visibility", "visible");
		$(".extraComment").css("visibility", "hidden");
		$("#commentAreaButton").addClass("btn btn-secondary");
	});

 	
 	$("#delete").click(function() {
		window.location = "equipment_manage.php";
	});

	$(".returnEquipment").click(function() {
  		var answerComment = prompt("Θέλετε να αφήσετε σχόλιο για την επιστροφή του εξαρτήματος;");
  		if (answerComment){
  			$.ajax({
            type: "POST",
            url: "actions_equipment.php?action=saveComment",
            data: {id_equip: $(this).attr("id_equip") ,answerComment : answerComment},
            success: function(result) {
                    window.location.reload();
                }
                 
           })
  		}
	});

	
	$("#imageToOpen").click(function() {
		var modal = document.getElementById('myModal');

		var img = document.getElementById('imageToOpen');
		var modalImg = document.getElementById("openedImage");
		
		modal.style.display = "block";
		modalImg.src = this.src;
		
		var span = document.getElementsByClassName("close")[0];
		span.onclick = function() { 
		modal.style.display = "none";
		modalImg.css("width", "100%");
		}
	});
	
	$(document).ready(function() {		
	    $("#isborrowed").change(function(){
	        if (($('#isborrowed option:selected').val()) == 1){	
	     		$("#retired").prop('disabled', true);
	        }else {
	        	$("#addProduct").prop('disabled', false);
	        	$("#retired").prop('disabled', false);
	        }	
	    });
	});

	
	$('#totalEquipmentForTeacher').click(function() {
		if ( this.checked ) {
			window.location = "totalEquipmentForTeacher.php";
			$('#availableEquipmentForTeacher').prop('checked', false);
		} 
	});

	$('#availableEquipmentForTeacher').click(function() {
		if ( this.checked ) {
			window.location = "equipmentViewForTeacher.php";
			$('#totalEquipmentForTeacher').prop('checked', false);
		}
	});

	$('#totalEquipmentForUser').click(function() {
		if ( this.checked ) {
			window.location = "totalEquipmentForUser.php";
			$('#availableEquipmentForUser').prop('checked', false);
		} 
	});

	$('#availableEquipmentForUser').click(function() {
		if ( this.checked ) {
			window.location = "equipmentViewForUser.php";
			$('#totalEquipmentForUser').prop('checked', false);
		}
	});

	$(document).ready(function() {		
	    $("#controlSelect").change(function(){
		$('#returnOptions').submit();
		 });
	});	

	$(function() {
	    setTimeout(function() {
        	$(".unableToDeleteProvider").fadeOut('fast');
    	}, 3000);
	});

	$(function() {
	    setTimeout(function() {
        	$(".unableToDeleteDepartment").fadeOut('fast');
    	}, 3000);
	});

	$(document).ready(function(){
		$('[data-toggle="popover"]').popover(); 
	});

	$('#name_e').keyup(function(){ 
	    var totalLength = this.value.length; 
	    if (totalLength > 100){
	    	alert("Το όνομα του εξαρτήματος δεν πρέπει να ξεπερνά τους 100 χαρακτήρες.");
	    }
	});

	$(function() {
	    setTimeout(function() {
        	$("#CSVCreatedSuccessfuly").fadeOut('fast');
    	}, 3000);
	});

	$(function() {
	    setTimeout(function() {
        	$(".imageDeleteInformMessage").fadeOut('fast');
    	}, 3000);
	});

	$(document).ready(function(){
		$('#filename').change(function(){
		    if( document.getElementById("filename").files.length != 0 ){
			    $("#imageUpload").prop('disabled', false);
			}
		})
	});

	$('#long_desc').keyup(function(){ 
	    var totalLength = this.value.length; 
	    if (totalLength > 200){
	    	alert("Η εκτενής περιγραφή του εξαρτήματος δεν πρέπει να ξεπερνά τους 200 χαρακτήρες.");
	    }
	});

</script>

<div class="container">
	<div id="footerMessage">
	    &#169; <a href="#"> </a>Developed by Savvidis Grigorios. Supervised by <a href="http://arch.icte.uowm.gr/mdasyg"> Minas Dasygenis  </a> , <a href="http://www.uowm.gr"> UOWM-Department of Informatics and Telecommunications Engineering </a> 
	</div>
</div>	