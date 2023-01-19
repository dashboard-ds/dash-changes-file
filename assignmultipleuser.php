<?php //hello testing git
session_start();
include('function/function.php');
if (isset( $_SESSION["clientemail"]))
{
      $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin')
		   {
			echo "<script>alert('You are not authorised to use this Module.')</script>";
		    echo "<script>window.location ='clienthome.php'</script>";
		    exit;
		   }
?>
<!doctype html>
<html lang="en">
  <head>
     <!-- Required meta tags -->
    <meta charset="utf-8">
	<meta name="theme-color" content="#999999" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<!------Hamberger-------->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<!----styling--->
	<link href = "css/stylecss.css" rel = "stylesheet">
	<!---Date table-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css"/>
	<!---------navbarcss------------>
	<link href = "css/navbar.css" rel = "stylesheet">
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
    <title>Assign reports</title>
	<style>
  table.dataTable.display tbody td,table.dataTable.display tbody th
  {
	  background-color:#F6EEE1 !important ;
  }
  table.dataTable.no-footer
  {
	  border:none;
  }
  table.dataTable thead th {
    border-bottom: none;
}
 
table.dataTable tfoot th {
    border-top: none;
    border-bottom: 1px solid  #111;
}
	</style>
  </head>
  
  <body>

                     
<div class='wrapper'>
<div class='info-header'>
	     <div class='info-header-child'>+91-(988)-873-2426 (Ind)</div>
		 <div class='info-header-child'>+1-(339)-237-3075 (USA)</div>
		 <div class='info-header-child'>info@icuerious.com</div>
	</div>
	<div class='new-header'>
	     <div class='new-header-child'><img src="images/dash-comb-logo.svg" height="50"></div>
		 <div class='new-header-child'></div>
		 <div class='new-header-child'>Welcome <?php echo $_SESSION["clientname"];?></div>
		 <div class='new-header-child top-option-bar-outer'>
		    
			      <?php include "clienttopnavbar.php"?>
			
		     
		 </div>
	</div>
                     
</div>                     				
<div class='container'>
	
	    <div class='due-msg'>
		
		
		</div>
	    <div class='show-msg alert alert-danger'>
	        
	    </div>
		<h2>Allocate users to the reports</h2>
	  <form method='post' action='' id='myform'>
	    
		
		<section>
			<h3>Details</h3>
			<div class='row'>
             <div class='col-md-12'>			
	           <div class='form-group'>
		            <label for='rid'>Report id</label>
		                <input type='text' name='rid' class='form-control' id='rid' placeholder='Report id' required />
		        </div>
	            
	     
	        
			<div class='form-group'>
		              <label for='emails'>Email</label>
		             <textarea  name='emails' class='form-control' id='emails' required placeholder='Multiple Emails separated by space'></textarea>
		    </div> 
					
			</div>
	    </div>
		</section>
		            
		
		
         
		<div class='nxtrow' style='text-align:center'>
		    <input type='submit' value='Save' class='button button1' id='sbmit'/>
		</div>
	  </form>
     
	</div>
	


  </div>
</div>
    <!-- jQuery-->
    <script src="js/jquery.min.js"></script>
	 <!-- bootstrap-->
    <script src="js/bootstrap.min.js"></script>
	 <!----ajax------->
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	 <!---------form validation---->
	 <script src='js/jquery.validate.js'></script>
	<script>
	$(document).ready(function () {
		
		$("#myform").validate();
		
	       
		 
		 $("#myform").submit(function(e)
		 {
			e.preventDefault();
					if($("#myform").valid() == true)
		             {
						 $("#sbmit").prop("disabled", true);
						 var fd = $("#myform").serialize();
			              $.ajax({
                                type: 'POST',
                                url: 'request.php',
								data: fd+"&action=allocatemultipleusers",
								dataType:"json",
                                success: function(data)
			                     {
									 
									 
									 if(data[0].rid!='')
									 {
									   alert('Done');
									  
									 }
									 else
									 {
									  alert('Successfully added');
									  
									 }
									 
									 $("#sbmit").prop("disabled", false);
								 }
					       });
						 
					 }
		             
				
		     
			 
		 });		 
		 
		 $('input,textarea,select').each(function(){
			 if($(this).prop('required'))
			 {
				 $(this).css({'border-left':'1px solid red'});
			 }
			 
		 });
          
         		  

             
	});
		        
              
	</script>
	
  </body>
</html>
<?php
	
}
else
{
	header('Location: index.php');
	
}

?>
