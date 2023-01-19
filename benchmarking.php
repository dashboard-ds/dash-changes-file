<?php
session_start();
include 'dbconnection.php';
include 'function/function.php';
if (isset( $_SESSION["clientemail"]))
{
if(!isset($_GET['reportid']))
{
	echo "Please check entered publication number";
	exit;
}
$reportmetainfo = Array();
include 'dbconnection.php';
mysqli_select_db($conn,$dbname);
		   //$sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus from reports r, reportallocation allo where r.rid = ? && r.rid=allo.rid && allo.email='".$_SESSION["clientemail"]."'";
	  $typeofuser = checktypeofuser($_SESSION["clientemail"]);
		   if($typeofuser =='client')
		   {
		   $sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus from reports r, reportallocation allo where r.rid = ? && r.rid=allo.rid && allo.email='".$_SESSION["clientemail"]."'";
		   }
		   else if($typeofuser =='user')
		   {
			 $sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus from reports r, reportallocation allo where r.rid = ? && r.uploadedby='".$_SESSION["clientemail"]."'";   
		   }
		   elseif($typeofuser =='admin')
		   {
			  $sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus from reports r where r.rid = ?";    
		   }
		   else
		   {
			   echo "access denied";
			   exit;
		   }
		   if($stmt = mysqli_prepare($conn, $sql))
		   {
		   mysqli_stmt_bind_param($stmt, "s", $rid);
		   $rid =trim($_GET['reportid']);
		   mysqli_stmt_execute($stmt);
		   }
		   mysqli_stmt_bind_result($stmt,$rid,$title,$type,$creationtime,$report_relevancy,$report_scoring,$report_categorization,$report_typeofassignee,$report_familymembers_legalstatus);	
           $num=0;		   
		   if(mysqli_stmt_fetch($stmt))
		   {
			   $num=1;
			   $reportmetainfo['rid'] = $rid;
			   $reportmetainfo['title'] = $title;
			   $reportmetainfo['type'] = $type;
			   $reportmetainfo['creationtime'] = $creationtime;
			   $reportmetainfo['relevancy'] = $report_relevancy;
			   $reportmetainfo['scoring'] = $report_scoring;
			   $reportmetainfo['categorization'] = $report_categorization;
			   $reportmetainfo['typeofassignee'] = $report_typeofassignee;
			   $reportmetainfo['legalstatus'] = $report_familymembers_legalstatus;
			   
		   }
           
          if ( $num> 0) 
          { 
	        
	      }
	      else
		  {
			 echo "check the provided reportid.";
			 exit;
		  }
    
	$toplevelarray = Array();
	include 'dbconnection.php';
    mysqli_select_db($conn,$dbname);
    $sql ="SELECT distinct level1 FROM categorization where rid='".$reportmetainfo['rid']."' order by level1 asc";
	$result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);
    if ($num> 0) 
    {
	  for($i=0;$i<$num;$i++) 
	     {
		   $row=mysqli_fetch_array($result);
		   array_push($toplevelarray,$row['level1']); 
																				   
	     }
	}
	
	$hirearchylevel = Array();
	$hirearchy1 = Array();
	$hirearchy2 = Array();
	$hirearchy3 = Array();
	$hirearchy4 = Array();
	$hirearchy5 = Array();
	$test ='testing';
	mysqli_select_db($conn,$dbname);
    $sql ="SELECT distinct level1,level2,level3,level4,level5 FROM categorization where rid='".$reportmetainfo['rid']."' group by level1,level2,level3,level4,level5 ";
	$result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);
    if ($num> 0) 
    {
	  for($i=0;$i<$num;$i++) 
	     {
		   $row=mysqli_fetch_array($result);
		   array_push($hirearchy1,$row['level1']); 
		   array_push($hirearchy2,$row['level2']);
           array_push($hirearchy3,$row['level3']);
           array_push($hirearchy4,$row['level4']);	
           array_push($hirearchy5,$row['level5']);		   
	     }
	}
	
   $uniquelevel1 = array_values(array_unique($hirearchy1));
   $uniquelevel2 = array_values(array_unique($hirearchy2));
   $uniquelevel3 = array_values(array_unique($hirearchy3));
   $uniquelevel4 = array_values(array_unique($hirearchy4));
   $child = Array();
   
   
function getsubchild($searchval,$level)
{
	global $hirearchy1;
	global $hirearchy2;
	global $hirearchy3;
	global $hirearchy4;
	global $hirearchy5;
	global $hirearchylevel;
	global $child;
	
	if(trim($level)!='' && $level!=null && $level=='l1')
	{
		
		 $temp = array();
		for($i=0;$i<count($hirearchy1);$i++) 
	     {
			
			 if($hirearchy1[$i]==$searchval)
			 {
				 if($hirearchy3[$i]!='' && $hirearchy3[$i]!=null)
				 {
				 array_push($temp,$hirearchy2[$i]);
				 }
				 
			 }
		    
	     }
		 
		 $temp = array_values(array_unique($temp));
		 
		 for($i=0;$i<count($temp);$i++) 
	     {
			
			if($temp[$i]!='')
			{
				array_push($child,$temp[$i]); 
				array_push($hirearchylevel,'l2');
				getsubchild($temp[$i],'l2'); 
			}
		    
	     }
		 
	}
	if(trim($level)!='' && $level!=null && $level=='l2')
	{
		
		 $temp = array();
		for($i=0;$i<count($hirearchy2);$i++) 
	     {
			
			 if($hirearchy2[$i]==$searchval)
			 {
				 if($hirearchy4[$i]!='' && $hirearchy4[$i]!=null)
				 {
				 array_push($temp,$hirearchy3[$i]);
				 }
				 
			 }
		    
	     }
		 
		 $temp = array_values(array_unique($temp));
		 
		 for($i=0;$i<count($temp);$i++) 
	     {
			if($temp[$i]!='')
			{
			 array_push($child,$temp[$i]); 
			 array_push($hirearchylevel,'l3');
			 
		     getsubchild($temp[$i],'l3'); 
			}
	     }
		 
	}
	
	if(trim($level)!='' && $level!=null && $level=='l3')
	{
		 $temp = array();
		for($i=0;$i<count($hirearchy3);$i++) 
	     {
			
			 if($hirearchy3[$i]==$searchval)
			 {
				 if($hirearchy5[$i]!='' && $hirearchy5[$i]!=null)
				 {
				 array_push($temp,$hirearchy4[$i]);
				 
				 }
				 
			 }
		    
	     }
		 
		 $temp = array_values(array_unique($temp));
		 
		 for($i=0;$i<count($temp);$i++) 
	     {
			if($temp[$i]!='')
			{
			array_push($child,$temp[$i]); 
			array_push($hirearchylevel,'l4');
		    getsubchild($temp[$i],'l4'); 
			 
			}
	     }
		 
	}
	
	if(trim($level)!='' && $level!=null && $level=='l4')
	{
		 $temp = array();
		for($i=0;$i<count($hirearchy4);$i++) 
	     {
			
			 if($hirearchy4[$i]==$searchval)
			 {
				 if($hirearchy5[$i]!='' && $hirearchy5[$i]!=null)
				 {
				 array_push($temp,$hirearchy5[$i]);
				 }
				 
			 }
		    
	     }
		 
		 $temp = array_values(array_unique($temp));
		 
		 for($i=0;$i<count($temp);$i++) 
	     {
			if($temp[$i]!='')
			{
			array_push($child,$temp[$i]); 
			echo 'push';
			array_push($hirearchylevel,'l5');
		    //getsubchild($temp[$i],'l4'); 
			}
	     }
		 
	}
}	
  for($i=0;$i<count($uniquelevel1);$i++) 
	     {
			array_push($child,$uniquelevel1[$i]); 
			array_push($hirearchylevel,'l1'); 
		  getsubchild($uniquelevel1[$i], 'l1'); 
	     }
    
	
	
	mysqli_close($conn); 

