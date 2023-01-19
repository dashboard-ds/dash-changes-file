<?php
session_start();
include('function/function.php');
$warningmsg ="";
$num =0;
                
				if(isset($_POST['submit']) && !empty($_POST['uid']) && !empty($_POST['pwd']) && isset($_POST['g-recaptcha-response']))
				{
			    // verify captacha first
				$captcha=$_POST['g-recaptcha-response'];
                $gresponse = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LeJAjMcAAAAALqdQdynm0Ke4uh2zzNNOCAAdanv&response=".$captcha), true);
                if($gresponse['success'] == true)
                {
				  					  				  
		          
					   
					 include 'dbconnection.php';
                    if(!$conn) 
		            {
                    die("Connection failed: " . mysqli_connect_error());
                    }
                    mysqli_select_db($conn,$dbname);
                    $sql = "SELECT clientid,name,email,firm,usertype  FROM client where email=? && cpassword=? && allowsignin='1' && cpassword IS NOT NULL && cpassword!=''";
					if($stmt = mysqli_prepare($conn, $sql))
			        {
                     mysqli_stmt_bind_param($stmt, "ss", $emailid,$cpassword);
				     $emailid = trim($_POST["uid"]);
					 $cpassword = md5(trim($_POST['pwd']));
			        }
					
					
					if (mysqli_stmt_execute($stmt))
			        {
				       mysqli_stmt_bind_result($stmt,$clientid,$name,$email,$firm,$usertype);
				       $row = array();
				       $num =0;
				       if(mysqli_stmt_fetch($stmt)) 
				       {
					   $num =1;
					   
					   $row['clientid'] = $clientid;
					   $row['name'] = $name;
                       $row['email'] = $email;
					   $row['firm'] = $firm;
					   $row['usertype'] = $usertype;
					   
                       }
				       
					   if ( $num> 0) 
                        {
			                
							 $_SESSION["clientemail"] = $row["email"];
							 $_SESSION["clientid"] = $row["clientid"];
							 $_SESSION["clientname"]=$row["name"];
							 $_SESSION["firm"] = $row["firm"];
							 $_SESSION["usertype"] = $row["usertype"];
						     echo "<script>window.location = 'clienthome.php';</script>";
							 exit;
					        
			            }
		                else
		                {
					      $warningmsg = "<p class='alert alert-warning' style='margin-top:5px;'>Please check your credential.</p>";
			            }
			       }
                    else{
						echo mysqli_stmt_error($stmt);
					}				   
				  
				}	
			    else
				{
					$warningmsg = "<p class='alert alert-warning'>Unable to authenticate recaptcha.</p>";
				}	
				}
				else
                {
					//$warningmsg = "<p class='alert alert-warning'>Please enter all required fields</p>";
				}					
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<!--my css file-->
	<link rel="stylesheet" href="css/customestyle.css">
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
    <title>Secure Login -iCuerious Dashboard</title>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<style>
	html,body
	{
		background-color: #F6EEE1;
	}
	.account-wall h2{ 
		margin:0px;
	}
	.usertype
	{
		padding:5px 0px 5px 0px;
	}
	.usercheckbox
	{
		
		margin:0px 5px 0px 5px !important;
	}
	.usertype label
	{
      color:#36454F !important;
	}
	</style>
  </head>
  <body>
  <div class="container">
    <div class="row">
	   <div class='outer-cover'>
	    <div class="col-md-6 login-left-container">
		Welcome to
                                <br />
            <h2>
                            	
                                iCuerious Dashboard
                            </h2>
                            <p><strong>Platform to access automated insights.</strong></p>
                            <hr class="hidden-xs"/>
                            <p class="small hidden-xs"></p>
                        
                            <div class='colored-bar'></div>
            
            
        </div>
        <div class="col-md-6 login-right-container">
            
            <div class="account-wall">
                <center><h2>
				           <img src='images/login-front-logo.svg' width='197'/>
				       </h2>
			   </center>
                <form class="form-signin" method="post" action="" autocomplete='off'>
                <input type="email" class="form-control" name="uid" placeholder="Email" required>
                <input type="password" class="form-control" name="pwd" placeholder="Password" required>
				<div class="g-recaptcha" data-sitekey="6LeJAjMcAAAAAAoR4ZSLNmkjJYziRJA03Jz7W_U6"></div>
                <button class="btn btn-lg btn-primary btn-block mybtn" type="submit" name="submit">Sign in</button>
				  <?php
				       echo $warningmsg;
				   ?>
                </form>
				<div class='colored-bar'></div>	
            </div>
            
            
        </div>
		</div>
    </div>
	 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Forgot Password??</h4>
        </div>
        <div class="modal-body">
               <div class='row'>
	               <div class='col-md-12'>
		              
					   <form method='post' class='.form-inline' id='forget-form' action="">
					     <div class='form-group'>
		                     <input type='email' placeholder='Registered email id..' name='uid2' class='form-control' required id='uid2'/>
					     </div>
					     <div class='form-group'>
					     <input type='submit' class='btn btn-primary mybtn' value='submit' name='submit' id='done'>
					      </div>
					    </form>
		           </div>
				   
	            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  


	
</div>
      
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src='js/jquery.validate.js'></script>
	<script>
	$(document).ready(function () {
		$("form").validate();
		$("#forget-form").validate();
		$("#forget-form").submit(function(){
			event.preventDefault();
			var email = encodeURIComponent($("#uid2").val());
		$.ajax({
                                type: 'POST',
                                url: 'sendmail.php',
								data:'email='+email,
                                success: function(data)
			                     {
									  alert(data); 
									  $("#uid2").val("");
								 },
								 error : function()
								 {
									  alert("error occurred");
								 }
				});
		});
		
		$('.usercheckbox').on('click',function(){
			$('.usercheckbox').prop('checked',false);
			
				$(this).prop('checked',true);
			
		});
	});
	</script>
	
  </body>
</html>