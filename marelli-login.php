<?php
session_start();
include('function/function.php');
$warningmsg ="";
$num =0;
                
				if(!empty($_POST['uid']) && !empty($_POST['pwd']) && isset($_POST['g-recaptcha-response']))
				{
			    // verify captacha first
				$captcha=$_POST['g-recaptcha-response'];
				//echo 'response:'.$captcha;
				
                $gresponse = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lf0UDYgAAAAAPqwdEWLTJr3Ko2sejFmOt1p9Wm9&response=".$captcha), true);
                //var_dump($gresponse);
				//exit;
				if($gresponse['success'] == true)
                {
				  					  				  
		          
					   
					 include 'dbconnection.php';
                    if(!$conn) 
		            {
                    die("Connection failed: " . mysqli_connect_error());
                    }
                    mysqli_select_db($conn,$dbname);
                    $sql = "SELECT clientid,name,email,firm,usertype,theme FROM client where email=? && cpassword=? && allowsignin='1' && cpassword IS NOT NULL && cpassword!=''";
					if($stmt = mysqli_prepare($conn, $sql))
			        {
                     mysqli_stmt_bind_param($stmt, "ss", $emailid,$cpassword);
				     $emailid = trim($_POST["uid"]);
					 $cpassword = md5(trim($_POST['pwd']));
			        }
					
					
					if (mysqli_stmt_execute($stmt))
			        {
				       mysqli_stmt_bind_result($stmt,$clientid,$name,$email,$firm,$usertype,$theme);
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
					   $row['theme'] = $theme;
                       }
				       
					   if ( $num> 0) 
                        {
			                
							 $_SESSION["clientemail"] = $row["email"];
							 $_SESSION["clientid"] = $row["clientid"];
							 $_SESSION["clientname"]=$row["name"];
							 $_SESSION["firm"] = $row["firm"];
							 $_SESSION["usertype"] = $row["usertype"];
							 $_SESSION["theme"] = $row["theme"];
							 $_SESSION["loginpage"] = 'marelli';
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
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
    <title>Secure Login -iCuerious Dashboard</title>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<style>
	html,body
	{
	height: 100%;
    width: 100%;
    margin:0px;
    padding:0px;
    background-color:#002652;	
	}
	.wrapper
	{
	display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
    align-content: normal;
    height: 100%;
    width: 100%;
	background-color:#002652;
	
	}
	.top-level-conatiner
	{
		flex-grow:1;
	}
	.bottom
	{
		height:auto;
	}
	.outer-box
	{
		width:500px;
		padding:20px;
	}
	.login-right-inner
	{
	display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    justify-content: normal;
    align-items: normal;
    align-content: normal;
    height: 100%;
    width: 100%;
	}
	.logo-center
	{
		text-align:center;
	}
	.login-form
	{
		background-color:#335377;
		padding:50px;
		border-radius:20px;
		margin-top:40px;
	}
	.form-signin .form-control 
	{
     position: relative;
     font-size: 16px;
     height: auto;
     padding: 20px;
	 border-radius:35px;
     -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
     box-sizing: border-box;
	 margin-bottom:10px;
   }
.form-signin input[type="password"] 
{
    margin-bottom: 10px;
   border-radius:35px;
}
.form-signin 
{
    max-width: 99%;
    padding: 0px;
    margin: 0 auto;
}
.mybtn
{
	border-radius: 40px;
}
.mybtn,.mybtn:hover,.mybtn:visited
{
	background:#002652;
}
@font-face 
{
  font-family: 'Bauziet';
  src: url('css/font/Bauziet-Norm-Regular.otf');  
}
@font-face 
{
  font-family: 'Telegraf-Regular';
  src: url('css/font/Telegraf-Regular.otf');  
}
@font-face 
{
  font-family: 'Telegraf-UltraLight';
  src: url('css/font/Telegraf-UltraLight.otf');  
}
@font-face 
{
  font-family: 'Telegraf';
  src: url('css/font/Telegraf-Bold.otf');
  font-weight: bold;
}
.trademarkline,.w-line
{
	font-family:Telegraf-Regular;
	color:#168EC5;
	text-align:center;
}
</style>
  </head>
  <body>
  <div class='wrapper'>
    <div class='top-level-conatiner'>
	  <div class='outer-box'>
	          <div class='login-right-inner'>
			        <div class='logo-center'>
					      <img src="images/marelli-logo.svg" alt="MARELLI Logo" height='80px'>
					</div>
					<div class='trademarkline'>Dynamic, Collaborative, & Secure Patent Insights</div>
					<div class='login-form'>
					    <form id='form-signin' class="form-signin" method="post" action="" autocomplete='off'>
                             <input type="email" class="form-control" name="uid" placeholder="Email" required>
                             <input type="password" class="form-control" name="pwd" placeholder="Password" required>
				             <div class="g-recaptcha" data-sitekey="6Lf0UDYgAAAAAEoJig9zARIRt4F1RrqYbFq7GylB" data-callback="onSubmit" data-size="invisible"></div>
                             <button class="btn btn-lg btn-primary btn-block mybtn">Sign in</button>
			            <?php
				            echo $warningmsg;
				         ?>
                       </form>
					</div>
					
				   
			  </div>
	  </div>
	  </div>
	  <div class='bottom'>
	     <div class='trademarkline'><center>Powered by iCuerious, LLP</center></div>
	  </div>
  </div>

      
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src='js/jquery.validate.js'></script>
	<script>
	$("#form-signin").on("submit", function(e) 
	{
		 e.preventDefault();
        // start recaptcha
        grecaptcha.reset();
        grecaptcha.execute();
	 });
	 
       function onSubmit(token) 
	   {
         document.getElementById("form-signin").submit();
       }
	</script>
	
  </body>
</html>