//---------------get list of all bench marking projects--------------------
        include 'dbconnection.php';
		$data= array();
        mysqli_select_db($conn,$dbname);
		 if($typeofuser =='admin')
		 {
		 $sql =  "SELECT r.rid,r.type,r.title,r.reporttag,r.referenceno,r.pono,r.creationtime,r.uploadedby FROM reports r where r.type = 'Benchmarking'";	 
		 }
		 else
		 {	 
		 $sql =  "SELECT r.rid,r.type,r.title,r.reporttag,r.referenceno,r.pono,r.creationtime,r.uploadedby FROM reports r,reportallocation allo where r.rid=allo.rid && allo.email='".$_SESSION["clientemail"]."' && r.type = 'Benchmarking'";
		 }
		 $result = mysqli_query($conn, $sql);
		 $num = mysqli_num_rows($result);
		 
		  if ( $num> 0) 
           {
			for($i=0;$i<$num;$i++) 
	           {
				 $newdata = array();
		         $row=mysqli_fetch_array($result);
				 $newdata['rid'] = $row['rid'];
				 $newdata['title'] = $row['title'];
				 $newdata['type'] = $row['type'];
				 $newdata['reporttag'] = $row['reporttag'];
				 $newdata['referenceno'] = $row['referenceno'];
				 $newdata['po'] = $row['pono'];
				 $newdata['creationtime'] = $row['creationtime'];
				 $newdata['uploadedby'] = $row['uploadedby'];
				 if($row['type']=='Patent Mining')
				 {
					 $newdata['url'] ="patentmining.php?reportid=".$row['rid'];
				 }
				 elseif($row['type']=='Benchmarking')
				 {
					 $newdata['url'] ="benchmarking.php?reportid=".$row['rid'];
				 }
				 else
				 {
					 $newdata['url'] ="viewreport.php?reportid=".$row['rid'];
				 }
				 
				 array_push($data,$newdata);      
			   } 
			   if ( $num> 0) 
               { 
	             
			
	           }
				
			   
		   }
		   else
		   {
			 
			   
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
	<link href="css/flag-icon.css" rel="stylesheet">
	<!-- Oue select dropdown -->
    <link rel="stylesheet" href="css/chosen.css">
	<link href="css/sumoselect.css" rel="stylesheet"/>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
	<script src="https://use.fontawesome.com/9f102ed215.js"></script>
	<link href="css/tipso.css" rel="stylesheet">
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
	<link rel="stylesheet" href="css/pretty-checkbox.css"/>
	<!---Dash Board-->
    <link rel="stylesheet" type="text/css" href="css/dashboardstyle.css"/>
	<?php if(isset($_SESSION["theme"]) && $_SESSION["theme"]!='')
			  {
				  if($_SESSION["theme"]=='blue')
				  {
					  echo '<link rel="stylesheet" type="text/css" href="css/theme-blue.css"/>';
				  }
				  
			  }
	 ?>
    <title>DashReports</title>
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-38089819-1', 'auto');
  ga('send', 'pageview');

</script>
  </head>
  <style>
  </style>
  <body>
    <div class='wrapper'>
	<div class='info-header'>
	     <div class='info-header-child'>+91-(988)-873-2426 (Ind)</div>
		 <div class='info-header-child'>+1-(339)-237-3075 (USA)</div>
		 <div class='info-header-child'>info@icuerious.com</div>
	</div>
	<div class='new-header'>
	     <div class='new-header-child'><a href='clienthome.php'>
		 <?php 
			  if(isset($_SESSION["firm"]) && $_SESSION["firm"]!='')
			  {
				  echo "<div id='customiziedlogo'>".$_SESSION["firm"]." <span class='logo-dash'>dashboard</span></div>";
			  }
			  else
			  {
				  ?>
		 <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 664.94 105.93" style="enable-background:new 0 0 664.94 105.93;" xml:space="preserve">
<style type="text/css">

	.st0{fill:#EF3C18;}
	.st1{fill:#EF3D17;}
	.st2{font-family:'Telegraf-UltraLight';}
	.st3{font-size:49.3698px;}
	.st4{fill:#0A0A0A;}
</style>
<g>
	<g>
		<rect x="134.78" y="51.45" class="st0" width="4.8" height="22.41"/>
	</g>
	<g>
		<path class="st0" d="M207.83,65.73c0.69-1.61,1.04-3.35,1.04-5.17V39.98h-4.8v20.58c0,1.16-0.22,2.27-0.67,3.3
			c-0.44,1.02-1.05,1.93-1.83,2.7c-0.77,0.77-1.68,1.39-2.7,1.83c-2.07,0.89-4.54,0.89-6.61,0c-1.02-0.44-1.93-1.05-2.7-1.83
			c-0.77-0.77-1.39-1.68-1.83-2.7c-0.44-1.03-0.67-2.14-0.67-3.3V39.98h-4.76l-0.04,20.58c0,1.82,0.35,3.56,1.04,5.17
			c0.69,1.62,1.65,3.05,2.85,4.25c1.2,1.2,2.63,2.16,4.25,2.85c3.24,1.38,7.1,1.38,10.35,0c1.62-0.69,3.05-1.65,4.25-2.85
			C206.18,68.77,207.14,67.34,207.83,65.73z"/>
	</g>
	<g>
		<polygon class="st0" points="237.33,44.78 237.33,39.98 214.36,39.98 214.36,73.86 237.33,73.86 237.33,69.05 219.16,69.05 
			219.16,59.32 233.7,59.32 233.7,54.52 219.16,54.52 219.16,44.78 		"/>
	</g>
	<g>
		<rect x="277.96" y="39.98" class="st0" width="4.8" height="33.88"/>
	</g>
	<g>
		<path class="st0" d="M330.03,69.97c1.2,1.2,2.63,2.16,4.25,2.85c3.24,1.38,7.11,1.38,10.35,0c1.62-0.69,3.05-1.65,4.25-2.85
			c1.2-1.2,2.16-2.63,2.85-4.25c0.69-1.61,1.04-3.35,1.04-5.17V39.98h-4.8v20.58c0,1.16-0.23,2.27-0.67,3.3
			c-0.44,1.02-1.05,1.93-1.83,2.7c-0.77,0.77-1.68,1.39-2.7,1.83c-2.07,0.89-4.54,0.89-6.61,0c-1.02-0.44-1.92-1.05-2.7-1.83
			c-0.78-0.78-1.39-1.68-1.83-2.7c-0.44-1.03-0.67-2.14-0.67-3.3V39.98h-4.76l-0.04,20.58c0,1.82,0.35,3.56,1.04,5.17
			C327.87,67.34,328.83,68.77,330.03,69.97z"/>
	</g>
	<g>
		<path class="st0" d="M174.1,53.07l4.54-1.63c-0.13-0.38-0.27-0.75-0.43-1.12c-0.89-2.06-2.11-3.87-3.62-5.39
			c-1.52-1.52-3.33-2.74-5.39-3.62c-2.05-0.88-4.27-1.33-6.6-1.33c-2.33,0-4.55,0.45-6.6,1.33c-2.06,0.89-3.87,2.11-5.39,3.62
			c-1.52,1.52-2.74,3.33-3.63,5.39c-0.88,2.05-1.33,4.27-1.33,6.6c0,2.33,0.45,4.55,1.33,6.6c0.89,2.06,2.11,3.87,3.63,5.39
			c1.52,1.52,3.33,2.74,5.39,3.63c2.05,0.88,4.27,1.33,6.6,1.33c2.33,0,4.55-0.45,6.6-1.33c2.06-0.89,3.87-2.11,5.39-3.63
			c1.38-1.38,2.52-3.02,3.39-4.86l-4.56-1.64c-0.59,1.15-1.35,2.19-2.25,3.09c-1.09,1.09-2.39,1.97-3.86,2.6
			c-2.98,1.29-6.51,1.29-9.46,0c-1.45-0.63-2.74-1.51-3.83-2.6c-1.09-1.09-1.96-2.38-2.6-3.83c-0.64-1.47-0.97-3.06-0.97-4.74
			c0-1.65,0.32-3.23,0.96-4.71c0.64-1.47,1.51-2.77,2.6-3.86c1.09-1.09,2.38-1.96,3.83-2.6c2.95-1.28,6.48-1.29,9.46,0
			c1.47,0.64,2.77,1.51,3.86,2.6c1.09,1.09,1.96,2.39,2.6,3.86C173.89,52.48,174,52.77,174.1,53.07z"/>
	</g>
	<g>
		<circle class="st0" cx="137.19" cy="42.18" r="3.68"/>
	</g>
	<g>
		<path class="st0" d="M316.93,44.91c-1.52-1.52-3.33-2.74-5.39-3.63c-2.05-0.88-4.27-1.33-6.61-1.33c-2.34,0-4.56,0.45-6.61,1.33
			c-2.06,0.89-3.88,2.11-5.39,3.63c-1.52,1.52-2.74,3.34-3.63,5.39c-0.88,2.05-1.33,4.27-1.33,6.61c0,2.34,0.45,4.56,1.33,6.61
			c0.89,2.06,2.11,3.87,3.63,5.39c1.52,1.52,3.34,2.74,5.4,3.63c2.05,0.88,4.27,1.33,6.61,1.33c2.33,0,4.56-0.45,6.61-1.33
			c2.06-0.89,3.87-2.11,5.39-3.63c1.52-1.52,2.74-3.34,3.63-5.39c0.88-2.05,1.33-4.27,1.33-6.61c0-2.34-0.45-4.56-1.33-6.61
			C319.67,48.25,318.45,46.43,316.93,44.91z M316.11,61.66c-0.63,1.45-1.51,2.74-2.6,3.84c-1.09,1.09-2.39,1.97-3.86,2.6
			c-1.48,0.64-3.07,0.96-4.72,0.96c-1.69,0-3.28-0.32-4.75-0.97c-1.46-0.64-2.75-1.51-3.84-2.6c-1.09-1.09-1.97-2.38-2.6-3.84
			c-0.64-1.47-0.97-3.07-0.97-4.75c0-1.65,0.32-3.24,0.97-4.72c0.64-1.47,1.51-2.77,2.6-3.86c1.09-1.09,2.38-1.96,3.84-2.6
			c2.95-1.28,6.49-1.28,9.47,0c1.47,0.64,2.77,1.51,3.86,2.6c1.09,1.09,1.97,2.39,2.6,3.86c0.64,1.49,0.97,3.07,0.97,4.72
			C317.08,58.6,316.75,60.19,316.11,61.66z"/>
	</g>
	<g>
		<path class="st0" d="M262.38,57.56v-1.07c4.44-0.1,7.34-3.49,7.34-6.84l0,0c0-1.31-0.26-2.56-0.76-3.74
			c-0.51-1.18-1.21-2.22-2.08-3.09c-0.87-0.87-1.91-1.57-3.09-2.08c-1.17-0.51-2.43-0.76-3.74-0.76H244.4v33.88h4.8V59.78h9.82
			c1.33,0,2.49,0.48,3.44,1.43c0.95,0.95,1.43,2.11,1.43,3.44v0v9.22h4.8v-9.22v0c0-1.14-0.21-2.23-0.59-3.26
			C267.29,59.37,264.98,57.69,262.38,57.56z M249.21,54.52v-9.73h10.84c1.33,0,2.49,0.48,3.44,1.43c0.95,0.95,1.43,2.11,1.43,3.44
			c0,1.33-0.48,2.49-1.43,3.44c-0.95,0.95-2.11,1.43-3.44,1.43H249.21z"/>
	</g>
	<g>
		<g>
			<path class="st0" d="M360.79,62.81c1.35,4.15,4.2,6.91,9.06,6.91c4.43,0,6.91-2.05,6.91-5.23c0-2.8-1.82-4.3-5.93-5.18
				l-4.34-0.98c-5.46-1.17-8.54-3.83-8.54-9.1c0-5.37,4.53-9.29,11.39-9.29c6.86,0,10.69,4.11,11.81,7.7l-4.39,2.01
				c-1.35-3.69-3.97-5.55-7.84-5.55c-3.97,0-6.3,2.05-6.3,4.9c0,2.61,1.59,3.97,5.18,4.76l4.48,0.98c5.79,1.26,9.38,4.43,9.38,9.34
				c0,5.79-4.34,9.85-12.09,9.85c-7.1,0-11.76-3.78-13.26-9.06L360.79,62.81z"/>
		</g>
	</g>
</g>
<g>
	<circle class="st1" cx="42.84" cy="33.6" r="12.23"/>
</g>
<g>
	<circle class="st1" cx="69.59" cy="61.37" r="12.23"/>
</g>
<rect x="31.2" y="48.9" class="st1" width="22.63" height="24.68"/>
<rect x="85.72" y="22.15" class="st1" width="22.63" height="51.43"/>
<text transform="matrix(1 0 0 1 395.6519 75.1571)" class="st0 st2 st3">dashboard</text>
<g>
	<g>
		<path class="st0" d="M111.49,22.25h6.49v1.17h-2.59v6.16h-1.36v-6.16h-2.54V22.25z"/>
		<path class="st0" d="M119.04,29.58v-7.33h2.24l1.39,3.83c0.21,0.59,0.27,1.24,0.28,2.21h0.52c0.01-0.96,0.09-1.62,0.3-2.21
			l1.38-3.83h2.18v7.33h-1.36v-3.29c0-0.97,0.22-1.99,0.65-3.04l-0.51-0.21l-2.36,6.54h-1.14l-2.36-6.54l-0.51,0.21
			c0.43,1.04,0.65,2.04,0.65,3.04v3.29H119.04z"/>
	</g>
</g>
</svg>
<?php
			  } 
			  ?>
		 </a></div>
		 <div class='new-header-child'><b>Title : </b><span class='project-title'><?php echo $reportmetainfo['title'];?></span></div>
		 <div class='new-header-child'><span class='wl-pr-text'>Welcome <?php echo $_SESSION["clientname"];?></span></div>
		 <div class='new-header-child top-option-bar-outer'>
	             <?php include "clienttopnavbar.php"?>	     
		 </div>
	</div>
 <!-- <div class='header'><button  class='actionbtn'  style='float:right;'><a target='_blank' id='exportbtn' href='exportresultset.php?reportid=<?php echo$_GET["reportid"]?>' title='Export Resultset In Excel Sheet'>Export</a></button><button title='Show result set' class='actionbtn' id='bcktolst' style='float:right;'>Result set</button><button title='show/hide filters' class='actionbtn filter-area-not-expanded' id='showfilters' style='float:right;'>Filters</button><button title='show/hide comments' class='actionbtn comment-area-not-expanded' id='showcomments' style='float:right;'>Comments</button><button title='show insights' class='actionbtn' id='showinsights' style='float:right;'>Insights</button></div>-->
  <div id='middle-container-outer-wrapper'>
  <div id='middle-container-left-wrapper'>
  <div class='filter-card-wd-100'>
  <div class='site-header'>Benchmarking Site <a class='gotoexternal' title='Export Scores' target='_blank'>Export Data <i class="fa fa-external-link" aria-hidden="true"></i></a></div>
  <div id="insight-result-count-clickedpoint"></div>
  </div>
  <div class='insight-card'><div class='insight-card-inner' id='chart-1'></div></div>
  <div class='insight-card'><div class='insight-card-inner' id='chart-2'></div></div>
  <div class='insight-card'><div class='insight-card-inner' id='chart-3'></div></div>
  <div class='insight-card'><div class='insight-card-inner' id='chart-4'></div></div>
  <div class='insight-card-wd-100'><div class='insight-card-inner' id='chart-5'></div></div>
 
    
  </div> 
  <?php if(count($data)>0)
  {
	?>
  <div id="middle-container-right-wrapper-projectlist">
            <div class='sticky'>Project List</div>
            <div id='project-list-container'>
			    <?php
				   for($i=0;$i<count($data);$i++) 
	               {
				    $srno = $i+1;
					if($_GET['reportid'] == $data[$i]["rid"])
					{
				    echo '<div class="anchordiv"><a class="activereport"  href='.$data[$i]["url"].'>'.$data[$i]["title"].'</a></div>';
					}
					else
					{
					 echo '<div class="anchordiv"><a  href='.$data[$i]["url"].'>'.$data[$i]["title"].'</a></div>';	
					}
				   }
				?>
			</div>
  </div>
   <?php  
  }
  ?>  
  </div>
  <div class='bottom'>
     <div class='bottom-inner'>
      <div class='bottom-content'>© iCuerious 2012-2022. All Rights Reserved
	      <a style='float:right;display:none;' id="google_translate_element"></a>
	  </div>
      
	  
	</div>
	</div>
  </div>
  

<div class="full-screen hidden-outer flex-container-center">
  <div class='layout-modal'>
  <div style='margin-bottom:10px;'>
    <span id='layout-insight-header'>Layout Settings</span>  <span id='closelayoutbtn'>X</span>
  </div>
  <div>
				  
                              <input type='checkbox' name='xaxislabel' id='xaxislabel'  />	<label for='xaxislabel'>Show complete X axis tick labels</label>
							  <br/><label>X axis tick label angle</label> <select name='xaxislabelangle' id='xaxislabelangle'><option>Auto</option><option>0</option><option>45</option><option>90</option><option>-45</option><option>-90</option></select>
                              <br/><label>X axis tick font size</label> <select name='xaxislabelfont' id='xaxislabelfont'><option>6</option><option>8</option><option selected>10</option><option>12</option><option>14</option><option>16</option><option>18</option><option>20</option></select>
							  <br/><input type='checkbox' name='yaxislabel' id='yaxislabel'  checked />	<label for='yaxislabel'>Show complete Y axis tick labels</label>
							  <br/><label>Y axis tick label angle</label> <select name='yaxislabelangle' id='yaxislabelangle'><option>Auto</option><option>45</option><option>90</option><option>-45</option><option>-90</option></select>
                              <br/><label>Y axis tick font size</label> <select name='yaxislabelfont' id='yaxislabelfont'><option>6</option><option>8</option><option selected>10</option><option>12</option><option>14</option><option>16</option><option>18</option><option>20</option></select>
					          <br/><label>Max Bubble size</label><select name='bubbleresizer' id='bubbleresizer'><option>10</option><option>20</option><option>30</option><option>40</option><option selected>50</option></select>
					  
                     
				     
			                  
				              
					    <div class='colors-pallets'>
						  <div><label>Select Color Palette</label></div> 
					      <div><span><input type='radio' name='palette' class='palette' value='p1'/></span><span id='p1'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p2'/></span><span id='p2'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p3'/></span><span id='p3'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p4'/></span><span id='p4'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p5'/></span><span id='p5'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p6'/></span><span id='p6'></span></div>
						  <div><span><input type='radio' name='palette' class='palette'  checked value='p7'/></span><span id='p7'></span></div>
					 
					   </div>
				  </div>
</div>		   
			 
</div>  
<!---pop filter--->
<div class='filter-right-side-view-wrapper-popup' id='filter-right-side-view-wrapper-popup'>
       <div class='filter-right-side-view'>
	        <div class="notify-arrowup" style=""></div>
	        <div class='all-filter-options-outer'>
			   <form id='filtersform'>
			      <div class='filter-label pretty p-default p-round'><input type='checkbox' name='pubdate' value='pubdate' id='flabel-1'/><div class='state p-danger'><label for='flabel-1'>Publication Date</label> </div></div>
	              <div class='filter-label-opt'>
				         <div>
				         <select name='pubdate_comparisonoperator' id='pubdate_comparisonoperator'><option>is</option><option>after</option><option>before</option><option>between</option></select>
	                     </div>
						 <div>
						 <input type='date' id='pubdate_start' name='pubstart'/><br/>
	                     <input type='date' name='pubend' style='display:none;' id='pubdate_end'/>
						 </div>
	               </div>
					   
				  
	              <div class='filter-label pretty p-default p-round'>
	                   <input type='checkbox' name='pdate' value='pdate' id='flabel-2'/><div class='state p-danger'><label for='flabel-2'>Priority Date</label></div>
	              </div>
	              <div class='filter-label-opt'>
				       <div>
	                   <select name='pdate_comparisonoperator' id='pdate_comparisonoperator'><option>is</option><option>after</option><option>before</option><option>between</option></select>
					   </div>
					   <div>
					   <input type='date' name='pstart' id='pdate_start'/><br/>
					   <input type='date' name='pend' id='pdate_end' style='display:none;'/>
                       </div>
				  </div>
	              <div class='filter-label pretty p-default p-round'>
	                   <input type='checkbox' name='appdate' value='appdate' id='flabel-3'/><div class='state p-danger'><label for='flabel-3'>Application Date</label> </div>
	              </div>
	              <div class='filter-label-opt'>
				       <div>
	                   <select name='appdate_comparisonoperator' id='appdate_comparisonoperator'><option>is</option><option>after</option><option>before</option><option>between</option></select>
					   </div>
					   <div>
					   <input type='date' name='appstart' id='appdate_start'/><br/>
					   <input type='date' name='append' id='appdate_end' style='display:none;'/>
                       </div>
				  </div>
	              <div class='filter-label pretty p-default p-round'>
	                   <input type='checkbox' name='updationdate' value='updationdate' id='flabel-4'/><div class='state p-danger'><label for='flabel-4'>Archieve Date</label></div> 
	              </div>
	              <div class='filter-label-opt'>
	                   <div>
					   <select name='updation_comparisonoperator' id='updation_comparisonoperator' ><option>is</option><option>after</option><option>before</option><option>between</option></select>
					   </div>
					   <div>
					   <input type='date' name='updationstart' id='updationdate_start'/><br/>
					   <input type='date' name='updationend' id='updationdate_end' style='display:none;'/>
	                   </div>
				  </div>
	              <div class='filter-label pretty p-default p-round'>
	                  <input type='checkbox' name='relevancycheck' value='relevancy' id='flabel-5'/><div class='state p-danger'><label for='flabel-5'>Relevancy</label> </div>
	              </div>
	              <div class='filter-label-opt'>
	                  <select name='relevancy[]'><option value=''>--Select--</option><option>H</option><option>M+</option><option>M</option><option>L</option></select>
	              </div>
	              <div class='filter-label pretty p-default p-round'>
	                  <input type='checkbox' name='assigneecheck' value='assignee' id='flabel-6'/><div class='state p-danger'><label for='flabel-6'>Assignee</label></div>
	              </div>
	              <div class='filter-label-opt'>
	                  <select id='assignee' name='assignee[]'  title='Assignee' multiple class='summoselect'>
		
							<?php
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 //$sql ="SELECT distinct parentassignee FROM relevantpatents where rid='".$reportmetainfo['rid']."' order by parentassignee asc";
																			       $sql = "SELECT parentassignee,count(*) as count FROM relevantpatents where rid='".$reportmetainfo['rid']."' group by parentassignee order by count desc";
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <option><?php echo utf8_encode($row['parentassignee'])?></option>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
							 ?>
																	   
				   </select>
	            </div>
	            <div class='filter-label pretty p-default p-round'>
	                <input type='checkbox' name='level1check' value='level1' id='flabel-7'/><div class='state p-danger'><label for='flabel-7'>Level1</label></div>
	            </div>
	            <div class='filter-label-opt'>
	                  <select id='level1' name='level1[]'  title='Level1'>
							<option value=''>--Select--</option>													
																	   <?php
																	           
																				   for($i=0;$i<count($toplevelarray);$i++) 
	                                                                               {
		                                                                           
																				   echo"<option>".$toplevelarray[$i]."</option>";
																				   
																				   }
																			   
																	   ?>
																	   
					</select>
               </div>
               <div class='filter-label pretty p-default p-round'>
                    <input type='checkbox' name='level2check' value='level2' id='flabel-8'/><div class='state p-danger'><label for='flabel-8'>Level2</label></div>
               </div>
               <div class='filter-label-opt'>
	                 <select id='level2' name='level2[]'  title='Level2'>
				         <option value=''>--Select--</option>
                     </select>
               </div>
               <div class='filter-label pretty p-default p-round'>
                    <input type='checkbox' name='level3check' value='level3' id='flabel-9'/><div class='state p-danger'><label for='flabel-9'>Level3</label></div>
               </div>
               <div class='filter-label-opt'>
	                 <select id='level3' name='level3[]'  title='Level3'>
				           <option value=''>--Select--</option>
	                 </select>
	           </div>
               <div class='filter-label pretty p-default p-round'>
                     <input type='checkbox' name='level4check' value='level4' id='flabel-10'/><div class='state p-danger'><label for='flabel-10'>Level4</label></div>
               </div>
               <div class='filter-label-opt'>
	               <select id='level4' name='level4[]'  title='Level4'>
				          <option value=''>--Select--</option>
	               </select>
		       </div>
               <div class='filter-label pretty p-default p-round'>
                     <input type='checkbox' name='taggingcheck' value='tagging' id='flabel-11'/><div class='state p-danger'><label for='flabel-11'>Tagging</label></div>
               </div>
               <div class='filter-label-opt'>
	               <select id='tagging' name='tagging[]'  title='Tagging'>
				          <option value=''>--Select--</option>
						  <?php
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 $sql ="SELECT distinct tagging FROM relevantpatents where tagging is not null && tagging!='' && rid='".$reportmetainfo['rid']."' order by tagging asc";
																			
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <option><?php echo $row['tagging']?></option>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
							 ?>
	               </select>
		       </div>			   
																	   
	    </form>
		</div>
		<div class='all-filter-apply-outer'>
		    <button id='applyfilterbtn' class='actionbtn' style='width:auto;height:auto;background-color:#ef3f23;border-radius:8px;text-transform:none;width:80%;font-size:14px;'>Apply Filters</button>
		</div>
       
     
	</div>
  </div>
 
  <!-- jQuery-->
    <script src="js/jquery.min.js"></script>
	 <!-- bootstrap-->
    <script src="js/bootstrap.min.js"></script>
	 <!-- select dropdown this page -->
		<script src="js/chosen.jquery.js" type="text/javascript"></script>
		<script src="js/jquery.sumoselect.js"></script>
	<!---------form validation---->
	 <script src='js/jquery.validate.js'></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js"></script>
	  <script src="js/tipso.js"></script>
	  <script src='js/initial.js'></script>
      <script type="text/javascript">
      function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en',layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL}, 'google_translate_element');
      }
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
 <script language="javascript" src="js/CSVExport.js"></script>   
	<script>
	  $(document).ready(function () 
	   {
		   /*$("#assignee").chosen({width:"170px",
    height: "2.5em"});*/
	$('body .summoselect').SumoSelect({search: true, searchText: 'Enter here.'});
	$('.show-help').tipso({background:'#777777',color:'#fff',position: 'top',size: 'small'});
	var fd;	
	var sectiontoshow_after_applyingfilter = 1; // 1 - consilidated view , 2 insight view, 3 pub view
	var showchart_after_applyingfilter = '';
	var insightclicked = 0;
	var reportid = '<?php echo $reportmetainfo["rid"];?>';
	var exportdata;
	var currentpage;
    $("#applyfilterbtn").click(function(e){
	e.preventDefault();
	 if($("#filtersform").valid() == true)
		 {
			    currentpage=1;
				$('#pagination').show();
				fd = $("#filtersform").serialize();
			    getrelevantpatents(reportid,'1');
			    if(sectiontoshow_after_applyingfilter==1)
				{
					$('#bcktolst').trigger('click');
					if(showchart_after_applyingfilter!='')
					{
						$('#'+showchart_after_applyingfilter).trigger('click');
						
						
					}
				}
				if(sectiontoshow_after_applyingfilter==2)
				{
					$('#showinsights').trigger('click');
					
					if(showchart_after_applyingfilter!='')
					{
						$('#'+showchart_after_applyingfilter).trigger('click');
						
						
					}
				}
                //invoicepagination(1);				
	     }
});


	
	
	$('#showfilters').on('click',function(){
		$('#showcomments').removeClass('activetab');
		$('#showcomments').addClass('comment-area-not-expanded');
		$('#comment-right-side-view-wrapper-popup').hide();
		$('#filter-right-side-view-wrapper-popup').toggle();
			if($(this).hasClass('filter-area-not-expanded'))
			{
				$(this).removeClass('filter-area-not-expanded');
				$(this).addClass('activetab');
				var filterbtn = $(this).offset();
			    var filtertop  = filterbtn.top + $(this).outerHeight(true);
			    var filterleft = filterbtn.left;
			    var filterright = $(document).outerWidth(true)-filterleft;
			    var filterwidth = $(this).outerWidth(true)/2;
				
				
				 var bottomheight = $('.bottom').outerHeight(true);
			    $('#filter-right-side-view-wrapper-popup').css({'top':filtertop,'right':0,'bottom':bottomheight});
			    //$('.notify-arrowup').css({'right':filterright-filterwidth-5,'top':-5});
			    
			    
			}
			else
			{
				$(this).addClass('filter-area-not-expanded');
				$(this).removeClass('activetab');
			}
		return false;
		if(!$('#filter-right-side-view-wrapper').hasClass('visibility-true'))
		{
			$('#filter-right-side-view-wrapper').addClass('visibility-true');
			$('#filter-right-side-view-wrapper').show();	
		}
		else
		{
			$('#filter-right-side-view-wrapper').removeClass('visibility-true');
			$('#filter-right-side-view-wrapper').hide();
		}
		window.dispatchEvent(new Event('resize'));
	});
	
	
	
	$('#showinsights').on('click',function(){
		if(!$('#middle-container-insight-view').hasClass('visibility-true'))
		{
			sectiontoshow_after_applyingfilter =2;
			$('#middle-container-insight-view').addClass('visibility-true');
		    $('#middle-container-insight-view').css('display', 'flex');
			$('#middle-container-filter-view').removeClass('visibility-true');
			$('#middle-container-filter-view').hide();
			$('#middle-container').removeClass('visibility-true');
			$('#middle-container').hide();
			$('#middle-container-consolidate-view').removeClass('visibility-true');
			$('#middle-container-consolidate-view').hide();
			$('#showfilters').show();
			$('#showcomments').show();
			$('#bcktolst').show();
			$('#showinsights').hide();
			$('#pagination').hide();
		    $('#pagination_pubview_outer').hide();
		}
		
		if(insightclicked==0)
		{
			$('.getinsight').first().trigger('click');
		}
		else
		{
			fetchcommentrecords(reportid,showchart_after_applyingfilter);
		}
		
		
	});
	
	
	
	
	
	$("#pubdate_comparisonoperator").on('change',function(){
		if($(this).val()=='between')
		{
			$('#pubdate_end').show();
		}
		else
		{
			$('#pubdate_end').hide();
		}
	});
	$("#appdate_comparisonoperator").on('change',function(){
		if($(this).val()=='between')
		{
			$('#appdate_end').show();
		}
		else
		{
			$('#appdate_end').hide();
		}
	});
	$("#pdate_comparisonoperator").on('change',function(){
		if($(this).val()=='between')
		{
			$('#pdate_end').show();
		}
		else
		{
			$('#pdate_end').hide();
		}
	});
	$("#updation_comparisonoperator").on('change',function(){
		if($(this).val()=='between')
		{
			$('#updationdate_end').show();
		}
		else
		{
			$('#updationdate_end').hide();
		}
	});
	
	$("#level1").on('change',function(){
		var level1val=[];
		 $("#level1 > option").each(function(){
			 
												 if($(this).prop("selected")==true)
												 {
													 var selectedval = $(this).val();
													 level1val.push(encodeURIComponent(selectedval));
												 }
											 });
			$("#level2").empty();								 
		//alert(level1val);
		$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=level2values&reportid="+reportid+"&level1="+level1val,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							 
							 	 if(data!=0)
								  {
									 var content='<option value="">--Select--</option>';
									  for (i = 0; i < data.length; i++) 
								      {
										  if(data[i].level2!='')
										  {
										  content = content+"<option>"+data[i].level2+"</option>";
										  }
										  
									  }
							       }
								   else
								   {
									   var content='<option value="">--Not Found--</option>';
								   }

                                 $("#level2").append(content);								   
                          },
                          error: function (e) 
						  {
                                      console.log("ERROR : ", e);
                                      if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });  
	});
	
	$("#level2").on('change',function(){
		var level2val=[];
		 $("#level2 > option").each(function(){
			 
												 if($(this).prop("selected")==true)
												 {
													 var selectedval = $(this).val();
													 level2val.push(encodeURIComponent(selectedval));
												 }
											 });
			$("#level3").empty();								 
		//alert(level1val);
		$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=level3values&reportid="+reportid+"&level2="+level2val,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							 
							 	 if(data!=0)
								  {
									 var content='<option value="">--Select--</option>';
									  for (i = 0; i < data.length; i++) 
								      {
										  if(data[i].level3!='')
										  {
										  content = content+"<option>"+data[i].level3+"</option>";
										  }
										  
									  }
							       }
								   else
								   {
									   var content='<option value="">--Not Found--</option>';
								   }

                                 $("#level3").append(content);								   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });  
	});
	
	$("#level3").on('change',function(){
		var level3val=[];
		 $("#level3 > option").each(function(){
			 
												 if($(this).prop("selected")==true)
												 {
													 var selectedval = $(this).val();
													 level3val.push(encodeURIComponent(selectedval));
												 }
											 });
			$("#level4").empty();								 
		//alert(level1val);
		$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=level4values&reportid="+reportid+"&level3="+level3val,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							 
							 	 if(data!=0)
								  {
									 var content='<option value="">--Select--</option>';
									  for (i = 0; i < data.length; i++) 
								      {
										  if(data[i].level4!='')
										  {
										  content = content+"<option>"+data[i].level4+"</option>";
										  }
										  
									  }
							       }
								   else
								   {
									   var content='<option value="">--Not Found--</option>';
								   }

                                 $("#level4").append(content);								   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });  
	});
	
	                corporateinsights();
					function corporateinsights()
					{
						fd = $("#filtersformcriteria").serialize();
						$('#chart-1').empty();
						$('#chart-2').empty();
						$('#chart-3').empty();
						$('#chart-4').empty();
						$('#chart-5').empty();
							

                       $('#chart-1').html("<div id='processing-div-1'>We are processing your request.... <img src='images/process.gif'/></div>");
                       $('#chart-2').html("<div id='processing-div-2'>We are processing your request.... <img src='images/process.gif'/></div>");
                       $('#chart-3').html("<div id='processing-div-3'>We are processing your request.... <img src='images/process.gif'/></div>");
                       $('#chart-4').html("<div id='processing-div-4'>We are processing your request.... <img src='images/process.gif'/></div>");
                       $('#chart-5').html("<div id='processing-div-5'>We are processing your request.... <img src='images/process.gif'/></div>");
                       	
                        $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart=44&topvalue=20",
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart illustrates the dominant companies according to patent assest index.");
							    $('#processing-div-1').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].score);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											
											var globalmetadata ={'drilldown':'enable','x':'assignee','y':'count'};
											
											
										 changecharttype('hbar','Patent Portfolio Asset Value','chart-1',globalmetadata);
											
							       }
								   else
								   {
									   $('#chart-1').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					  
					  $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart=45&topvalue=20",
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart illustrates the dominant companies according to market coverage score.");
							    $('#processing-div-3').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].score);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											var globalmetadata ={'drilldown':'enable','x':'assignee','y':'count'};
											
											
										 changecharttype('hbar','Geographic coverage','chart-3',globalmetadata);
											
							       }
								   else
								   {
									   $('#chart-3').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					  
					  
					  
	                   $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart=4&topvalue=20",
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart illustrates the dominant companies filing patents in the subject technology domain.");
							    $('#processing-div-2').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].count);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											var globalmetadata ={'drilldown':'enable','x':'assignee','y':'count'};
											
											
										 changecharttype('hbar','Patent portfolio size','chart-2',globalmetadata);
											
							       }
								   else
								   {
									   $('#chart-2').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
	           
                     $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart=46&topvalue=20",
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("Technology score");
							    $('#processing-div-4').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].score);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											var globalmetadata ={'drilldown':'enable','x':'assignee','y':'count'};
											
											
										 changecharttype('hbar','Attention degree','chart-4',globalmetadata);
											
							       }
								   else
								   {
									   $('#chart-4').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					  
					  
					  
					  $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart=47&topvalue=20",
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							   
							   $("#insightdescription").html("THIS CHART HIGHLIGHTS THE PATENT DISTRIBUTION of various companies AMONG THE DIFFERENT COUNTRIES CORRESPONDING TO ALL THE PATENT FAMILIES OF RELEVANT PATENTS.");
								$('#processing-div-5').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  /*$.each(data, function(key, value)
									  {
                                              $.each(value, function(key, value){
                                                x.push(key);
										        y.push(value);
                                                });
                                       });*/
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].assignee);
										  z.push(data[i].value);
										  y.push(data[i].cc);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											var globalmetadata ={'drilldown':'enable','x':'assignee','y':'activecountry','xcol':'Assignee Name','ycol':'Market Country','zcol':'Count'};
											
											
										changecharttype('h3Dbar','Patent portfolio size by country','chart-5',globalmetadata);
											
								    
							       }
								   else
								   {
									   $('#chart-5').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });						
					}	
						
						
