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
	 <script>
	 
     </script>
	<style>
	html,body{
	height: 100%;
    width: 100%;
margin:0px;
padding:0px;	
	}
	.wrapper
	{
	display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: normal;
    align-items: normal;
    align-content: normal;
    height: 100%;
    width: 100%;
	background-color:#ef3f23;
	
	}
	.login-left
	{
		flex-grow:1;
		background:url('images/Asset 1.svg');
		background-repeat: no-repeat;
	}
	.login-right
	{
		flex-grow:1;
		//padding:40px;
		flex-basis:0px;
		padding:20px 100px 100px 100px;
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
	.test{
		padding-left:80px;
	}
	.login-form
	{
		background-color:#F6EEE1;
		//width:80%;
		padding:50px;
		border-radius:20px;
		margin-top:40px;
	}
	.form-signin .form-control {
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
.form-signin input[type="password"] {
    margin-bottom: 10px;
   border-radius:35px;
}
.form-signin {
    max-width: 99%;
    padding: 0px;
    margin: 0 auto;
}
.mybtn
{
	border-radius: 40px;
}
@font-face {font-family: 'Telegraf-UltraLight';src: url('css/font/Telegraf-UltraLight.otf'); }
.test svg
{
	height:50px;
}
.trademarkline,.w-line
{
	font-family:Telegraf-UltraLight;
	color:black;
}
	</style>
  </head>
  <body>
  <div class='wrapper'>
      <div class='login-left'></div>
	  <div class='login-right'>
	          <div class='login-right-inner'>
			       <div style='text-align:right;'><img src='images/login-1.svg' height='50px'></div>
				   <div class='test'>
				       <div class='w-line'>Welcome to</div>
					   <div>
					       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 497.35 53.1"><defs><style>.cls-1{isolation:isolate;font-size:49.37px;font-family:Telegraf-UltraLight, Telegraf;font-weight:100;}.cls-2{letter-spacing:-0.02em;}.cls-3{letter-spacing:0em;}.cls-4{letter-spacing:-0.02em;}.cls-5{letter-spacing:0em;}.cls-6{letter-spacing:-0.01em;}.cls-7{letter-spacing:-0.01em;}.cls-8{letter-spacing:-0.03em;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><rect x="1.28" y="17.39" width="4.8" height="22.41"/><path d="M74.33,31.67a13,13,0,0,0,1-5.17V5.92h-4.8V26.5a8.18,8.18,0,0,1-.67,3.3,8.49,8.49,0,0,1-1.83,2.7,8.66,8.66,0,0,1-2.7,1.83,8.46,8.46,0,0,1-9.31-1.83,8.66,8.66,0,0,1-1.83-2.7,8.35,8.35,0,0,1-.67-3.3V5.92H48.8l0,20.58a13,13,0,0,0,1,5.17,13.34,13.34,0,0,0,21.7,4.25A13.77,13.77,0,0,0,74.33,31.67Z"/><polygon points="103.83 10.72 103.83 5.92 80.85 5.92 80.85 39.8 103.83 39.8 103.83 34.99 85.66 34.99 85.66 25.26 100.2 25.26 100.2 20.46 85.66 20.46 85.66 10.72 103.83 10.72"/><rect x="144.46" y="5.92" width="4.8" height="33.88"/><path d="M196.52,35.91a13.31,13.31,0,0,0,21.7-4.25,13,13,0,0,0,1-5.17V5.92h-4.8V26.5a8.35,8.35,0,0,1-.67,3.3A8.49,8.49,0,0,1,212,32.5a8.66,8.66,0,0,1-2.7,1.83,8.54,8.54,0,0,1-11.14-4.53,8.35,8.35,0,0,1-.67-3.3V5.92h-4.76l0,20.58a13,13,0,0,0,1,5.17A13.25,13.25,0,0,0,196.52,35.91Z"/><path d="M40.6,19l4.54-1.63c-.13-.38-.27-.75-.43-1.12a17.38,17.38,0,0,0-3.62-5.39A17,17,0,0,0,35.7,7.25a17,17,0,0,0-13.2,0,16.9,16.9,0,0,0-9,9,17,17,0,0,0,0,13.2,17,17,0,0,0,9,9,17,17,0,0,0,13.2,0A16.93,16.93,0,0,0,44.48,30l-4.56-1.64A12.22,12.22,0,0,1,33.81,34a12,12,0,0,1-9.46,0,12.37,12.37,0,0,1-3.83-2.6,12.54,12.54,0,0,1-2.6-3.83,12.05,12.05,0,0,1,0-9.45,12.24,12.24,0,0,1,6.43-6.46,12.09,12.09,0,0,1,9.46,0,12.16,12.16,0,0,1,6.46,6.46A6.55,6.55,0,0,1,40.6,19Z"/><circle cx="3.68" cy="8.12" r="3.68"/><path d="M183.42,10.85A17.23,17.23,0,0,0,178,7.22a17.09,17.09,0,0,0-13.22,0,17.12,17.12,0,0,0-5.39,3.63,17.35,17.35,0,0,0-3.63,5.39,17.09,17.09,0,0,0,0,13.22,16.94,16.94,0,0,0,9,9,17.09,17.09,0,0,0,13.22,0,17,17,0,0,0,9-9,17.09,17.09,0,0,0,0-13.22A17.37,17.37,0,0,0,183.42,10.85Zm-.81,16.75a12.53,12.53,0,0,1-2.6,3.84,12.27,12.27,0,0,1-3.86,2.6,11.82,11.82,0,0,1-4.72,1,11.63,11.63,0,0,1-4.75-1,12.47,12.47,0,0,1-3.84-2.6,12.29,12.29,0,0,1-2.6-3.84,11.83,11.83,0,0,1-1-4.75,11.66,11.66,0,0,1,1-4.72,12.21,12.21,0,0,1,6.44-6.46,12.16,12.16,0,0,1,9.47,0,12.44,12.44,0,0,1,3.86,2.6,12.27,12.27,0,0,1,2.6,3.86,12.07,12.07,0,0,1,0,9.48Z"/><path d="M128.88,23.5V22.43a7.2,7.2,0,0,0,7.34-6.84h0a9.51,9.51,0,0,0-.76-3.74,9.71,9.71,0,0,0-5.17-5.17,9.3,9.3,0,0,0-3.74-.76H110.91V39.8h4.8V25.72h9.82a4.88,4.88,0,0,1,4.87,4.87h0v9.22h4.8V30.59h0a9.37,9.37,0,0,0-.59-3.26A6.65,6.65,0,0,0,128.88,23.5Zm-13.17-3V10.73h10.84a4.88,4.88,0,0,1,4.87,4.87,4.88,4.88,0,0,1-4.87,4.87H115.71Z"/><path d="M227.29,28.75c1.35,4.15,4.2,6.91,9.06,6.91,4.43,0,6.91-2,6.91-5.23,0-2.8-1.82-4.3-5.93-5.18l-4.34-1c-5.46-1.17-8.54-3.83-8.54-9.1S229,5.88,235.84,5.88s10.69,4.11,11.81,7.7l-4.39,2A7.72,7.72,0,0,0,235.42,10c-4,0-6.3,2.05-6.3,4.9,0,2.61,1.59,4,5.18,4.76l4.48,1c5.79,1.26,9.38,4.43,9.38,9.34,0,5.79-4.34,9.85-12.09,9.85-7.1,0-11.76-3.78-13.26-9.06Z"/><text class="cls-1" transform="translate(262.15 41.1)"><tspan class="cls-2">d</tspan><tspan class="cls-3" x="28.29" y="0">a</tspan><tspan class="cls-4" x="54.5" y="0">s</tspan><tspan class="cls-5" x="79.09" y="0">h</tspan><tspan class="cls-6" x="107.13" y="0">b</tspan><tspan class="cls-4" x="135.57" y="0">o</tspan><tspan class="cls-7" x="163.41" y="0">a</tspan><tspan class="cls-8" x="189.43" y="0">r</tspan><tspan x="206.07" y="0">d</tspan></text></g></g></svg>
					   </div>
					   <div class='trademarkline'>Dynamic, Collaborative, & Secure Patent Insights</div>
					   <div class='login-form'>
					     <form id='form-signin' class="form-signin" method="post" action="" autocomplete='off'>
                <input type="email" class="form-control" name="uid" placeholder="Email" required>
                <input type="password" class="form-control" name="pwd" placeholder="Password" required>
				<div class="g-recaptcha"
      data-sitekey="6Lf0UDYgAAAAAEoJig9zARIRt4F1RrqYbFq7GylB"
      data-callback="onSubmit"
      data-size="invisible">
</div>
                
				<button class="btn btn-lg btn-primary btn-block mybtn">Sign in</button>
				 
				  <?php
				       echo $warningmsg;
				   ?>
                </form>
					   </div>
				   </div>
			  </div>
	  </div>
  </div>

      
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src='js/jquery.validate.js'></script>
	<script>
	$("#form-signin").on("submit", function(e) {
		 e.preventDefault();

    // start recaptcha
    grecaptcha.reset();
    grecaptcha.execute();
	 });
	 
	  /*window.recaptchaSubmit = function(token) {
    // from here on we should really submit the form
    really = true;

    // trigger a second submit
    $form.submit();
  };*/
       function onSubmit(token) {
         document.getElementById("form-signin").submit();
       }
	</script>
	
  </body>
</html>