/*-----------------------Insights----------------------------------------*/	
var globalx;
var gloabaly;
var globalz;
var globalmetadata;
var charttitle;			


$('body').on('click','.getinsight',function(){
	                insightclicked =1;
					exportdata = '';
	                charttitle = $(this).text();
					globalx = [];
					globaly = [];
					globalz = [];
					globalmetadata =[];
					var tracecolor= ['#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844','#f44336','#f85b47','#fc6f59','#ff826b','#ff947d','#ffa590','#ffb6a4','#ffc7b8','#ffd7cd','#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844'];
					$('.getinsight').css({'font-weight':'normal'});
					$(this).css({'font-weight':'bold'});
					$('#tester').remove();
					$('#insightdescription').html("");
					$('.mc-iv-right-tester-outer').html("<div id='processing-div'>We are processing your request.... <img src='images/process.gif'/></div>");
					$('.mc-iv-right-tester-outer').append("<div id='tester' style='width:100%;height:100%;'></div>");
					var chart = 3;
					$('#insight-result-count-clickedpoint').html('');
					showsettingbtn(chart);
					showpdatefilter(chart);
					showleadercheckbox(chart);
					var previouschartid = showchart_after_applyingfilter;
					showchart_after_applyingfilter = $(this).attr("id");
					var currentchartid = showchart_after_applyingfilter;
					var layout = {font: {family: 'Telegraf,Bahnschrift, sans-serif'},colorway : tracecolor,title:charttitle,paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1',annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}]};
					
					var bubblelayout = {font: {family: 'Telegraf,Bahnschrift, sans-serif'},colorway : tracecolor,title:charttitle,paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1','yaxis': {'automargin': true},showlegend: false,hovermode: "closest",annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}]};
					
					var stackedcolors = ['#F4BDAE','#F9E2B8','#FFF2D7','#C0EFF5','#53C6D9'];
					 var stackedcolors2 = ["#4c78a8", "#f58518", "#e45756", "#72b7b2", "#54a24b", "#eeca3b", "#b279a2", "#ff9da6", "#9d755d", "#bab0ac"];
					var stackedlayout = {font: {family: 'Telegraf,Bahnschrift, sans-serif'},colorway : stackedcolors2,title:charttitle,paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1',barmode: 'stack',showlegend: true,hovermode: "closest",legend: {},xaxis:{showgrid:false,'categoryorder':'total descending'},yaxis:{showgrid:false},annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}]};
					
					var config = {
						           displaylogo: false,
					               displayModeBar: true,
								   responsive: true, 
								   modeBarButtonsToRemove:["zoom2d", "pan2d", "select2d", "lasso2d", "zoomIn2d", "zoomOut2d", "autoScale2d", "resetScale2d", "hoverClosestCartesian", "hoverCompareCartesian", "zoom3d", "pan3d", "resetCameraDefault3d", "resetCameraLastSave3d", "hoverClosest3d", "orbitRotation", "tableRotation", "zoomInGeo", "zoomOutGeo", "resetGeo", "hoverClosestGeo", "sendDataToCloud", "hoverClosestGl2d", "hoverClosestPie", "toggleHover", "resetViews", "toggleSpikelines", "resetViewMapbox"],
								   modeBarButtonsToAdd: [
                                                         /*{
                                                          name: 'Comments',
                                                          icon: {'svg':'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="grey" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'},
                                                          direction: 'up',
                                                          click: function(gd) 
	                                                           { 
	                                                           var url = "viewcomment.php?reportid="+reportid+"&chartid="+showchart_after_applyingfilter;
	                                                           window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=300,left=500,width=400,height=350");
						                                       }
						                                 },*/
														 {
                                                          name: 'Export Chart Data',
                                                          icon: {'svg':'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M13 12h3l-4 4-4-4h3V8h2v4zm2-8H5v16h14V8h-4V4zM3 2.992C3 2.444 3.447 2 3.999 2H16l5 5v13.993A1 1 0 0 1 20.007 22H3.993A1 1 0 0 1 3 21.008V2.992z"/></svg>'},
														  direction: 'up',
                                                          click: function(gd) 
	                                                           { 
	                                                               exportchartdata();
						                                       }
						                                 }
														 ]
				                 };
								 
					fetchcommentrecords(reportid,showchart_after_applyingfilter);
					
					if(chart==1)
					{
					 $('#top-companies-filters').hide();
					 $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							 $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].country);
										  y.push(data[i].count);
										  
									  }
									  
									  TESTER = document.getElementById('tester');
                                       Plotly.plot( 
									    TESTER, 
									     [{
                                         values: y,
                                         labels: x,
                                         type:'pie',
										 hole: .4,
										 }],
										 layout,
										 config
										 );
                                        console.log( Plotly.BUILD );
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
						   if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                           
                          }
                      });
					}
					else if(chart==2)
					{
						
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="scatter">Scatter</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart illustrates the distribution of the relevant patents based on their relevance (H, M+, and M) to the subject technology domain.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].relevancy);
										  y.push(data[i].count);
										  
									  }
									 
								            globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'relevancy','y':'count'};
											
											if(currentchartid==previouschartid)
											{
												changecharttype($("#switchchart").val(),charttitle);
											}
											else
											{
												changecharttype('pie',charttitle);
											}

                                          
							       }
								   else
								   {
									  $('#tester').html("<div>No result set found.</div>"); 
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==3)
					{
						
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="line">Line</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart represents an overall filing trend in the subject technology domain.");
							    $('#processing-div').remove();
							   if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].filingyear);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'count'};	
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==4)
					{
						$('#top-companies-filters').show();
						if(currentchartid!=previouschartid)
						{
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						  $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart illustrates the dominant companies filing patents in the subject technology domain.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].count);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'count'};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==5)
					{
						$('#insight-suboption').hide();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (result) 
						  {
							  $('#insightdescription').html("This chart represents a birds' eye view of the various technology areas considered for the analysis.");
							  $('#processing-div').remove();
							 	 if(result!=0)
								  {
									 
									  /*TESTER = document.getElementById('tester');

                                     Plotly.plot( TESTER, 
									 [{
                                         parents: data['parent'],
                                         labels: data['child'],
                                           type:"treemap",
										   name:"Taxanomy",
										   //values: data['values'],
	                                 }],
									 {margin: {l: 0, r: 0, b: 0, t: 45},font: {family: 'Bahnschrift, sans-serif'},colorway : palette7,title:charttitle,paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1'},
									 config);*/
                                      /*---------------------new approach-----------------*/
									  var data = result['nodes'];

                                      // create a name: node map
                                      var dataMap = data.reduce(function(map, node) {
	                                            map[node.name] = node;
	                                            return map;
                                                 }, {});

                                       // create the tree array
                                       var treeData = [];
                                       data.forEach(function(node) {
	                                   // add to parent
	                                   var parent = dataMap[node.parent];
	                                   if (parent) {
		                               // create child array if it doesn't exist
		                               (parent.children || (parent.children = []))
			                           // add node to child array
			                           .push(node);
	                                   } else {
		                               // parent is null or missing
		                               treeData.push(node);
	                                   }
                                       });

                                       //console.log(treeData);
                                       //alert($("#tester").width());

                                        // ************** Generate the tree diagram	 *****************
                                        var margin = {top: 0, right: 0, bottom: 0, left: 120},
	                                    width = $('#tester').width() - margin.right - margin.left,
	                                    height = $('#tester').height() * 2 - margin.top - margin.bottom;
	
                                        var i = 0,
	                                    duration = 750,
	                                    root;

                                        var tree = d3.layout.tree()
	                                     .size([height, width]);
                                       //.nodeSize([20, 40])//

                                       var diagonal = d3.svg.diagonal()
	                                   .projection(function(d) { return [d.y, d.x]; });

                                       var svg = d3.select("#tester").append("svg")
	                                    //.attr("width", width + margin.right + margin.left)
	                                    //.attr("height", height + margin.top + margin.bottom)
										.attr("width","100%")
	                                    .attr("height", "200%")
                                        .append("g")
	                                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                                        root = treeData[0];
                                        root.x0 = height / 2;
                                        root.y0 = 0;
  
                                        update(root);

                                      //d3.select(self.frameElement).style("height", "500px");

                                     function update(source) {

                                       // Compute the new tree layout.
                                      var nodes = tree.nodes(root).reverse(),
	                                  links = tree.links(nodes);

                                      // Normalize for fixed-depth.
                                      nodes.forEach(function(d) { d.y = d.depth * 180; });

                                      // Update the nodes…
                                      var node = svg.selectAll("g.node")
	                                  .data(nodes, function(d) { return d.id || (d.id = ++i); });

                                      // Enter any new nodes at the parent's previous position.
                                      var nodeEnter = node.enter().append("g")
	                                   .attr("class", "node")
	                                   .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
	                                   .on("click", click);

                                       nodeEnter.append("circle")
	                                   .attr("r", 1e-6)
	                                   .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

                                       nodeEnter.append("text")
	                                   .attr("x", function(d) { return d.children || d._children ? -13 : 13; })
	                                   .attr("dy", ".35em")
	                                   .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
	                                   .text(function(d) { return d.name; })
	                                   .style("fill-opacity", 1e-6);

                                        // Transition nodes to their new position.
                                        var nodeUpdate = node.transition()
	                                    .duration(duration)
	                                    .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

                                       nodeUpdate.select("circle")
	                                   .attr("r", 1)
	                                   .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

                                       nodeUpdate.select("text")
	                                   .style("fill-opacity", 1);

                                       // Transition exiting nodes to the parent's new position.
                                       var nodeExit = node.exit().transition()
	                                   .duration(duration)
	                                   .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
	                                   .remove();

                                       nodeExit.select("circle")
	                                  .attr("r", 1e-6);

                                       nodeExit.select("text")
	                                   .style("fill-opacity", 1e-6);

                                      // Update the links…
                                     var link = svg.selectAll("path.link")
	                                .data(links, function(d) { return d.target.id; });

                                     // Enter any new links at the parent's previous position.
                                    link.enter().insert("path", "g")
	                                .attr("class", "link")
	                                .attr("d", function(d) {
		                           var o = {x: source.x0, y: source.y0};
		                             return diagonal({source: o, target: o});
	                                  });

                                    // Transition links to their new position.
                                    link.transition()
	                                .duration(duration)
	                                .attr("d", diagonal);

                                    // Transition exiting nodes to the parent's new position.
                                    link.exit().transition()
	                                .duration(duration)
	                                .attr("d", function(d) {
		                            var o = {x: source.x, y: source.y};
		                               return diagonal({source: o, target: o});
	                                    })
	                                  .remove();

                                    // Stash the old positions for transition.
                                    nodes.forEach(function(d) {
	                                d.x0 = d.x;
	                                d.y0 = d.y;
                                    });
                                  }

                                 // Toggle children on click.
                                 function click(d) {
                                  if (d.children) {
	                              d._children = d.children;
	                              d.children = null;
                                  } else {
	                              d.children = d._children;
	                              d._children = null;
                                  }
                                  update(d);
                                  }
									  

							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==6)
					{
						$('#top-companies-filters').hide();
						
						
						if(currentchartid!=previouschartid)
						{
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option><option value="map">Map</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart highlights the patent distribution among the different countries corresponding to all the patent families of relevant patents.");
							    $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  $.each(data, function(key, value)
									  {
                                              $.each(value, function(key, value){
                                                x.push(key);
										        y.push(value);
                                                });
                                       });
									 
								            globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'marketcountry','y':'count'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}

                                       
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==7)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option><option value="map">Map</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart highlights the patent distribution among the countries where the first patent corresponding to a specific patent family was filed."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cc);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'innovationcountry','y':'count'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==8)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart correlates the patent filings among various innovation countries with the filing dates."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].pdate);
										  y.push(data[i].cc);
										  z.push(data[i].z);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'innovationcountry'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									 
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
			        else if(chart==9)
					{
						$('#top-companies-filters').hide();
						var hlevel = $(this).data('hlevel');
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var level = $(this).data('toplevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel'))+"&hlevel="+hlevel+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html(" This chart represents a year-by-year filing in the domain of various types of "+decodeURIComponent(level)+"s.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].year);
										  y.push(data[i].indepthcategory);
										  z.push(data[i].count);
										  
									  }
									 
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','y':'categorisation','x':'pdate','hlevel':hlevel,'toplevel':level};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									 
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==10)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						var level = $(this).data('toplevel');
						var hlevel = $(this).data('hlevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel'))+"&hlevel="+hlevel,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart illustrates the patent distribution among the various "+decodeURIComponent(level)+"s."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].indepthcategory);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'categorisation','y':'count','hlevel':hlevel,'toplevel':level};
                                            											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}
									  

                                            
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==11)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="line">Line</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart illustrates the patent distribution among the various types of companies involved in the subject technology domain."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].typeofassignee);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'typeofassignee','y':'count'};	
											
											if(currentchartid==previouschartid)
											{
												changecharttype($("#switchchart").val(),charttitle);
											}
											else
											{
												changecharttype('pie',charttitle);
											}
								   
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==12)
					{
						
						$('#top-companies-filters').show();
						if(currentchartid!=previouschartid)
						{
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="line">Line</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var typeofassignee = $(this).data('typeofassignee');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&typeofassignee="+encodeURIComponent($(this).data('typeofassignee'))+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							  $('#insightdescription').html("This chart illustrates the dominant "+typeofassignee+"s filing patents in the subject technology domain.");
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'count'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==13)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var level = $(this).data('toplevel');
						var hlevel = $(this).data('hlevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel'))+"&topvalue="+$("#top-comp-opt").val()+"&hlevel="+hlevel,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							   
							    $('#insightdescription').html("This chart compares the patents filed by the different companies among various types of "+decodeURIComponent(level)+".s");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].indepthcategory);
										  z.push(data[i].count);
										  
									  }
									 
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'categorisation','hlevel':hlevel,'toplevel':level};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==14)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&topvalue="+$("#top-comp-opt").val()+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart presents a comparison among the year-by-year patent filings by various companies working in the subject technology domain.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  y.push(data[i].parentassignee);
										  x.push(data[i].pdate);
										  z.push(data[i].z);
										  
									  }
									 
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'assignee'};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									 
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==15)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var level = $(this).data('toplevel');
						var hlevel = $(this).data('hlevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel'))+"&scoringopt="+$('#scoring-opt').val()+"&activeauthopt="+$('#active-auth-opt').val()+"&hlevel="+hlevel+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart presents a year-by-year variation in the quality of the patents corresponding to various "+decodeURIComponent(level)+"s.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].year);
										  y.push(data[i].indepthcategory);
										  z.push(Math.round(data[i].score));
										  
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','y':'categorisation','x':'pdate','hlevel':hlevel,'toplevel':level};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									 
									 
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==16)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						var level = $(this).data('toplevel');
						var hlevel = $(this).data('hlevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel'))+"&scoringopt="+$('#scoring-opt').val()+"&activeauthopt="+$('#active-auth-opt').val()+"&topvalue="+$("#top-comp-opt").val()+"&hlevel="+hlevel,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart correlates the patent filings by various companies in various  "+decodeURIComponent(level)+"s with the quality of their portfolio size based on the various scoring parameters.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  y.push(data[i].indepthcategory);
										  z.push(Math.round(data[i].score));
										  
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','y':'categorisation','x':'assignee','hlevel':hlevel,'toplevel':level};	
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									 
									 
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==17)
					{
						$('#switch-charts').hide();
						$('#top-companies-filters').show();
						$('#insight-suboption').show();
						var typeofassignee = $(this).data('typeofassignee');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&typeofassignee="+encodeURIComponent($(this).data('typeofassignee'))+"&scoringopt="+$('#scoring-opt').val()+"&activeauthopt="+$('#active-auth-opt').val()+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							  $('#insightdescription').html("This chart presents the LEADerboard chart considering the major "+typeofassignee+"s filing patents in this domain.");
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									  
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].count);
										  y.push(data[i].score);
										  z.push(data[i].parentassignee);
									  }
									 
									        globalx = x;
											globaly  = y;
											globalz = z;
                                            globalmetadata ={'drilldown':'enable','x':'assignee','y':'typeofassignee','typeofassignee':typeofassignee};
							                changecharttype('leaderboard',charttitle);
								   }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==18)
					{
						$('#switch-charts').hide();
						$('#top-companies-filters').show();
						$('#insight-suboption').show();
						
						var typeofassignee = $(this).data('typeofassignee');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&typeofassignee="+encodeURIComponent($(this).data('typeofassignee'))+"&scoringopt="+$('#scoring-opt').val()+"&activeauthopt="+$('#active-auth-opt').val()+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							  $('#insightdescription').html("This chart presents the LEADerboard chart considering the overall major companies filing patents in this domain.");
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									  var colorsch=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].count);
										  y.push(data[i].score);
										  z.push(data[i].parentassignee);
									  }
									   
									        globalx = x;
											globaly  = y;
											globalz = z;
                                            globalmetadata ={'drilldown':'enable','x':'assignee'};
							                
											changecharttype('leaderboard',charttitle);
									 
                                   }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==20)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="3Dmap">Map</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							   
							   $("#insightdescription").html("THIS CHART HIGHLIGHTS THE PATENT DISTRIBUTION of various companies AMONG THE DIFFERENT COUNTRIES CORRESPONDING TO ALL THE PATENT FAMILIES OF RELEVANT PATENTS.");
								$('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  /*$.each(data, function(key, value)
									  {
                                              $.each(value, function(key, value){
                                                x.push(key);
										        y.push(value);
                                                });
                                       });*/
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cc);
										  z.push(data[i].value);
										  y.push(data[i].assignee);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'marketcountry','y':'assignee'};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
								    
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==19)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="3Dmap">Map</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("THIS CHART HIGHLIGHTS THE PATENT DISTRIBUTION of various companies AMONG THE COUNTRIES WHERE THE FIRST PATENT CORRESPONDING TO A SPECIFIC PATENT FAMILY WAS FILED."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cc);
										  z.push(data[i].count);
										  y.push(data[i].parentassignee);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'innovationcountry','y':'assignee'};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==21)
					{
						
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="scatter">Scatter</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("THIS CHART PRESENTS A DISTRIBUTION OF THE RELEVANT PATENT FAMILIES BASED ON THEIR OVERALL FAMILY LEGAL STATUS.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].legalstate);
										  y.push(data[i].count);
										  
									  }
								     
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'overalllegalstatus','y':'count'};
											if(currentchartid==previouschartid)
											{
												changecharttype($("#switchchart").val(),charttitle);
											}
											else
											{
												changecharttype('pie',charttitle);
											}
									 
								
								
							       }
								   else
								   {
									  $('#tester').html("<div>No result set found.</div>"); 
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==22)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html(""); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].typeofassignee);
										  z.push(data[i].count);
										  y.push(data[i].legalstate);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'typeofassignee','y':'overalllegalstatus'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==23)
					{
						
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("THIS CHART PRESENTS THE DISTRIBUTION OF THE PATENT FAMILIES ASSIGNED TO MAJOR PLAYERS BASED ON THE OVERALL FAMILY LEGAL STATUS."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  z.push(data[i].count);
										  y.push(data[i].legalstate);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'overalllegalstatus'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
									  
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==24)
					{
						$('#top-companies-filters').hide();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart presents the distribution of the patents among preferred market countries based on the overall legal status of their families."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cc);
										  y.push(data[i].value);
										  z.push(data[i].legalstate);
									  }
									  
									  TESTER = document.getElementById('tester');

                                        Plotly.plot( TESTER, 
										[{
                                         x: x,
                                         y: y,
                                         type:'bar',
										 text : z,
										 hovertemplate: "<b>%{text}</b> :%{y}<extra></extra>",
										 //textposition: 'auto',
										 transforms: [{
                                                      type: 'groupby',
                                                      groups: z,
													  
                                                      }]
										 }] ,
										 stackedlayout,
										 config);

                                      /* Current Plotly.js version */
                                       console.log( Plotly.BUILD );
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==25)
					{
						$('#top-companies-filters').hide();
						
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var level = $(this).data('toplevel');
						var hlevel = $(this).data('hlevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel'))+"&hlevel="+hlevel,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("");
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].indepthcategory);
										  z.push(data[i].count);
										  y.push(data[i].legalstate);
										  
									  }
									        
											globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','y':'overalllegalstatus','x':'categorisation','hlevel':hlevel,'toplevel':level};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==26)
					{
						$('#top-companies-filters').hide();
						
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="scatter">Scatter</option>');	
						}
						$('#switch-charts').show();
						
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart presents a distribution of all the family members based on their legal status.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].legalstate);
										  y.push(data[i].count);
										  
									  }
								     
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'familymemberslegalstatus','y':'count'};
											
											if(currentchartid==previouschartid)
											{
												changecharttype($("#switchchart").val(),charttitle);
											}
											else
											{
												changecharttype('pie',charttitle);
											}
								       
								
							       }
								   else
								   {
									  $('#tester').html("<div>No result set found.</div>"); 
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==27)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&topvalue="+$("#top-comp-opt").val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart presents the distribution of all the patent family members assigned to major players based on their legal status."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].parentassignee);
										  z.push(data[i].count);
										  y.push(data[i].legalstate);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'familymemberslegalstatus'};	
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==28)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							  $('#insightdescription').html("This chart presents the distribution of the patents among different market countries based on their legal status."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cc);
										  z.push(data[i].value);
										  y.push(data[i].legalstate);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'marketcountry','y':'familymemberslegalstatus'};	
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dbar',charttitle);
											}
									  
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==29)
					{
						
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="line">Line</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html("This chart represents an overall expiry trend in the subject technology domain.");
							    $('#processing-div').remove();
							   if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].expiryyear);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
												
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bar',charttitle);
											}
									  
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
					else if(chart==30)
					{
						$('#top-companies-filters').hide();
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var level = $(this).data('toplevel');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&level="+encodeURIComponent($(this).data('toplevel')),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
							    $('#insightdescription').html(" This chart represents a year-by-year expiry trend in the domain of various types of "+decodeURIComponent(level)+"s.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].year);
										  y.push(data[i].indepthcategory);
										  z.push(data[i].count);
										  
									  }
									 
									        globalx = x;
											globaly  = y;
											globalz = z;
												
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('bubble',charttitle);
											}
									 
							       }
								   else
								   {
									   $('#tester').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					}
				});
				
				
				$('body').on('click','#leftpaneltoggle',function(){
					if($('.mc-iv-left-outer').css('display')=='flex')
					{
						$('.mc-iv-left-outer').css({'display':'none'});
					}
					else
					{
						$('.mc-iv-left-outer').css({'display':'flex'});
					}
					window.dispatchEvent(new Event('resize'));
				});
				
				
				$('body').on('change','#scoring-opt',function(){
				if(showchart_after_applyingfilter!='')
					{
						$('#'+showchart_after_applyingfilter).trigger('click');
						
						
					}
				});
				
				$('body').on('change','#top-comp-opt',function(){
				if(showchart_after_applyingfilter!='')
					{
						$('#'+showchart_after_applyingfilter).trigger('click');
						
						
					}
				});
				
				$('body').on('change','#priority-year-opt',function(){
				if(showchart_after_applyingfilter!='')
					{
						$('#'+showchart_after_applyingfilter).trigger('click');
						
						
					}
				});
				
				$('body').on('change','#active-auth-opt',function(){
				if(showchart_after_applyingfilter!='')
					{
						$('#'+showchart_after_applyingfilter).trigger('click');
						
						
					}
				});
				

	
	
function exportchartdata()
{
	var x = new CSVExport(exportdata);
			return false;
}

function changecharttype(charttype,charttitle,chartposid,globalmetadata)
	   {
		            $('#insight-result-count-clickedpoint').html('');
					
		            var tracecolor= ['#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844','#f44336','#f85b47','#fc6f59','#ff826b','#ff947d','#ffa590','#ffb6a4','#ffc7b8','#ffd7cd','#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844'];
		            var layout = {uniformtext:{minsize:8, mode:'hide'},font: {family: 'Bauziet, Telegraf, Bahnschrift, sans-serif'},colorway : tracecolor,title:{text:charttitle.toUpperCase(),xref:'container',x:0,xanchor:'left',family: 'Bauziet', color:'#1E150B', size:24, pad:{l : 20}},paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1','yaxis':{'automargin': true,'tickfont':{}},'xaxis':{autotick: true,'categoryorder':'total descending','tickfont':{}},showlegend: false,hovermode: "closest",barmode: 'stack',annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}],geo: {bgcolor:'#F6EEE1',countrycolor:'#ccc',coastlinecolor:'#ccc',showframe: false,showcountries:true,projection: {type: 'robinson'}},margin: {}};
					
					var bubblelayout = {font: {family: 'Bauziet, Telegraf, Bahnschrift, sans-serif'},colorway : tracecolor,title:{text:charttitle.toUpperCase(),xref:'container',x:0,xanchor:'left',family: 'Bauziet', color:'#1E150B', size:24,pad:{l : 20}},paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1','yaxis': {autotick: false,'automargin': true,'tickfont':{}},'xaxis':{autotick: false,tickangle: 35,'tickfont':{}},showlegend: false,hovermode: "closest",annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}],margin: {}};
					
					<?php
					    if(isset($_SESSION["theme"]) && $_SESSION["theme"]!='')
			             {
				          if($_SESSION["theme"]=='blue')
				          {
					         ?>
							    bubblelayout.paper_bgcolor ='#f0efef';
								bubblelayout.plot_bgcolor ='#f0efef';
								layout.paper_bgcolor ='#f0efef';
								layout.plot_bgcolor ='#f0efef';
							 <?php
				          }
				  
			             }
					?>
					/*-------custom settings-----*/
					bubblelayout.xaxis.tickangle = $('#xaxislabelangle').val();
				    layout.xaxis.tickangle = $('#xaxislabelangle').val();
					
					bubblelayout.yaxis.tickangle = $('#yaxislabelangle').val();
				    layout.yaxis.tickangle = $('#yaxislabelangle').val();
					
					bubblelayout.xaxis.tickfont.size = $('#xaxislabelfont').val();
				    layout.xaxis.tickfont.size = $('#xaxislabelfont').val();
					
					bubblelayout.yaxis.tickfont.size = $('#yaxislabelfont').val();
				    layout.yaxis.tickfont.size = $('#yaxislabelfont').val();
						
					if($('#xaxislabel').prop('checked')==true)
					{
						bubblelayout.xaxis.automargin = true;
						layout.xaxis.automargin = true;
					}
					else
					{
						bubblelayout.xaxis.automargin = false;
						layout.xaxis.automargin = false;
					}
					
					if($('#yaxislabel').prop('checked')==true)
					{
						bubblelayout.yaxis.automargin = true;
					    layout.yaxis.automargin = true;
					}
					else
					{
						bubblelayout.yaxis.automargin = false;
						layout.yaxis.automargin = false;
					}
					
					var pvalue = $("input[name='palette']:checked").val();
					var paletteselected=[];
                    if(pvalue=='p1')
					{
                      bubblelayout.colorway = palette1;
					  layout.colorway = palette1;
					  paletteselected = palette1;
                    }
					else if(pvalue=='p2')
					{
                      bubblelayout.colorway = palette2;
					  layout.colorway = palette2;
					  paletteselected = palette2;
                    }
					else if(pvalue=='p3')
					{
                      bubblelayout.colorway = palette3;
					  layout.colorway = palette3;
					  paletteselected = palette3;
                    }
					else if(pvalue=='p4')
					{
                      bubblelayout.colorway = palette4;
					  layout.colorway = palette4;
					  paletteselected = palette4;
                    }
					else if(pvalue=='p5')
					{
                      bubblelayout.colorway = palette5;
					  layout.colorway = palette5;
					  paletteselected = palette5;
                    }
					else if(pvalue=='p6')
					{
                      bubblelayout.colorway = palette6;
					  layout.colorway = palette6;
					  paletteselected = palette6;
                    }
					else if(pvalue=='p7')
					{
                      bubblelayout.colorway = palette7;
					  layout.colorway = palette7;
					  paletteselected = palette7;
                    }
					/*---------------------------*/
					var stackedcolors = ['#F4BDAE','#F9E2B8','#FFF2D7','#C0EFF5','#53C6D9'];
					var stackedcolors2 = ["#4c78a8", "#f58518", "#e45756", "#72b7b2", "#54a24b", "#eeca3b", "#b279a2", "#ff9da6", "#9d755d", "#bab0ac"];
					var stackedlayout = {font: {family: 'Telegraf,Bahnschrift, sans-serif'},colorway : stackedcolors2,title:charttitle,paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1',barmode: 'stack',showlegend: true,hovermode: "closest",legend: {},xaxis:{'categoryorder':'total descending'},yaxis:{},annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}]};
					var ccdata = {"AM":"ARM","AR":"ARG","AT":"AUT","AU":"AUS","BA":"BIH","BE":"BEL","BG":"BGR","BR":"BRA","BY":"BLR","CA":"CAN","CH":"CHE","CL":"CHL","CN":"CHN","CO":"COL","CR":"CRI","CS":"CSK","CU":"CUB","CY":"CYP","CZ":"CZE","DD":"DDR","DE":"DEU","DK":"DNK","DO":"DOM","DZ":"DZA","EC":"ECU","EE":"EST","EG":"EGY","ES":"ESP","FI":"FIN","FR":"FRA","GB":"GBR","GE":"GEO","GR":"GRC","GT":"GTM","HK":"HKG","HN":"HND","HR":"HRV","HU":"HUN","ID":"IDN","IE":"IRL","IL":"ISR","IN":"IND","IS":"ISL","IT":"ITA","JO":"JOR","JP":"JPN","KE":"KEN","KG":"KGZ","KR":"KOR","KZ":"KAZ","LT":"LTU","LU":"LUX","LV":"LVA","MA":"MAR","MC":"MCO","MD":"MDA","ME":"MNE","MN":"MNG","MO":"MAC","MT":"MLT","MW":"MWI","MX":"MEX","MY":"MYS","NI":"NIC","NL":"NLD","NO":"NOR","NZ":"NZL","PA":"PAN","PE":"PER","PH":"PHL","PL":"POL","PT":"PRT","RO":"ROU","RS":"SRB","RU":"RUS","SA":"SAU","SE":"SWE","SG":"SGP","SI":"SVN","SK":"SVK","SM":"SMR","SV":"SLV","TH":"THA","TJ":"TJK","TN":"TUN","TR":"TUR","TT":"TTO","TW":"TWN","UA":"UKR","US":"USA","UY":"URY","UZ":"UZB","VN":"VNM","YU":"YUG","ZA":"ZAF","ZM":"ZMB","ZW":"ZWE"};
					var config = {
						           displaylogo: false,
					               displayModeBar: false,
								   responsive: true, 
								   modeBarButtonsToRemove:["zoom2d", "pan2d", "select2d", "lasso2d", "zoomIn2d", "zoomOut2d", "autoScale2d", "resetScale2d", "hoverClosestCartesian", "hoverCompareCartesian", "zoom3d", "pan3d", "resetCameraDefault3d", "resetCameraLastSave3d", "hoverClosest3d", "orbitRotation", "tableRotation", "zoomInGeo", "zoomOutGeo", "resetGeo", "hoverClosestGeo", "sendDataToCloud", "hoverClosestGl2d", "hoverClosestPie", "toggleHover", "resetViews", "toggleSpikelines", "resetViewMapbox"],
								   modeBarButtonsToAdd: [
                                                         /*{
                                                          name: 'Comments',
                                                          icon: {'svg':'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="grey" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'},
                                                          direction: 'up',
                                                          click: function(gd) 
	                                                           { 
	                                                           var url = "viewcomment.php?reportid="+reportid+"&chartid="+showchart_after_applyingfilter;
	                                                           window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=300,left=500,width=400,height=350");
						                                       }
						                                 },
														 {
                                                          name: 'Export Chart Data',
                                                          icon: {'svg':'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M13 12h3l-4 4-4-4h3V8h2v4zm2-8H5v16h14V8h-4V4zM3 2.992C3 2.444 3.447 2 3.999 2H16l5 5v13.993A1 1 0 0 1 20.007 22H3.993A1 1 0 0 1 3 21.008V2.992z"/></svg>'},
														  direction: 'up',
                                                          click: function(gd) 
	                                                           { 
	                                                               exportchartdata();
						                                       }
						                                 }*/
														 ]
				                 };
		   
		   
									  $('#tester').remove();
					                  //$('#middle-container-left-wrapper').append("<div class='insight-card'><div class='insight-card-inner' id='"+chartposid+"'></div></div>");
									  TESTER = document.getElementById(chartposid);
									  
									 
									  
									  if(charttype=='line')
									  {
                                      var data = [{
                                                  x: globalx,
                                                  y: globaly,
                                               //type:'bar',
											   mode:'lines+markers+text',
											   line: {shape: 'spline'},
                                              text : globaly,
									   textposition: 'top',											   
											      }]; 
												  
                                             Plotly.plot(
									              TESTER, 
												  data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
														$("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
													},1000);
													  });
													  
												  }
									  }
									  else if(charttype=='scatter')
									  {
                                      var data = [{
                                                  x: globalx,
                                                  y: globaly,
                                               type:'scatter',
											   marker:{
												   symbol:'circle',
												   size:19
											   },
											   mode:'markers+text',
                                              text : globaly,
									   textposition: 'auto',											   
											      }];
                                      Plotly.plot(
									              TESTER, 
												  data,
												  layout,
												  config);
										if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													   $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													  setTimeout(function(){	  
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
                                                      },1000);
													  });
													  
												  }		  
									  }
									  else if(charttype=='3Dline')
									  {
                                      layout.showlegend = true;
									  var data = [{
                                                  x: globalx,
                                                  y: globalz,
												  customdata : globaly,
                                               type:'scatter',
											   mode:'lines+markers',
											   line: {shape: 'spline'},
                                              //text : globaly,
									   textposition: 'top',	
                                             transforms: [{
                                              type: 'groupby',
                                              groups: globaly,
                                               }]									   
											      }];
                                      Plotly.plot(
									              TESTER, 
												   data,
												  layout,
												  config);
												  
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													   setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].customdata;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
													   },1000);
													  });
													  
												  }
									  }
									  else if(charttype=='3Dscatter')
									  {
                                      layout.showlegend = true;
									  var data = [{
                                                  x: globalx,
                                                  y: globalz,
                                               type:'scatter',
											   mode:'markers',
                                              text : globalz,
									   textposition: 'top center',	
                                             transforms: [{
                                              type: 'groupby',
                                              groups: globaly,
                                               }]									   
											      }];
                                      Plotly.plot(
									              TESTER, 
												   data,
												  layout,
												  config);
									  }
									  else if(charttype=='bar')
									  {
										  var data = [{
                                                  x: globalx,
                                                  y: globaly,
                                               type:'bar',
											   opacity:0.8,
											 //marker:{color: Array(globalx.length).fill(paletteselected).flat().splice(0, globalx.length)/*paletteselected'colorscale': 'Viridis','color':globaly*/},
                                              text : globaly,
									   textposition: 'auto',											   
											      }];
										  Plotly.plot(
									              TESTER, 
												   data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
														$("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													   setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
													   },1000);
													  });
													  
												  }
												  
									  }
									  else if(charttype=='hbar')
									  {
										  layout.yaxis.categoryorder = 'total ascending';
										  layout.yaxis.automargin = true;
										  var data = [{
                                                  x: globaly,
                                                  y: globalx,
                                               type:'bar',
											   opacity:0.8,
											   orientation: 'h',
											   mode:'lines+markers',
											   //marker:{color: Array(globalx.length).fill(paletteselected).flat().splice(0, globalx.length)/*paletteselected'colorscale': 'Viridis','color':globaly*/},
                                              text : globaly,
									   texttemplate:"%{x:.2f}",
									   textposition: 'auto',											   
											      }];
										  Plotly.plot(
									              TESTER, 
												   data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													  setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
                                                      },1000);
													  });
													  
												  }
									  }
									  else if(charttype=='3Dbar')
									  {
										  layout.showlegend = true;
										  var data = [{
                                                  x: globalx,
                                                  y: globalz,
												  //text: globaly,
                                               type:'bar',
											   mode:'lines+markers',
											   opacity:0.8,
											  text : globaly,
									  hovertemplate: "<b>%{x}</b> :%{y}(%{text})<extra></extra>",
									   textposition: 'auto',
                                            transforms: [{
                                              type: 'groupby',
                                              groups: globaly,
                                               }]												   
											      }]; 
										  Plotly.plot(
									              TESTER, 
												  data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													  setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].text;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
                                                      },1000);
													  });
													  
												  }
									  }
									  else if(charttype=='h3Dbar')
									  {
										  layout.yaxis.categoryorder = 'total ascending';
										  layout.showlegend = true;
										  layout.yaxis.automargin = true;
										  
										  var data = [{
                                                  x: globalz,
                                                  y: globalx,
                                               type:'bar',
											   orientation:'h',
											   opacity:0.8,
											   mode:'lines+markers',
                                              text : globaly,
									  hovertemplate: "<b>%{x}</b> :%{y}(%{text})<extra></extra>",
									   textposition: 'auto',
                                            transforms: [{
                                              type: 'groupby',
                                              groups: globaly,
                                               }]												   
											      }];
										  Plotly.plot(
									              TESTER, 
												   data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													  setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].text;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
                                                      },1000);
													  });
													  
												  }
									  }
									  else if(charttype=='pie')
									  {
										 layout.showlegend = true;
										 var data = [{
                                         values: globaly,
                                         labels: globalx,
                                         type:'pie'	
										 }];
										 Plotly.plot( 
								         TESTER, 
										 data,
										 layout,
										 config);
										 if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
														$("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													    setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].label;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].value;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
														},1000);
													  });
													  
												  }
										 
									  }
									  else if(charttype=='bubble')
									  {
										  
										   var maxbubblesize = Math.max.apply(Math, globalz);	
									        var   bubblesizeset = $('#bubbleresizer').val();
											//TESTER = document.getElementById('tester');
											
											var data = [{ 
                                         x: globalx,
                                         y: globaly,
										 z:globalz,
										 text: globalz,
										 mode:'markers+text',
										 marker:{size:globalz, sizeref : 2*maxbubblesize/(bubblesizeset**2), sizemode:'area',sizemin:10},
										 transforms: [{
                                              type: 'groupby',
                                              groups: globaly,
                                               }]
										   
										   }];
									       Plotly.plot( TESTER, 
									       data,
										   bubblelayout,
										   config);
										   
										   if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													  setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
                                                      },1000);
													  });
													  
												  }
									  }
									  else if(charttype=='heatmap')
									  {
										  
										  for ( var i = 0; i < globalx.length; i++ ) 
										  {
											  var result = {
                                                        xref: 'x1',
                                                        yref: 'y1',
                                                        x: globalx[i],
                                                        y: globaly[i],
                                                        text: globalz[i],
                                                        font: 
														{
                                                        family: 'Arial',
                                                        size: 12,
                                                        color: 'rgb(50, 171, 96)'
                                                        },
                                                   showarrow: false,
                                                   font: {
                                                         color: 'black'
                                                         }
                                                          };
                                             bubblelayout.annotations.push(result);
  
                                          }

                                    var colorscaleValue = [
                                              [0, '#f9e3bf'],
                                              [1, '#EF3C18']
                                                           ];
														   
										var data = 	[{ 
                                         x: globalx,
                                         y: globaly,
										 z:globalz,
										 type: 'heatmap',
										 hoverongaps: false,
										 colorscale: colorscaleValue,
                                         showscale: false,
  
										 text: globalz,
										 textposition: 'top center',
										  
										 mode:'text',
										 
										   
										   }];			   
										Plotly.plot( TESTER, 
									       data,
										   bubblelayout,
										   config);
										   
										   if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													 setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].x;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].y;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
													 },1000);
													  });
													  
												  }
									  }
									  else if(charttype=='map')
									  {
										  layout.margin.l = 15;
										  layout.margin.r = 15;
										  layout.margin.b = 10;
										  layout.margin.t = 40;
										  layout.margin.pad = 4;
										  
										  var cc =[];
										  var count =[];
										  var customdata=[];
										  
										  for ( var i = 0; i < globalx.length; i++ ) 
										      {
												  
												  if(typeof ccdata[globalx[i]]!== 'undefined')
												  {
											      cc[i] = ccdata[globalx[i]];
												  count[i] = globaly[i];
												  customdata[i] = globalx[i];
												  }
												  
                                              }
										  
										  var data = [{
                                                    locations: cc,
                                                    type: 'choropleth',
									                locationmode: 'ISO-3',
											        z : count,
													customdata: customdata,
											        marker: {
                                                    line: {
                                                      color: '#ccc',
                                                      width: 1
                                                          }
                                                             },
									                autocolorscale: true
											      }];
										  Plotly.newPlot(
									              TESTER, 
												   data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													$("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].customdata;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = data.points[i].z;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
													},1000);
													  });
													  
												  }
									  }
									  else if(charttype=='3Dmap')
									  {
										  layout.showlegend = true;
										  layout.margin.l = 15;
										  layout.margin.r = 15;
										  layout.margin.b = 10;
										  layout.margin.t = 40;
										  layout.margin.pad = 4;
										  
										  var maxbubblesize = Math.max.apply(Math, globalz);  
										  
										  var cc =[];
										  var count =[];
										  var assignee =[];
										  for ( var i = 0; i < globalx.length; i++ ) 
										      {
												  
												  if(typeof ccdata[globalx[i]]!== 'undefined')
												  {
											      cc[i] = ccdata[globalx[i]];
												  count[i] = globalz[i];
												  assignee[i] = globaly[i];
												  }
												  
                                              }
										  
										  var data = [{
                                                    locations: cc,
                                                    type: 'scattergeo',
											        locationmode: 'ISO-3',
											        z : count,
											        marker:{size:globalz, sizeref : 2*maxbubblesize/(50**2), sizemode:'area',symbol:'triangle-up'},
											        transforms: [{
                                                    type: 'groupby',
                                                  groups: assignee,
                                               }],
									 //autocolorscale: true
											      }];
										  Plotly.plot(
									              TESTER, 
												   data,
												  layout,
												  config);
									  }
									  else if(charttype=='leaderboard')
									  {
										  
										  var colorsch=[];
										  var divisor = Math.max.apply(Math, globaly);
									      
										  for(var i = 0, length = globaly.length; i < length; i++)
									      {
                                              globaly[i] = (globaly[i]/divisor)*10;
                                          }
										  
										  var divisor = Math.max.apply(Math, globalx);  
										  
										  for(var i = 0, length = globalx.length; i < length; i++)
									      {
                                              globalx[i] = (globalx[i]/divisor)*10;
											  
											  if(globalx[i]<5 && globaly[i]<5)
											  {
												  colorsch.push('red');
											  }
											  else if(globalx[i]>=5 && globaly[i]<5)
											  {
												  colorsch.push('orange');
											  }
											  else if(globalx[i]<5 && globaly[i]>=5)
											  {
												  colorsch.push('#8B8000');
											  }
											  else if(globalx[i]>=5 && globaly[i]>=5)
											  {
												  colorsch.push('blue');
											  }
                                          }
                                        
										var modevalue ='markers';
										if($('#leaderboardtext').prop("checked")==true)
											{
												modevalue = 'markers+text';
											}
											else
											{
												modevalue = 'markers';
											}
											
										var data = [{
											
									     mode : modevalue,
									     type: 'scatter',
                                         x: globalx,
                                         y: globaly,
										 text : globalz,
										 textposition: 'right center',
										 textfont: {
                                          size:  6
                                            },
										 hovertemplate: "<b>%{text}</b><br>Normalized Portfolio Size:%{x}<br>Normalized Portfolio Strength:%{y}<extra></extra>",
										 marker:{color:colorsch,size:12,opacity:0.5,line:{color:'white',width:1}} 
										   }];
										   
										   
                                        Plotly.plot(
										TESTER, 
										data,
										   {font: {family: 'Bauziet, Telegraf,Bahnschrift, sans-serif'},
										    colorway : tracecolor,title:{text:charttitle.toUpperCase(),xref:'container',x:0,xanchor:'left',family: 'Bauziet', color:'#1E150B', size:24, pad:{l : 20}},paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1',
											yaxis: {automargin: true,showgrid : false,zeroline : true, title:'Score >>'},
											xaxis: {showgrid : false, zeroline : true, title:'Portfolio size >>'},
											showlegend: false,hovermode: "closest",
											shapes: [
											//line vertical
											{
                                              type: 'line',
                                              x0: 5,
                                              y0: 0,
                                              x1: 5,
                                              y1: 10,
                                              line: {
                                                    color: '#ddd',
                                                    width: 1
                                                    }
                                            },
                                            //Line Horizontal
                                            {
                                             type: 'line',
                                             x0: 0,
                                             y0: 5,
                                             x1: 10,
                                             y1: 5,
                                             line: 
											 {
                                              color: '#ddd',
                                              width: 1,
                                             }
                                             }], 
	                                         annotations: [
											 {
                                                 xref: 'paper',
                                                 yref: 'paper',
                                                 x: 0,
                                                 xanchor: 'left',
                                                 y: 1,
                                                 yanchor: 'bottom',
                                                 text: 'Advanced innovators',
                                                 showarrow: false
                                              }, 
											  {
                                                 xref: 'paper',
                                                 yref: 'paper',
                                                 x: 1,
                                                 xanchor: 'right',
                                                 y: 0,
                                                 yanchor: 'bottom',
                                                 text: 'Extended innovators',
                                                 showarrow: false
                                               },
                                               {
                                                  xref: 'paper',
                                                  yref: 'paper',
                                                  x: 0,
                                                  xanchor: 'left',
                                                  y: 0,
                                                  yanchor: 'bottom',
                                                  text: 'Delayed innovators',
                                                  showarrow: false
                                               },
                                               {
                                                  xref: 'paper',
                                                  yref: 'paper',
                                                  x: 1,
                                                  xanchor: 'right',
                                                  y: 1,
                                                  yanchor: 'bottom',
                                                  text: 'Leading innovators',
                                                  showarrow: false
                                                },
												{
													xref: 'paper',
													yref: 'paper',
													x: 1,
													xanchor: 'left',
													y: 0,
													yanchor: 'top',
													text: '&#169; iCuerious',
													showarrow: false,
													opacity:0.3
												}
												]}, 
						                   config);
                                           
										   if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
												   $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               var pointclicked_xcol = globalmetadata.x;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_xcol));
		                                               var pointclicked_xvalue = data.points[i].text;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.y !== "undefined")
													   {
		                                               var pointclicked_ycol = globalmetadata.y;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_ycol));
		                                               var pointclicked_yvalue = globalmetadata.typeofassignee;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_yvalue));
													   }
                                                       }
	
	                                                   var redirecturl = 'showdata_trial.php?&'+ pts.join('&')+"&"+ptsval.join('&')+"&action=showdata&reportid="+reportid+"&"+fd;
	                                                   if(typeof globalmetadata.x !== "undefined")
													   {
														   redirecturl = redirecturl+"&hlevel="+globalmetadata.hlevel+"&toplevelcat="+globalmetadata.toplevel;
													   }
                                                       $("#insight-result-count-clickedpoint").html("<a target='_blank' id='insightshowdatabtn' href="+redirecturl+">Show Data</a>");
                                                       window.dispatchEvent(new Event('resize'));
													},1000);
													  });
													  
												  }
									  }
	   }
	   
	   $('#switchchart').on('change',function(){
		   changecharttype($(this).val(),charttitle);
	   });
	   
	   $('#xaxislabel,#yaxislabel,#xaxislabelangle,#xaxislabelfont,#yaxislabelfont,#yaxislabelangle,#bubbleresizer').on('change',function(){
		   changecharttype($('#switchchart').val(),charttitle);
	   });
	   
	   $("#closelayoutbtn").on('click',function(){
		   $('.full-screen').first().addClass('hidden-outer');
	   });
	   
	   $("#layoutsettings").on('click',function(){
		   $('.full-screen').first().removeClass('hidden-outer');
	   });
	   
	   var palette1 = ['#696969','#556b2f','#8b4513','#228b22','#483d8b','#008b8b','#9acd32','#00008b','#8fbc8f','#8b008b','#b03060','#ff4500','#ff8c00','#ffd700','#7cfc00','#deb887','#00ff7f','#dc143c','#00ffff','#00bfff','#0000ff','#a020f0','#1e90ff','#fa8072','#90ee90','#add8e6','#ff1493','#7b68ee','#ee82ee','#ffc0cb'];			 
	   for ( var i = 0; i < palette1.length; i++ ) 
	   {
		   $('#p1').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette1[i]+"'></div>");
	   }
	 var palette2 = ['#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844','#f44336','#f85b47','#fc6f59','#ff826b','#ff947d','#ffa590','#ffb6a4','#ffc7b8','#ffd7cd','#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844'];
	   for ( var i = 0; i < palette2.length; i++ ) 
	   {
		   $('#p2').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette2[i]+"'></div>");
	   }
	   var palette3 = ['#228b22','#00008b','#b03060','#ff4500','#ffff00','#deb887','#00ff00','#00ffff','#ff00ff','#6495ed'];
	   for ( var i = 0; i < palette3.length; i++ ) 
	   {
		   $('#p3').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette3[i]+"'></div>");
	   }
	   var palette4 = ['#003f5c','#2f4b7c','#665191','#a05195','#d45087','#f95d6a','#ff7c43','#ffa600'];
	   for ( var i = 0; i < palette4.length; i++ ) 
	   {
		   $('#p4').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette4[i]+"'></div>");
	   }
	   var palette5 = ['#004c6d','#25617e','#3f768f','#588ca0','#72a3b2','#8cb9c4','#a8d0d7','#c4e7ea','#e1ffff'];
	   for ( var i = 0; i < palette5.length; i++ ) 
	   {
		   $('#p5').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette5[i]+"'></div>");
	   }
       var palette6 = ["#ffa842","#663dc2","#dcc730","#9493ff","#3c7700","#d3007b","#8bd97d","#b80026","#01d0bb","#ff753b","#01c9e6","#bc6000","#016eb0","#ff9c4d","#ffa2fa","#007549","#894000","#fdb68f","#715600","#905c41"];
       for ( var i = 0; i < palette6.length; i++ ) 
	   {
		   $('#p6').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette6[i]+"'></div>");
	   }
	   var palette7 = ['#F3613A','#F58668','#FBC3B4','#615A52','#887F78','#C9BEAA','#CBCC64','#63CE80','#95CDCE','#F2A7B3','#E25476','#AF4B72','#7F407F','#D82550','#EF3C18','#F2613A','#F58768','#FBC3B3','#C8BEAA','#887F77','#615A53','#E3C399','#CACC64','#EDCC3E','#ED993E','#A1673D','#758452','#A0B762','#ACD354','#BDF464','#A5EEA1','#BDE0BA','#A4C6A0','#4D9F49','#63CE7F','#84D8A6','#CC69B2','#CE9BCE','#9483EC','#AF68DD','#383887','#6B609D','#5252BF','#309F9D','#95CCCE','#BCDCE5','#49A3F9','#31C8F9','#30FBF9'];
                <?php
					    if(isset($_SESSION["theme"]) && $_SESSION["theme"]!='')
			             {
				          if($_SESSION["theme"]=='blue')
				          {
					         ?>
							    palette7 = ['#009cdd','#002652','#335377','#F3613A','#F58668','#FBC3B4','#615A52','#887F78','#C9BEAA','#CBCC64','#63CE80','#95CDCE','#F2A7B3','#E25476','#AF4B72','#7F407F','#D82550','#EF3C18','#F2613A','#F58768','#FBC3B3','#C8BEAA','#887F77','#615A53','#E3C399','#CACC64','#EDCC3E','#ED993E','#A1673D','#758452','#A0B762','#ACD354','#BDF464','#A5EEA1','#BDE0BA','#A4C6A0','#4D9F49','#63CE7F','#84D8A6','#CC69B2','#CE9BCE','#9483EC','#AF68DD','#383887','#6B609D','#5252BF','#309F9D','#95CCCE','#BCDCE5','#49A3F9','#31C8F9','#30FBF9'];
							 <?php
				          }
				  
			             }
					?>
	   for ( var i = 0; i < palette7.length; i++ ) 
	   {
		   $('#p7').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette7[i]+"'></div>");
	   }
        $(".palette").click(function(){
            var radioValue = $("input[name='palette']:checked").val();
            if(radioValue){
                changecharttype($('#switchchart').val(),charttitle);
            }
        });
		
		function showsettingbtn(chart)
		{
			
			if(chart!=5 && chart!=17 && chart!=18)
			{
				$('#layoutsettings').show();
			}
			else
			{
				
				$('#layoutsettings').hide();
			}
		}
		
		function showpdatefilter(chart)
		{
			
			if(chart==3 || chart==8 || chart==9 || chart==14 || chart==15)
			{
				$('#pdate-filter').show();
			}
			else
			{
				
				$('#pdate-filter').hide();
			}
		}
		
		function showleadercheckbox(chart)
		{
			if(chart==17 || chart==18)
			{
				$('#leaderboard-checked-text').show();
			}
			else
			{
				
				$('#leaderboard-checked-text').hide();
			}
		}
		
		 $('#leaderboardtext').on('change',function(){
		   changecharttype('leaderboard',charttitle);
	   });
		
		$('body').on('click','#open-max-window',function(){
			   //$(this).hide();
			   //$('#close-max-window').show();
			   openFullscreen();
		   });
		   
		   $('body').on('click','#close-max-window',function(){
			   $(this).hide();
			   $('#open-max-window').show();
			   exitFullscreen();
		   });
var elem = document.getElementsByClassName("mc-iv-right")[0];

function openFullscreen() {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
}

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    }
}

$('.gotoexternal').on('click',function(){
			window.open('exportscoring.php?reportid='+reportid, '_blank');
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
	
  