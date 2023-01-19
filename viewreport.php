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
		   $sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus,r.report_ipccpc,r.report_headquarter from reports r, reportallocation allo where r.rid = ? && r.rid=allo.rid && allo.email='".$_SESSION["clientemail"]."'";
		   }
		   else if($typeofuser =='user')
		   {
			 $sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus,r.report_ipccpc,r.report_headquarter from reports r, reportallocation allo where r.rid = ? && r.uploadedby='".$_SESSION["clientemail"]."'";   
		   }
		   elseif($typeofuser =='admin')
		   {
			  $sql ="select r.rid,r.title,r.type,r.creationtime,r.report_relevancy,r.report_scoring,r.report_categorization,r.report_typeofassignee,r.report_familymembers_legalstatus,r.report_ipccpc,r.report_headquarter from reports r where r.rid = ?";    
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
		   mysqli_stmt_bind_result($stmt,$rid,$title,$type,$creationtime,$report_relevancy,$report_scoring,$report_categorization,$report_typeofassignee,$report_familymembers_legalstatus,$report_ipccpc,$report_headquarter);	
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
			   $reportmetainfo['ipccpc'] = $report_ipccpc;
			   $reportmetainfo['headquarter'] = $report_headquarter;
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
	<link href="css/skins/minimal/red.css" rel="stylesheet">
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
  <div class='header'><button  class='actionbtn'  style='float:right;'><a target='_blank' id='exportbtn' href='exportresultset.php?reportid=<?php echo$_GET["reportid"]?>' title='Export Resultset In Excel Sheet'>Export</a></button><button title='Show result set' class='actionbtn' id='bcktolst' style='float:right;'>Result set</button><button title='show/hide filters' class='actionbtn filter-area-not-expanded' id='showfilters' style='float:right;'>Filters</button><button title='show/hide comments' class='actionbtn comment-area-not-expanded' id='showcomments' style='float:right;'>Comments</button><button title='show insights' class='actionbtn' id='showinsights' style='float:right;'>Insights</button><button  class='actionbtn'  style='float:right;'><a id='showoverallinsights' title='A bird eye view of quick insights' target='_blank' href='insightoverview.php?reportid=<?php echo$_GET["reportid"]?>' title=''>Insight Synopsis</a></button></div>
  <div id='middle-container-outer-wrapper'>
  <div id='middle-container-left-wrapper'>
  <div id='middle-container' class='middle-container'>
  <div class='mc-view-publication-inner'>
    <div class='mc-left'>
	    <div class='mc-left-pubno'>Publication No.<br/><span id='pubno'></span> <span id='relevancy'></span><span id='pubtagging'></span><div id='alllinks'></div></div>
		<div class='mc-left-biblio'>
		        <div class='sticky'>Bibliography</div>
				<div id='biblio'></div>
        </div>
		<div class='mc-left-family'>
		        <div class='sticky'>Family</div>
				<div id='inpadocfamily'></div>
        </div>
		<div class='mc-left-abstract'>
		      <div class='sticky'>Abstract</div>
			  <div id='abstract'></div>
        </div>
		
	</div>
	<div class='mc-middle'>
	      <!--<div class='mc-middle-nav'>
		       <div id="filterContainer">
                     <button class="filterbtn active">Claims</button>
                     <button class="filterbtn">Relevant</button>
               </div>
		  </div>
	      <div class='mc-middle-relevant-claim'>
		        <div class='sticky'>Relevant Claims</div>
		  </div>-->
		  <div class='mc-middle-all-claim'>
		       <div class='sticky'>All Claims <button id='saveclaims' data-pid='' class='actionbtn'>Save</button></div>
			   <div id='claims'></div>
		  
		  </div>
		  
		  
	</div>
	<?php if($reportmetainfo['categorization']==1)
	{?>
	<div class='mc-right'>
	    <div class='mc-right-cat'> 
		     <div class='sticky'>Categorisation <span style='float:right;cursor:pointer;margin-right:15px;' id='addcatbtn' class='category-area-not-expanded' title='Add Category'><i class="fa fa-plus" aria-hidden="true"></i></span></div>
			 <div id='categorisation'></div>
	    </div>
		<div class='mc-right-tagging'>
		     <!--<div class='sticky'>Tagging</div>-->
	    </div>
	</div>
	<?php
	}
	?>
</div>
<!--<div class='mc-pubview-navigation'>
    
</div>-->
  </div>
  <div id='middle-container-consolidate-view' class='middle-container-consolidate-view visibility-true'>
     <div class='mc-cv-list-view'>
	
	 <table width='100%'>
	 <thead><th>Sr. No.</th><th class='headcol' style="">Publication Number</th><th style='width:300px;'>Title</th><th>App. Date</th><th>Priority Date</th><th>Publication Date</th><th>Applicant/Assignee</th><th>Archieve Date</th><th>Tagging</th></thead>
	 <tbody id='relevantpatents'>
	 

	 </tbody>
	 </table>
	 </div>
	 <!--<div class='mc-cv-page-navigation'>
	 
	 </div>-->
	 
  </div>
  <div id='middle-container-filter-view' class='middle-container-filter-view'>
  <div class='mc-cv-filters'>
   
  </div>
  </div>
  <div class='middle-container-insight-view' id='middle-container-insight-view'>
   <div class='mc-iv-left-outer'>
      <div class='mc-iv-left'>
	    <ul class="file-tree">
		      <li>
			     <div class='insight-group show-help' data-tipso='This section presents a brief overview of our analysis.'>INTRODUCTION TO OUR ANALYSIS</div>
		          <ul>
				  <?php if($reportmetainfo['categorization']==1)
					 {
				      echo"<li><div class='getinsight' id='ins-3' data-chart='5'>Taxonomy Chart</div></li>";
					 }?>
				 <?php if($reportmetainfo['relevancy']==1)
					 {
		             echo"<li><div class='getinsight' id='ins-2' data-chart='2'>Relevancy</div></li>";
					 }?>
				  </ul>
			  </li>
		</ul>
	    
	    <ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section underlines the industry’s focus by presenting the patent distribution in various technology areas/ sub-categories considered for the analysis.">TECHNOLOGY LANDSCAPE</div>
		          <ul>
				  <?php if($reportmetainfo['categorization']==1){?>
				     <li><div class='insight-group show-help' data-tipso="This section illustrates the patent distribution among the various technology areas considered for the analysis. These charts may help identify the focused technology areas in the subject technology domain.">MAJOR TECHNOLOGY FOCUS</div>
					     <ul>
						     <?php
		                        for($i=0;$i<count($child);$i++)
		                           {
			                        echo "<li><div data-hlevel='".$hirearchylevel[$i]."' class='getinsight ".$hirearchylevel[$i]."' id='ins-patact-".$i."' data-chart='10' data-toplevel='".rawurlencode($child[$i])."'>".$child[$i]."</div></li>";
					               }
		                      ?>
						 </ul>
				     </li>
				  <?php }?>
					 <li><div class='insight-group show-help' data-tipso="This section indicates the pace of innovation by correlating the patent filings in various technology areas/ sub-categories with the filing dates.">PACE OF INNOVATION</div>
					     <ul>
						      <li><div class='getinsight' id='ins-4' data-chart='3'>Overall filing trends</div></li>
						     <?php
							 if($reportmetainfo['categorization']==1)
							   {
		                         for($i=0;$i<count($child);$i++)
		                         {
			                       echo "<li><div data-hlevel='".$hirearchylevel[$i]."' class='getinsight ".$hirearchylevel[$i]."' id='ins-tech-".$i."' data-chart='9' data-toplevel='".rawurlencode($child[$i])."'>".$child[$i]."</div></li>";
					             }
							   }
		                     ?>
                             <?php
							  if($reportmetainfo['type']=='FTO')
							  {?>
                              <li><div class='getinsight' id='ins-29' data-chart='29'>Expiry trends</div></li>							 
						      <?php
							 if($reportmetainfo['categorization']==1)
							   {
		                         for($i=0;$i<count($toplevelarray);$i++)
		                         {
			                       echo "<li><div class='getinsight' id='ins-expirytech-".$i."' data-chart='30' data-toplevel='".rawurlencode($toplevelarray[$i])."'>".$toplevelarray[$i]."(Expiry Trend)</div></li>";
					             }
							   }
		                     ?>
							  <?php
							  }
							  ?>
							  
						 </ul>
				     </li>
		          </ul>
			  </li>
		</ul>
	      
	    <ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section highlights the dominant companies and also presents a complete supply chain in the subject technology domain by highlighting the business nature/ type of major companies working in the domain.">MAJOR INNOVATORS & SUPPLY CHAIN ANALYSIS</div>
		          <ul>
				      <?php if($reportmetainfo['typeofassignee']==1){?>
				      <li><div class='getinsight' id='ins-9' data-chart='11'>Business type of assignees</div></li>
                      <?php }?>
					  <li><div class='getinsight' id='ins-8' data-chart='4'>Major assignees (Overall)</div></li>
					                                                     <?php
																		 if($reportmetainfo['typeofassignee']==1)
																		 {
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 $sql ="SELECT distinct typeofassignee FROM relevantpatents where rid='".$reportmetainfo['rid']."'";
																			
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <?php echo " <li><div class='getinsight' id='ins-assignee-".$i."' data-chart='12' data-typeofassignee='".$row['typeofassignee']."'>".$row['typeofassignee']."</div></li>"?>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
																		 }
																	   ?>
						 <li><div class='getinsight' id='ins-comp-pr-14' data-chart='14'>Companies vs Priority year</div>	</li>
						 <?php if($reportmetainfo['typeofassignee']==1){?>
				         <li><div class='getinsight' id='ins-typeassignee-pr-14' data-chart='35'>Business type of assignees vs Priority year</div>	</li>
                        <?php }?>
						
						
						 <?php if($reportmetainfo['categorization']==1){?>
						 <li>
						    <div class='insight-group show-help' data-tipso="This section compares the patent filings by various companies in various technology areas/ sub-categories.">Technological areas vs. Companies</div>
							<ul>
							    <?php
		                          for($i=0;$i<count($child);$i++)
		                          {
			                       echo "<li><div data-hlevel='".$hirearchylevel[$i]."' class='getinsight ".$hirearchylevel[$i]."' id='ins-comp-".$i."' data-chart='13' data-toplevel='".rawurlencode($child[$i])."'>".$child[$i]."</div></li>";
					   
		                          }
		                        ?>  
							</ul>
                         </li>	
                         <?php }?>							 
				  </ul>
			  </li>
		</ul>
		
		<?php if(($reportmetainfo['typeofassignee']==1) && ($reportmetainfo['headquarter']==1)){?>
		<ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section highlights the different regions of the players working in this domain based on the number of patents owned by the different region companies">Focused Region- Companies region analysis (Based on players Patent Share)</div>
		          <ul> 
				                                                 <?php
																		 if($reportmetainfo['typeofassignee']==1)
																		 {
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 $sql ="SELECT distinct typeofassignee FROM relevantpatents where rid='".$reportmetainfo['rid']."'";
																			
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <?php 
																				   if($row['typeofassignee']!='JV' && $row['typeofassignee']!='INVENTOR')
																				   {
																				   echo " <li><div class='getinsight' id='ins-fregions-".$i."' data-chart='36' data-typeofassignee='".$row['typeofassignee']."'>".$row['typeofassignee']."</div></li>";
																				   }
																				   ?>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
																		 }
																	   ?>
																   
				  </ul>
				  
				  <div class='insight-group show-help' data-tipso="This section highlights the different regions of the players working in this domain based on the actual number of the companies corresponding to different regions">Focused Region- Companies region analysis (Based on number of players)</div>
		          <ul> 
				                                                 <?php
																		 if($reportmetainfo['typeofassignee']==1)
																		 {
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 $sql ="SELECT distinct typeofassignee FROM relevantpatents where rid='".$reportmetainfo['rid']."'";
																			
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <?php 
																				   if($row['typeofassignee']!='JV' && $row['typeofassignee']!='INVENTOR')
																				   {
																				   echo " <li><div class='getinsight' id='ins-fregions-2-".$i."' data-chart='37' data-typeofassignee='".$row['typeofassignee']."'>".$row['typeofassignee']."</div></li>";
																				   }
																				   ?>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
																		 }
																	   ?>
				  </ul>
			  </li>
			 
		</ul>
		 <?php }?>
		 
		 <?php if(($reportmetainfo['typeofassignee']==1) && ($reportmetainfo['headquarter']==1)){?>
		<ul>
		      <li>
			     <div class='insight-group' data-tipso="">Companies vs priority years</div>
		          <ul> 
				                                                 <?php
																		 if($reportmetainfo['typeofassignee']==1)
																		 {
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 $sql ="SELECT distinct typeofassignee FROM relevantpatents where rid='".$reportmetainfo['rid']."'";
																			
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <?php 
																				   if($row['typeofassignee']!='JV' && $row['typeofassignee']!='INVENTOR')
																				   {
																				   echo " <li><div class='getinsight' id='ins-comptrans-".$i."' data-chart='39' data-typeofassignee='".$row['typeofassignee']."'>".$row['typeofassignee']."</div></li>";
																				   }
																				   ?>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
																		 }
																	   ?>
																	   
																	   
                              							 
						      
							  
				  </ul>
				  
				  
			  </li>
			 
		</ul>
		<ul>
		    <li>
			 <div class='insight-group show-help' data-tipso="This section highlights change in the supply chain in terms of entry/ exit of players and shifts towards some new region companies owing to COVID-19">COVID-19 impact on the supply chain</div>
		      <ul>
		      <li><div class='getinsight' id='ins-40' data-chart='40'>Players transition</div></li>
			  <li><div class='getinsight' id='ins-41' data-chart='41'>Players region transition</div></li>	
		      </ul>
		    </li>
		</ul>
		
		 <?php }?>
		
		 <ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section highlights the various regions/ jurisdictions preferred by the applicants/ companies filing patents in the subject technology domain.">PREFERRED REGIONS</div>
		          <ul>
				        <li><div class='getinsight' id='ins-6' data-chart='7'>Origin of innovation</div></li>
						<li><div class='getinsight' id='ins-7' data-chart='8'>Countries vs. Priority years</div></li>
	                    <li><div class='getinsight' id='ins-5' data-chart='6'>Preferred market countries</div></li>
	                    
				  </ul>
			  </li>
		</ul>
																	   
	    <ul>
		      <li>
			     <div class='insight-group'>PREFERRED REGIONS VS. COMPANIES</div>
		          <ul>
				        <li><div class='getinsight' id='ins-19' data-chart='19'>Origin of innovation</div></li>
	                    <li><div class='getinsight' id='ins-20' data-chart='20'>Preferred market countries</div></li>
				  </ul>
			  </li>
		</ul>
   	    
		<ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section presents a correlation among the different aspects of the patent families with their overall legal status.">Legal status analysis (Overall family legal status)</div>
		          <ul>
				        <li><div class='getinsight' id='ins-21' data-chart='21'>Overall legal status</div></li>
	                    <?php if($reportmetainfo['typeofassignee']==1){?>
						<li><div class='getinsight' id='ins-22' data-chart='22'>Business type of assignee</div></li>
						<?php }?>
						<li><div class='getinsight' id='ins-23' data-chart='23'>Major Assignee</div></li>
						<!--<li><div class='getinsight' id='ins-24' data-chart='24'>Preferred market countries</div></li>-->
				  </ul>
			  </li>
			  <?php if($reportmetainfo['categorization']==1){?>	
			   <li><div class='insight-group'>Technology areas vs Legal status</div>
					     <ul>
						     <?php
		                        for($i=0;$i<count($child);$i++)
		                           {
			                        echo "<li><div data-hlevel='".$hirearchylevel[$i]."' class='getinsight ".$hirearchylevel[$i]."' id='ins-patact-legal-".$i."' data-chart='25' data-toplevel='".rawurlencode($child[$i])."'>".$child[$i]."</div></li>";
					               }
		                      ?>
						 </ul>
			  </li>
			  <?php }?>
			 
		</ul>
		 <?php if($reportmetainfo['legalstatus']==1){?>
		<ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section presents a correlation among the different aspects of the patents with their specific legal status.">Legal status analysis (Family members legal status)</div>
		          <ul>
				        <li><div class='getinsight' id='ins-26' data-chart='26'>Overall legal status</div></li>
						<li><div class='getinsight' id='ins-27' data-chart='27'>Major Assignee</div></li>
						<li><div class='getinsight' id='ins-28' data-chart='28'>Authorities protected</div></li>
				  </ul>
			  </li>
			 
		</ul>
		 <?php }?>
		<?php if($reportmetainfo['ipccpc']==1){?>
		<ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="IPC Analysis">IPC Analysis</div>
		          <ul>
				        <li><div class='getinsight' id='ins-31' data-chart='31'>Major IPC classes</div></li>
						<li><div class='getinsight' id='ins-32' data-chart='32'>Major IPC classes VS. Companies</div></li>
						
				  </ul>
			  </li>
			 
		</ul>
		
		<ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="CPC Analysis">CPC Analysis</div>
		          <ul>
				        <li><div class='getinsight' id='ins-33' data-chart='33'>Major CPC classes</div></li>
						<li><div class='getinsight' id='ins-34' data-chart='34'>Major CPC classes VS. Companies</div></li>
						
				  </ul>
			  </li>
			 
		</ul>
		 <?php }?>
<?php if($reportmetainfo['scoring']==1){?>		
	<div>
	    <select id='scoring-opt'>
		    <option value='1'>EXTERNAL TECHNOLOGY RELEVANCE</option>
			<option value='2'>INTERNAL TECHNOLOGY RELEVANCE</option>
			<option value='3'>TECHNOLOGY RELEVANCE</option>
			<option value='4'>MARKET COVERAGE</option>
			<option value='5'>COMPETITIVE IMPACT</option>
			<option value='6'>ECI</option>
			<option value='7'>PATENT ASSET INDEX</option>
		</select>
	</div>
	  
	<div>
	    <select id='active-auth-opt'>
		    <option value='1'>All</option>
			<option value='2'>Active</option>
		</select>
	</div>
	    <?php if($reportmetainfo['categorization']==1){?>
	    <ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section includes the qualitative evaluation of the patents to recommend the most focused/ growing technology areas by calculating the overall portfolio strength of each technology area based on various scoring parameters.">ICUE-TECHGRADING</div>
		          <ul>
				      <?php
		                 for($i=0;$i<count($child);$i++)
		                 {
			              echo "<li><div data-hlevel='".$hirearchylevel[$i]."' class='getinsight ".$hirearchylevel[$i]."' id='score-tech-".$i."' data-chart='15' data-toplevel='".rawurlencode($child[$i])."'>".$child[$i]."</div></li>";
					   
		                 }
		               ?>  
				  </ul>
			  </li>
		</ul>
	    <?php }?>
	    <ul>
		      <li>
			     <div class='insight-group show-help' data-tipso="This section presents the competitive benchmarking of the various companies working in the subject technology domain based on the qualitative evaluation of their portfolio.">ICUE-STANDINGS</div>
		          <ul>
				     <li><div class='insight-group show-help' data-tipso="The technology LEADerboard chart&trade; provides unique insights into the race for technology leadership among the major companies in a specific technology domain. The chart is prepared by considering the portfolio size and the quality of the patents. The chart is divided into four sections, wherein each section defines a specific group of companies based on the quantitative and qualitative strength of their patent portfolio.">LEADerBoard</div>
					     <ul>
						     <li><div class='getinsight' id='ins-18' data-chart='18'>Overall</div></li>
							                                         <?php
																	  if($reportmetainfo['typeofassignee']==1)
																		{
																	           include 'dbconnection.php';
                                                                                if (!$conn) 
		                                                                        {
                                                                                die("Connection failed: " . mysqli_connect_error());
                                                                                }
																				 mysqli_select_db($conn,$dbname);
																			     
																				 $sql ="SELECT distinct typeofassignee FROM relevantpatents where rid='".$reportmetainfo['rid']."'";
																			
                                                                              
                                                                               $result = mysqli_query($conn, $sql);
                                                                                  $num = mysqli_num_rows($result);
                                                                               if ($num> 0) 
																			   {
																				   for($i=0;$i<$num;$i++) 
	                                                                               {
		                                                                           $row=mysqli_fetch_array($result);
																				   ?>
																				   <?php echo " <li><div class='getinsight' id='leaderboard-assignee-".$i."' data-chart='17' data-typeofassignee='".$row['typeofassignee']."'>".$row['typeofassignee']."</div></li>"?>
																				   <?php
																				   }
																			   }
																			   else{
																				   echo "";
																			   }
																		}
																	   ?>
						 </ul>
				     </li>
					 <?php if($reportmetainfo['categorization']==1){?>
					 <li><div class='insight-group show-help' data-tipso="This section correlates the patent filings by various companies in various technology areas/ sub-categories with the quality of their portfolio size based on the various scoring parameters. This section may help identify the various technology areas focused by companies based on the quality of patents filed by the companies among various technology areas.">COMPANIES VS. ICUE-TECHGRADING</div>
					     <ul>
						       <?php
		                            for($i=0;$i<count($child);$i++)
		                            {
			                          echo "<li><div data-hlevel='".$hirearchylevel[$i]."' class='getinsight ".$hirearchylevel[$i]."' id='score-comp-".$i."' data-chart='16' data-toplevel='".rawurlencode($child[$i])."'>".$child[$i]."</div></li>";
					   
		                            }
		                        ?> 
						 </ul>
				     </li>
					 <?php }?>
		          </ul>
			  </li>
		</ul>
	  <?php }?>    
	
			
	
	
	
	                                                              
	
	
	</div>
    
   
   </div>
   <div class='mc-iv-right'><div class='mc-iv-right-navigation'><span id='leftpaneltoggle' style='cursor:pointer' title='Show/hide left panel'><i class="fa fa-exchange" aria-hidden="true"></i></span><span id='insightdescription'></span><span id='layoutsettings' style='cursor:pointer' title='Layout Settings' data-toggle="modal" data-target="#layoutsettingForm"><i class="fa fa-cog" aria-hidden="true"></i></span><span id='open-max-window' title='Maximize chart window' class='notopened'><i class="fa fa-expand" aria-hidden="true"></i></span></div><div id='insight-suboption'><div id='top-companies-filters'><select id="top-comp-opt"><option value="10">Top 10</option><option value="20">Top 20</option><option value="30">Top 30</option><option value='all'>All</option></select></div><div id='switch-charts'><select id="switchchart"></select></div><div id='pdate-filter'><select id="priority-year-opt"><option value="5">Last 5 years</option><option value="7">Last 7 years</option><option value="10" selected >Last 10 years</option><option value="20">Last 20 years</option><option value='precovid'>Pre covid years</option><option value='postcovid'>Post covid years</option><option value="all">All years</option></select></div><div id='emerging-companies-filters'><div class="pretty p-default p-round"><input type='radio' name='emergingplayer' value='precovid' id='pcovid' checked /> <div class="state p-danger" ><label for='pcovid'  >Old  </label> </div></div> <div class="pretty p-default p-round"><input type='radio' name='emergingplayer' value='postcovid' id='postcovid' /> <div class="state p-danger" ><label for='postcovid' class='' > New  </label></div></div> <div class="pretty p-default p-round"> <input type='radio' name='emergingplayer' value='prendpostcovid' id='prendpostcovid' /> <div class="state p-danger" > <label for='prendpostcovid' class='' > Active </label></div></div></div><div id='leaderboard-checked-text'><input type='checkbox' id='leaderboardtext'/> Show Text</div><div id='insight-result-count-clickedpoint'></div></div><div class='mc-iv-right-tester-outer'><div id="tester" style="width:100%;height:100%;"></div></div><div id='insight-navigation'><span id='pre-ins' title='Previous Insight'><img src="images/left-nav.svg" height="15"> Previous</span><span id='nxt-ins' title='Next Insight'>Next <img src="images/right-nav.svg" height="15"></span></div></div>
  <!---->
 </div>
    
  </div> 
  
     
  </div>
  <div class='bottom'>
     <div class='bottom-inner'>
      <div class='bottom-content'>© iCuerious 2012-2022. All Rights Reserved
	      <a style='float:right;display:none;' id="google_translate_element"></a>
	  </div>
      <div id='pagination'>
	  
	      <div><b>Total Records : </b><span id='totalRecord'></span> <select id='perpagerecord' name='perpagerecord'><option>15</option><option>30</option><option>50</option></select>
	      <ul class='pagination_new'>
		  </ul>
		  </div>
	  </div>
	  <div id='pagination_pubview_outer'>
	  
	      <div>
	      <ul class='pagination_pubview'>
		      <li><a onclick="previous_pub()"><image src='images/left-nav.svg' height='15'/> Previous</a></li><li><a onclick="next_pub()">Next <image src='images/right-nav.svg' height='15'/></a></li>
		  </ul>
		  </div>
	  </div>
	</div>
	</div>
  </div>

<!---Change categorization form--------------> 
 <!--<div id="addCatForm" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ADD Category</h4>
      </div>
      <div class="modal-body">
        <form method="POST" id="categoryForm">
		
		     <div class='row'>
			      <div class='col-md-3'>
				       <input type='hidden' name='connected_pid' id='connected_pid' value=''/>
				       <div class='form-group'>
					       <label>Level1</label>
						   <select id='formlevel1' name='level1'  class='form-control' required>
							<option value=''>--Select--</option>													
																	   <?php
																	           
																				  /* for($i=0;$i<count($toplevelarray);$i++) 
	                                                                               {
		                                                                           
																				   echo"<option>".$toplevelarray[$i]."</option>";
																				   
																				   }*/
																			   
																	   ?>
																	   
					      </select>
					   </div>
				  </div>
				   <div class='col-md-3'>
				       <div class='form-group'>
					       <label>Level2</label>
						      <select id='formlevel2' name='level2'   class='form-control'>
				                   <option value=''>--Select--</option>
                              </select>
					   </div>
				  </div>
				   <div class='col-md-3'>
				       <div class='form-group'>
					       <label>Level3</label>
						   <select id='formlevel3' name='level3'   class='form-control'>
				                <option value=''>--Select--</option>
                           </select>
					   </div>
				  </div>
				   <div class='col-md-3'>
				       <div class='form-group'>
					       <label>Level4</label>
						   <select id='formlevel4' name='level4'   class='form-control'>
				               <option value=''>--Select--</option>
                            </select>
					   </div>
				  </div>
				  <div class='col-md-12'>
				        <div style='text-align:center;'>
				        <button class="actionbtn" id='savecatbtn'>Save</button>
						</div>
				  </div>
			 </div>
		</form>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>

  </div>
</div> -->
<!---Add tagging form-------------->
 <!--<div id="addTaggingForm" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ADD Tagging</h4>
      </div>
      <div class="modal-body">
        <form method="POST" id="taggingForm">
		
		     <div class='row'>
			      <div class='col-md-12'>
				  
                    <div id='custom-tags'>
					
                    </div>	
                     <div class='std-tags'>
				      <div class='form-group' style='display:none;' id='hiddentag'>
			                  <label for='definetag'>Define Tag</label>
				              <input type='text' class='form-control' name='definetag' id='definetag' />	
                        </div>
				        
                    </div>					
                        <div style="text-align:center;">
				              <button class="actionbtn" id="savetaggingbtn">Save</button>
						</div>						
				  </div>
				   
			 </div>
		</form>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>

  </div>
</div>   -->
<!---Change layout form-------------->
 <!--<div id="layoutsettingForm" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    Modal content
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Layout Settings</h4>
      </div>
      <div class="modal-body">
        
		
		     <div class='row'>
			      <div class='col-md-6'>
				  
                    
                     
				      <div class='form-group'>
			                  
				              <input type='checkbox' name='xaxislabel' id='xaxislabel'  />	<label for='xaxislabel'>Show complete X axis tick labels</label>
							  <br/><label>X axis tick label angle</label> <select name='xaxislabelangle' id='xaxislabelangle'><option>Auto</option><option>45</option><option>90</option><option>-45</option><option>-90</option></select>
                              <br/><label>X axis tick font size</label> <select name='xaxislabelfont' id='xaxislabelfont'><option>6</option><option>8</option><option selected>10</option><option>12</option><option>14</option><option>16</option><option>18</option><option>20</option></select>
							  <br/><input type='checkbox' name='yaxislabel' id='yaxislabel'  checked />	<label for='yaxislabel'>Show complete Y axis tick labels</label>
							  <br/><label>Y axis tick label angle</label> <select name='yaxislabelangle' id='yaxislabelangle'><option>Auto</option><option>45</option><option>90</option><option>-45</option><option>-90</option></select>
                              <br/><label>Y axis tick font size</label> <select name='yaxislabelfont' id='yaxislabelfont'><option>6</option><option>8</option><option selected>10</option><option>12</option><option>14</option><option>16</option><option>18</option><option>20</option></select>
					          <br/><label>Max Bubble size</label><select name='bubbleresizer' id='bubbleresizer'><option>10</option><option>20</option><option>30</option><option>40</option><option selected>50</option></select>
					  </div>
				        
                   				
                        					
				  </div>
				  <div class='col-md-6'>
				     <div class='form-group'>
					   <label>Select Color Palette</label><br/>
					      <input type='radio' name='palette' class='palette' value='p1'/><span id='p1'></span><br/>
						  <input type='radio' name='palette' class='palette' value='p2'/><span id='p2'></span><br/>
						  <input type='radio' name='palette' class='palette' value='p3'/><span id='p3'></span><br/>
						  <input type='radio' name='palette' class='palette' value='p4'/><span id='p4'></span><br/>
						  <input type='radio' name='palette' class='palette' value='p5'/><span id='p5'></span><br/>
						  <input type='radio' name='palette' class='palette' value='p6'/><span id='p6'></span><br/>
						  <input type='radio' name='palette' class='palette'  checked value='p7'/><span id='p7'></span>
					 </div>
				  </div> 
			 </div>
		
      </div>
      <div class="modal-footer">
        
      </div>
    </div>

  </div>
</div>  -->  
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
 <!----comment popup---->
  <div class='comment-right-side-view-wrapper-popup' id='comment-right-side-view-wrapper-popup'>
  <div class='comment-outer'>
   <div class='comment-right-side-view'>
      <section>
		        <h5>Comments</h5>
				<div class='projectprogress'>
				
				 
                 <div id='commentbody'></div>							
			   </div>
		</section>
	</div>
	<div class='comment-post-outer'>
	    <textarea contentEditable="true" placeholder='Add your comment here' required name='comment' id='comment'  class='comment_textarea' rows='1'></textarea>
		<button class='button button1' id='updatecmtbtn'>Post</button>
	</div>
</div>
  </div> 
 <!------- Tagging popup---->
  <div class='tagging-right-side-view-wrapper-popup' id='tagging-right-side-view-wrapper-popup'>
  <div class='tagging-outer'>
   <div class='tagging-header'>
      Add Tag <span id="closetaggingbtn">X</span>
   </div>
   <div class='tagging-right-side-view'>
        <form method="POST" id="taggingForm">
		             <div id='custom-tags'></div>	
                     <div class='std-tags'>
				      <div class='form-group' style='display:none;' id='hiddentag'>
			                  <label for='definetag'>Define Tag</label>
				              <input type='text' class='form-control' name='definetag' id='definetag' placeholder='Add your tag here'/>	
                      </div>
				        
                    </div>					
         </form>
	</div>
	<button class="actionbtn" id="savetaggingbtn">Save</button>
	</div>
  </div> 
   <!------- Category popup---->
  <div class='category-right-side-view-wrapper-popup' id='category-right-side-view-wrapper-popup'>
  <div class='category-outer'>
   <div class='category-header'>
      Add Category <span id="closecategorybtn">X</span>
   </div>
   <div class='category-right-side-view'>
        <form method="POST" id="categoryForm">
		
		     
			      <div class=''>
				       <input type='hidden' name='connected_pid' id='connected_pid' value=''/>
				       <div class=''>
					       <label>Level1</label><br/>
						   <select id='formlevel1' name='level1'  class='summoselect' required>
							<option value=''>--Select--</option>													
																	   <?php
																	           
																				  for($i=0;$i<count($toplevelarray);$i++) 
	                                                                               {
		                                                                           
																				   echo"<option>".$toplevelarray[$i]."</option>";
																				   
																				   }
																			   
																	   ?>
																	   
					      </select>
					   </div>
				  </div>
				   <div class=''>
				       <div class=''>
					       <label>Level2</label><br/>
						      <select id='formlevel2' name='level2'   class='summoselect'>
				                   <option value=''>--Select--</option>
                              </select>
					   </div>
				  </div>
				   <div class=''>
				       <div class=''>
					       <label>Level3</label><br/>
						   <select id='formlevel3' name='level3'   class='summoselect'>
				                <option value=''>--Select--</option>
                           </select>
					   </div>
				  </div>
				   <div class=''>
				       <div class=''>
					       <label>Level4</label><br/>
						   <select id='formlevel4' name='level4'   class='summoselect'>
				               <option value=''>--Select--</option>
                            </select>
					   </div>
				  </div>
				  
			
		</form>
	</div>
	 <button class="actionbtn" id='savecatbtn'>Save</button>
	</div>
  </div> 
<!-------Context---->
<div id="contextbox">
<button id='colorbox' title="Colors" class="btn btn-success"  data-toggle="tooltip" >C</button>
<button id='bold' title='Bold' class="btn btn-danger" data-toggle="tooltip">B</button>
<button id='underline' title='Underline' class="btn btn-info" data-toggle="tooltip">U</button>
<button id='removehighlight' title='Remove' class="btn btn-warning" data-toggle="tooltip">X</button>
</div>
<div id='colorboxdiv'>
<span id='closecolor' title='Close' style='float:right; position:absolute; top:-20px; right:-15px; font-size:16px; color:#1b75bc; background-color:white;cursor:pointer;'><img src='images/color-picker-close.png'/></span>
<div style="background-color:#1ABC9C"></div><div style="background-color:#F1C40E"></div><div style="background-color:#F39C12"></div><div style="background-color:#D35400"></div>
<div style="background-color:#9B59B6"></div><div style="background-color:#3498DB"></div><div style="background-color:#CDDC39"></div><div style="background-color:#32D9CC"></div>
<div style="background-color:#8BC34A"></div><div style="background-color:#FFEB3B"></div><div style="background-color:#da2ad8"></div><div style="background-color:#33495E"></div>
<div style="background-color:#795548"></div><div style="background-color:#E9503D"></div><div style="background-color:grey"></div><div style="background-color:blue"></div>
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
	  <script src="js/icheck.js"></script>
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

$('body').on('click','.pre_pub',function(){
	
		var previous ='';
		var pub = $(this).data('pubno');
		$(".viewpub").each(function(){
			if($(this).data('pubno')==pub)
			{
				if(previous!='')
				{
				retrievetext(reportid, previous);
				return false;
				}
				else
				{
					alert('You have reached the very first publication number in your list.');
				}
			}
			else
			{
				previous = $(this).data('pubno');
			}
		});
	});
	
$('body').on('click','.after_pub',function(){
		var matched = 0;
		var movedtonext =0;
		var pub = $(this).data('pubno');
		$(".viewpub").each(function(){
			
			if(matched == 0)
			{
			   if($(this).data('pubno')==pub)
			   {
				 matched = 1;
			   }
			}
			else
			{
				movedtonext =1;
				retrievetext(reportid, $(this).data('pubno'));
				return false;
			}
			
		});
		if(movedtonext==0)
		{
			alert('You have reached the very last publication number in your list. Kindly go back to the resultset and select another page.');
		}
	});
	/*retrievetext('<?php echo $_GET["reportid"];?>', '<?php echo $_GET["pubno"];?>');*/
	function retrievetext(reportid, pubno)
	{
		fetchcommentrecords(reportid, pubno);
		              $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=retrievetext&reportid="+reportid+"&pubno="+pubno,
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							 
							 	 if(data!=0)
								  {
									 
									  for (i = 0; i < data.length; i++) 
								      {
										  $('#connected_pid').val(data[i].pid);
										  $('#saveclaims').prop('data-pid',data[i].pid);
										  var cc ='';
                                        var	serailno ='';	
                                        var kc ='';
                                        var googlelink ='';
                                        var espacenetlink = '';
										var usptolink ='';
										var icuemaplink ='';
										var alllinksbtn ='';
										cc = data[i].pubno.substring(0,2).toUpperCase();
										var pubnowithkc = data[i].pubno.substring(2);
                                        var firstletterofkc = /[a-z]/i.exec(pubnowithkc);
                                            if(firstletterofkc!=null)
											{
												index = firstletterofkc.index;
												serailno = pubnowithkc.substring(0,index);
												kc = pubnowithkc.substring(index);
											}
											else
											{
												//kc not found kc=''
												serailno = data[i].pubno.substring(2);
											}
											
										  if(cc=='US' && serailno.length<=8)
										  {
											  googlelink ="https://patents.google.com/patent/"+data[i].pubno;
								              usptolink = "https://ppubs.uspto.gov/pubwebapp/external.html?q=("+serailno+").pn";
											  espacenetlink = "https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+cc+""+serailno;	
											  icuemaplink = "https://www.icuerious.com/icuemapnew/?pubno="+data[i].pubno;
											  alllinksbtn ="<a class='lnkbtn' href='"+googlelink+"' target='_blank' title='Google Patents'><img src='images/googlepatent.svg' height='20px'/></a><a class='lnkbtn' href='"+usptolink+"' target='_blank' title='Uspto'><img src='images/uicon.svg' height='20px'/></a><a class='lnkbtn' href='"+espacenetlink+"' target='_blank' title='EspaceNet'><img src='images/espacenet.svg' height='20px'/></a><a class='lnkbtn' href='"+icuemaplink+"' target='_blank' title='Patent Family Tree'><img src='images/icuemap.svg' height='20px'/></a><a class='lnkbtn' title='Tagging' id='taggingbtn' style='cursor:pointer;'><img src='images/tagging.svg' height='20px'/></a>";
										  
										  }
                                          else if(cc=='US' && serailno.length>8)
										  {
											  if(serailno.length == 10)
							                  {
								                serailno = serailno.substr(0,4) + "0" + serailno.substr(4);
								                
							                  }
											  googlelink ="https://patents.google.com/patent/"+cc+""+serailno;                                  
			                                  usptolink = "https://ppubs.uspto.gov/pubwebapp/external.html?q=("+serailno+").pn";
											  espacenetlink = "https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+cc+""+serailno;
										      icuemaplink = "https://www.icuerious.com/icuemapnew/?pubno="+data[i].pubno;
											  alllinksbtn ="<a class='lnkbtn' href='"+googlelink+"' target='_blank' title='Google Patents'><img src='images/googlepatent.svg' height='20px'/></a><a class='lnkbtn' href='"+usptolink+"' target='_blank' title='Uspto'><img src='images/uicon.svg' height='20px'/></a><a class='lnkbtn' href='"+espacenetlink+"' target='_blank' title='EspaceNet'><img src='images/espacenet.svg' height='20px'/></a><a class='lnkbtn' href='"+icuemaplink+"' target='_blank' title='Patent Family Tree'><img src='images/icuemap.svg' height='20px'/></a><a class='lnkbtn' id='taggingbtn' title='Tagging' style='cursor:pointer;'><img src='images/tagging.svg' height='20px'/></a>";
										  }
                                          else
										  {
											  googlelink ="https://patents.google.com/patent/"+data[i].pubno;
											  espacenetlink = "https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+cc+""+serailno;
										      icuemaplink = "https://www.icuerious.com/icuemapnew/?pubno="+data[i].pubno;
											  alllinksbtn ="<a class='lnkbtn' href='"+googlelink+"' target='_blank' title='Google Patents'><img src='images/googlepatent.svg' height='20px'/></a><a class='lnkbtn' href='"+espacenetlink+"' target='_blank' title='EspaceNet'><img src='images/espacenet.svg' height='20px'/></a><a class='lnkbtn' href='"+icuemaplink+"' target='_blank' title='Patent Family Tree'><img src='images/icuemap.svg' height='20px'/></a><a class='lnkbtn' title='Tagging' id='taggingbtn' style='cursor:pointer;'><img src='images/tagging.svg' height='20px'/></a>";
										  
										  }	
										  
										  $('#pubno').text(data[i].pubno);
										  if(data[i].tagging!='' && data[i].tagging!=null)
										  {
										  $('#pubtagging').html('<img src="images/tagging-icon.svg" height="15"/> '+data[i].tagging);
										  }
										  else
										  {
										  $('#pubtagging').html('');  
										  }
										  $('#alllinks').html(alllinksbtn);
										  if(data[i].relevancy!='')
										  {
										   $('#relevancy').text("("+data[i].relevancy+")");
										  }
										  else
										  {
											$('#relevancy').text("");  
										  }
										   var biblio ='';
										   biblio ='Title : '+data[i].pubtitle+"<br/>";
										   if(data[i].relevantpatent_legalstatus!='' && data[i].relevantpatent_legalstatus!=null)
										   {
											  biblio =biblio+'Legal Status : '+data[i].relevantpatent_legalstatus+"<br/>"; 
										   }
										   if(data[i].legalstate!='' && data[i].legalstate!=null)
										   {
											  biblio =biblio+'Overall Family Legal Status : '+data[i].legalstate+"<br/>"; 
										   }
										   biblio = biblio+'Publication Date : '+data[i].pubdate+"<br/>";
										   biblio = biblio+'Application Date : '+data[i].appdate+"<br/>";
										   biblio = biblio+'Priority : '+data[i].pdate+"<br/>";
										   biblio = biblio+'Inventor : '+data[i].inventor+"<br/>";
										   biblio = biblio+'Parent Assignee : '+data[i].parentassignee+"<br/>";
										   $('#biblio').html(biblio);
										   var inpadocfamily;
										   
										   if(data[i].family!='' && data[i].family!=null)
										   {
											   familyarray = data[i].family.split('\n');
											   if(data[i].familylegalstatus!='' && data[i].familylegalstatus!=null)
										       {
											   familystatus = data[i].familylegalstatus.split('\n');
											   }
											   var familytablerow ='';
											   for (s = 0; s < familyarray.length; s++) 
								                   {
													   if(typeof familystatus != 'undefined')
													   {
													   familytablerow = familytablerow+"<tr><td><a href='https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+familyarray[s]+"' target='_blank'>"+familyarray[s]+"</a></td><td> "+familystatus[s]+"</td></tr>";
													   }
													   else
													   {
														familytablerow = familytablerow+"<tr><td><a href='https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+familyarray[s]+"' target='_blank'>"+familyarray[s]+"</a></td><td></td></tr>";   
													   }
												   }
												   
												inpadocfamily = '<table width="100%"><tbody>'+familytablerow+"</tbody>";   
										   }
										   $('#inpadocfamily').html(inpadocfamily);
										   $('#abstract').text(data[i].pubabstract);
										   $('#claims').html(data[i].claims);
										   var cat = data[i].categorization;
										   var catcontent ="";
										   for (s = 0; s < cat.length; s++)
										   {
											   /*catcontent=catcontent+"<div class='cat-separator'>";
											   if(cat[s].level1!='' && cat[s].level1!=null)
											   {
												   catcontent =catcontent+"<span class='cat-box l1'>"+cat[s].level1+"</span>";
											   }
											   if(cat[s].level2!='' && cat[s].level2!=null)
											   {
												   catcontent =catcontent+"<span class='cat-box l2'>"+cat[s].level2+"</span>";
											   }
											   if(cat[s].level3!='' && cat[s].level3!=null)
											   {
												   catcontent =catcontent+"<span class='cat-box l3'>"+cat[s].level3+"</span>";
											   }
											   if(cat[s].level4!='' && cat[s].level4!=null)
											   {
												   catcontent =catcontent+"<span class='cat-box l4'>"+cat[s].level4+"</span>";
											   }
											   if(cat[s].level5!='' && cat[s].level5!=null)
											   {
												   catcontent =catcontent+"<span class='cat-box l5'>"+cat[s].level5+"</span>";
											   }
											   catcontent=catcontent+"</div>";*/
											   if(cat[s].cid!='' && cat[s].cid!=null)
											   {
											   catcontent = catcontent + '<tr>';
											   if(cat[s].level1!='' && cat[s].level1!=null)
											   {
												   catcontent =catcontent+"<td class='l1' data-cid='"+cat[s].cid+"'>"+cat[s].level1+"</td>";
											   }
											   else
											   {
												   catcontent =catcontent+"<td class='l1' data-cid='"+cat[s].cid+"'></td>";
											   }
											   if(cat[s].level2!='' && cat[s].level2!=null)
											   {
												   catcontent =catcontent+"<td class='l2' data-cid='"+cat[s].cid+"'>"+cat[s].level2+"</td>";
											   }
											   else
											   {
												   catcontent =catcontent+"<td class='l2' data-cid='"+cat[s].cid+"'></td>";
											   }
											   if(cat[s].level3!='' && cat[s].level3!=null)
											   {
												   catcontent =catcontent+"<td class='l3' data-cid='"+cat[s].cid+"'>"+cat[s].level3+"</td>";
											   }
											   else
											   {
												   catcontent =catcontent+"<td class='l3' data-cid='"+cat[s].cid+"'></td>";
											   }
											   if(cat[s].level4!='' && cat[s].level4!=null)
											   {
												   catcontent =catcontent+"<td class='l4' data-cid='"+cat[s].cid+"'>"+cat[s].level4+"</td>";
											   }
											   else
											   {
												   catcontent =catcontent+"<td class='l4' data-cid='"+cat[s].cid+"'></td>";
											   }
											   if(cat[s].level5!='' && cat[s].level5!=null)
											   {
												   catcontent =catcontent+"<td class='l5' data-cid='"+cat[s].cid+"''>"+cat[s].level5+"</td>";
											   }
											   else
											   {
												   catcontent =catcontent+"<td class='l5' data-cid='"+cat[s].cid+"'></td>";
											   }
											   catcontent =catcontent+"<td><span data-cid='"+cat[s].cid+"' class='delcat' title='Delete row'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";
											   catcontent=catcontent+"</tr>";
											   }
										   }											   
										   $("#categorisation").html("<table class='table' style='' width='100%'><tbody>"+catcontent+"</tbody>");
										   $(".pagination_pubview").empty();
										   $(".pagination_pubview").append("<li><a data-pubno='"+data[i].pubno+"' class='pre_pub'><image src='images/left-nav.svg' height='15'/> Previous</a></li><li><a data-pubno='"+data[i].pubno+"' class='after_pub'>Next <image src='images/right-nav.svg' height='15'/></a></li>");
									  }
							       }			 
                          },
                          error: function (e) 
						  {
                            if(e.status=='401')
							{
								window.location.replace('index.php');
							}
                           
                          }
                      });  
	}
	//getrelevantpatents('<?php echo $_GET["reportid"];?>','1');
	function getrelevantpatents(reportid,page_no)
	{
		$("#relevantpatents").empty();
		$("#relevantpatents").html("<tr id='processing-div2'><td colspan='9'>We are processing your request.... <img src='images/process.gif'/></td></tr>");
		 $.ajax({
                                type: 'POST',
                                url: 'retrievetext.php',
								data: fd+'&action=relevantpatent&reportid='+reportid+'&page_no='+page_no+"&perpagerecord="+$("#perpagerecord").val(),
								dataType: "json",
                                success: function(result)
			                     {
								  $("#processing-div2").remove();
								  var content ="";
								  var amount = 0;
								  if(typeof result['records'] !== "undefined" && result['records']!='')
								  {
									  data = result['records'];
									  for (i = 0; i < data.length; i++) 
								      {
										  if(i==0)
										  {
											  retrievetext(reportid, data[i].pubno);
										  }
										  var relevancy='';
										  if(data[i].relevancy!='' && data[i].relevancy!=null)
										  {
											  relevancy = ' ('+data[i].relevancy+')';
										  }
										  var memberlegalstatus='';
										  if(data[i].relevantpatent_legalstatus!='' && data[i].relevantpatent_legalstatus!=null)
										  {
											   memberlegalstatus = '<br/>'+data[i].relevantpatent_legalstatus;
										  }
										  content = content+"<tr><td class='srno'>["+data[i].srno+"]</td><td class='headcol'><a class='viewpub' title='View Publication' data-listrowid='"+data[i].srno+"' data-pubno='"+data[i].pubno+"' data-reportid='"+data[i].rid+"'> <span class='flag-icon flag-icon-"+data[i].pubno.substr(0, 2).toLowerCase()+"'></span> "+data[i].pubno+""+relevancy+"</a>"+memberlegalstatus+"</td><td>"+data[i].pubtitle+"</td><td>"+data[i].appdate+"</td><td>"+data[i].pdate+"<td>"+data[i].pubdate+"</td><td>"+data[i].parentassignee+"</td><td>"+data[i].updateddate+"</td><td id='list-tagging-"+data[i].srno+"'>"+data[i].tagging+"</td>";
										  content=content+"</tr>";
                                      }
									  

								  }
								  else
								  {
									content="<tr><td colspan='9'>No Result Found.</td></tr> ";
								  }
							      
									  $("#relevantpatents").empty();
									  $("#relevantpatents").append(content);
									  if(typeof result['totalrecords'] !== "undefined" && result['totalrecords']!='')
									  {
								        relevantpatentpagination(result['totalrecords'],page_no);
									  }
									  else
									  {
										 relevantpatentpagination(0,page_no); 
									  }
								  	  
									  
								 },
								 error: function (e) 
						         {
                                       if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                           
                                  }
					       });
	}
	
	function relevantpatentpagination(totalrecords,page_no)
		{
			
			
									 $('.pagination_new').empty();
									 $("#totalRecord").html(totalrecords);
									 
									 var recordsperpage = $("#perpagerecord").val();
									 var totalpages = Math.ceil(totalrecords/recordsperpage);
									 var nextpage = parseInt(page_no)+1;
									 var previouspage = parseInt(page_no)-1;
									 var pagethum = "<ul class='pagination_new'>";
									if(page_no<=1)
									  {
										  $('.pagination_new').append("<li class='disabled'><a><image src='images/left-nav.svg' height='15'/> Previous</a></li>");
									  }
                                      else
									  {
										  $('.pagination_new').append("<li><a data-page='1' class='pageitem'><image src='images/left-nav.svg' height='15'/> First</a></li><li><a data-page='"+previouspage+"' class='pageitem'><image src='images/left-nav.svg' height='15'/> Previous</a></li>");
										  
									  }									  
                                      if(page_no<totalpages)
									  {
										  $('.pagination_new').append("<li><a data-page='"+nextpage+"' class='pageitem'>Next <image src='images/right-nav.svg' height='15'/></a></li><li><a data-page='"+totalpages+"' class='pageitem'>Last <image src='images/right-nav.svg' height='15'/></a></li>");
									  }
									  else
									  {
										  $('.pagination_new').append("<li class='disabled'><a>Last <image src='images/right-nav.svg' height='15'/></a></li>");
									  }
                                      									  
								 
					
		}
		
		$("#pagination").on('click','.pageitem',function(){
			
		currentpage = $(this).data("page");
			getrelevantpatents(reportid, currentpage);
		    
		}); 

           $('#perpagerecord').on('change', function() {
			          currentpage = 1;
                      getrelevantpatents(reportid, '1');
		               
                });		
	/*$('#bcktolst').on('click',function(){
		if($('#middle-container').hasClass('visibility-true'))
		{
			$('#middle-container').removeClass('visibility-true');
			$('#middle-container').hide();
			$('#middle-container-consolidate-view').addClass('visibility-true');
			$('#middle-container-consolidate-view').show();
			
		}
		else if($('#middle-container-consolidate-view').hasClass('visibility-true'))
		{
			$('#middle-container-consolidate-view').removeClass('visibility-true');
			$('#middle-container-consolidate-view').hide();
			$('#middle-container').addClass('visibility-true');
			$('#middle-container').show();
		}
		
	});*/
	
	$('#showfilters').on('click',function(){
		$('#showcomments').removeClass('activetab');
		$('#showcomments').addClass('comment-area-not-expanded');
		$('#comment-right-side-view-wrapper-popup').hide();
		$('#tagging-right-side-view-wrapper-popup').hide();
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
	
	$('#showcomments').on('click',function(){
		$('#showfilters').removeClass('activetab');
		$('#showfilters').addClass('filter-area-not-expanded');
		$('#filter-right-side-view-wrapper-popup').hide();
		$('#tagging-right-side-view-wrapper-popup').hide();
		$('#comment-right-side-view-wrapper-popup').toggle();
			if($(this).hasClass('comment-area-not-expanded'))
			{
				$(this).removeClass('comment-area-not-expanded');
				$(this).addClass('activetab');
				var cmntbtn = $(this).offset();
			    var cmnttop  = cmntbtn.top + $(this).outerHeight(true);
			    var cmntleft = cmntbtn.left;
			    var cmntright = $(document).outerWidth(true)-cmntleft;
			    var cmntwidth = $(this).outerWidth(true)/2;
				
				
				 var bottomheight = $('.bottom').outerHeight(true);
			    $('#comment-right-side-view-wrapper-popup').css({'top':cmnttop,'right':0,'bottom':bottomheight});
			    //$('.notify-arrowup-comment').css({'right':cmntright-cmntwidth-5,'top':-5});
			    
			    
			}
			else
			{
				$(this).addClass('comment-area-not-expanded');
				$(this).removeClass('activetab');
			}
		return false;

		if(!$('#comment-right-side-view-wrapper').hasClass('visibility-true'))
		{
			$('#comment-right-side-view-wrapper').addClass('visibility-true');
			$('#comment-right-side-view-wrapper').show();	
		}
		else
		{
			$('#comment-right-side-view-wrapper').removeClass('visibility-true');
			$('#comment-right-side-view-wrapper').hide();
		}
		window.dispatchEvent(new Event('resize'));
	});
	
	$('body').on('click','#taggingbtn',function()
	{
		
		
		if($('#showcomments').hasClass('activetab'))
		{
			$('#showcomments').removeClass('activetab');
			$('#showcomments').addClass('comment-area-not-expanded');
			$('#comment-right-side-view-wrapper-popup').hide();
		}
		
		if($('#showfilters').hasClass('activetab'))
		{
			$('#showfilters').removeClass('activetab');
			$('#showfilters').addClass('filter-area-not-expanded');
			$('#filter-right-side-view-wrapper-popup').hide();
		}
		
		$('#tagging-right-side-view-wrapper-popup').toggle();
		
			if($("#tagging-right-side-view-wrapper-popup").is(":visible"))
			{
				
				var cmntbtn = $(this).offset();
			    var cmnttop  = cmntbtn.top + $(this).outerHeight(true) + 5;
			    var bottomheight = $('.bottom').outerHeight(true);
			    $('#tagging-right-side-view-wrapper-popup').css({'top':cmnttop,'left':5,'bottom':bottomheight});
			   
			    
			    
			}
			
		
		window.dispatchEvent(new Event('resize'));
	});
	
	$('body').on('click','#addcatbtn',function()
	{
		
		
		if($('#showcomments').hasClass('activetab'))
		{
			$('#showcomments').removeClass('activetab');
			$('#showcomments').addClass('comment-area-not-expanded');
			$('#comment-right-side-view-wrapper-popup').hide();
		}
		
		if($('#showfilters').hasClass('activetab'))
		{
			$('#showfilters').removeClass('activetab');
			$('#showfilters').addClass('filter-area-not-expanded');
			$('#filter-right-side-view-wrapper-popup').hide();
		}
		
		$('#tagging-right-side-view-wrapper-popup').hide();
		
		$('#category-right-side-view-wrapper-popup').toggle();
		
			if($('#category-right-side-view-wrapper-popup').is(":visible"))
			{
				$(this).removeClass('category-area-not-expanded');
				
				var cmntbtn = $(this).offset();
			    var cmnttop  = cmntbtn.top + $(this).outerHeight(true) + 5;
			    var bottomheight = $('.bottom').outerHeight(true);
			    $('#category-right-side-view-wrapper-popup').css({'top':cmnttop,'right':0,'bottom':bottomheight});
			   
			    
			    
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
	
	$('#bcktolst').on('click',function(){
	    if(!$('#middle-container-consolidate-view').hasClass('visibility-true'))
		{
			sectiontoshow_after_applyingfilter =1;
			$('#middle-container-filter-view').removeClass('visibility-true');
			$('#middle-container-filter-view').hide();
			$('#middle-container').removeClass('visibility-true');
			$('#middle-container').hide();
		    $('#middle-container-insight-view').removeClass('visibility-true');
			$('#middle-container-insight-view').hide();
			$('#middle-container-consolidate-view').addClass('visibility-true');
			$('#middle-container-consolidate-view').show();
			
			$('#showfilters').show();
			$('#showinsights').show();
			$('#bcktolst').hide();
			$('#showcomments').hide();
			$('#showcomments').removeClass('activetab');
			$('#showcomments').addClass('comment-area-not-expanded');
			$('#comment-right-side-view-wrapper').hide();
			$('#tagging-right-side-view-wrapper-popup').hide();
			$('#comment-right-side-view-wrapper').removeClass('visibility-true');
			$('#comment-right-side-view-wrapper-popup').hide();
			$('#pagination').show();
		    $('#pagination_pubview_outer').hide();
		}
		
		
		
	});
	
	$('body').on('click','.viewpub',function(){
		sectiontoshow_after_applyingfilter =3;
		$('#pagination').hide();
		$('#pagination_pubview_outer').show();
		var pubno = $(this).data('pubno');
		var reportid = $(this).data('reportid');
		retrievetext(reportid, pubno);
		$('#middle-container').addClass('visibility-true');
		$('#middle-container').show();
		$('#middle-container-consolidate-view').removeClass('visibility-true');
		$('#middle-container-consolidate-view').hide();
		$('#middle-container-filter-view').removeClass('visibility-true');
		$('#middle-container-filter-view').hide();
		$('#middle-container-insight-view').removeClass('visibility-true');
		$('#middle-container-insight-view').hide();
		$('#showfilters').hide();
		$('#filter-right-side-view-wrapper').removeClass('visibility-true');
		$('#showfilters').removeClass('activetab');
		$('#showfilters').addClass('filter-area-not-expanded');
		$('#filter-right-side-view-wrapper-popup').hide();
		//$('#showinsights').hide();
		$('#showcomments').show();
		$('#bcktolst').show();
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
	
	$("#formlevel1").on('change',function(){
		var level1val=[];
		 $("#formlevel1 > option").each(function(){
			 
												 if($(this).prop("selected")==true)
												 {
													 var selectedval = $(this).val();
													 level1val.push(encodeURIComponent(selectedval));
												 }
											 });
			$("#formlevel2").empty();								 
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

                                 $("#formlevel2").append(content);
                                 $('#formlevel2')[0].sumo.reload();								 
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
	
	$("#formlevel2").on('change',function(){
		var level2val=[];
		 $("#formlevel2 > option").each(function(){
			 
												 if($(this).prop("selected")==true)
												 {
													 var selectedval = $(this).val();
													 level2val.push(encodeURIComponent(selectedval));
												 }
											 });
			$("#formlevel3").empty();								 
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

                                 $("#formlevel3").append(content);
                                 $('#formlevel3')[0].sumo.reload();								 
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
	
	$("#formlevel3").on('change',function(){
		var level3val=[];
		 $("#formlevel3 > option").each(function(){
			 
												 if($(this).prop("selected")==true)
												 {
													 var selectedval = $(this).val();
													 level3val.push(encodeURIComponent(selectedval));
												 }
											 });
			$("#formlevel4").empty();								 
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

                                 $("#formlevel4").append(content);
                                 $('#formlevel4')[0].sumo.reload();								 
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
	
	           //show on load table
			    currentpage=1;
				$('#pagination').show();
				fd = $("form").serialize();
			    getrelevantpatents(reportid,'1');
                //invoicepagination(1);

/*-----------------------Insights----------------------------------------*/	
var globalx;
var gloabaly;
var globalz;
var globalclasseshover;
var globalmetadata;
var charttitle;			
$('body').on('click','.getinsight',function(){
	                insightclicked =1;
					exportdata = '';
	                charttitle = $(this).text();
					globalx = [];
					globaly = [];
					globalz = [];
					globalclasseshover =[];
					globalmetadata =[];
					var tracecolor= ['#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844','#f44336','#f85b47','#fc6f59','#ff826b','#ff947d','#ffa590','#ffb6a4','#ffc7b8','#ffd7cd','#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844'];
					$('.getinsight').css({'font-weight':'normal'});
					$('.getinsight').removeClass('ins-selected');
					$(this).css({'font-weight':'bold'});
					$(this).addClass('ins-selected');
					$('#tester').remove();
					$('#insightdescription').html("");
					$('.mc-iv-right-tester-outer').html("<div id='processing-div'>We are processing your request.... <img src='images/process.gif'/></div>");
					$('.mc-iv-right-tester-outer').append("<div id='tester' style='width:100%;height:100%;'></div>");
					var chart = $(this).data("chart");
					$('#insight-result-count-clickedpoint').html('');
					showsettingbtn(chart);
					showpdatefilter(chart);
					showemergingplayerfilter(chart);
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
						  $('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="line">Line</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'count','xcol':'Priority/Filing Year','ycol':'Count'};	
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
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'count','xcol':'Assignee Name','ycol':'Count'};
											
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
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option><option value="map">Map</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'marketcountry','y':'count','xcol':'Market Country','ycol':'Count'};	
											
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
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option><option value="map">Map</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'innovationcountry','y':'count','xcol':'Country of Innovation','ycol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'innovationcountry','xcol':'Priority/Filing Year','ycol':'Country of Innovation','zcol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','y':'categorisation','x':'pdate','hlevel':hlevel,'toplevel':level,'xcol':'Priority/Filing Year','ycol':'Category','zcol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'categorisation','y':'count','hlevel':hlevel,'toplevel':level,'xcol':'Category','ycol':'Count'};
                                            											
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
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="line">Line</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'typeofassignee','y':'count','xcol':'Type of Assignee','ycol':'Count'};	
											
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
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="line">Line</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'count','xcol':'Assignee Name','ycol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'categorisation','hlevel':hlevel,'toplevel':level,'xcol':'Assignee Name','ycol':'Category','zcol':'Count'};
											
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
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'assignee','xcol':'Priority/Filing Year','ycol':'Assignee Name','zcol':'Count'};
											
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
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','y':'categorisation','x':'pdate','hlevel':hlevel,'toplevel':level,'xcol':'Priority year','ycol':'Category','zcol':'Score'};
											
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
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','y':'categorisation','x':'assignee','hlevel':hlevel,'toplevel':level,'xcol':'Assignee Name','ycol':'Category','zcol':'Score'};	
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
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="leaderboard" selected>Leaderboard</option><option value="table">Table</option>');	
						}
						$('#switch-charts').show();
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
                                            globalmetadata ={'drilldown':'enable','x':'assignee','y':'typeofassignee','typeofassignee':typeofassignee,'xcol':'Normalized Portfolio size(Count)','ycol':'Normalized Portfolio strength(Score)','zcol':'Assignee Name'};
							                if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('leaderboard',charttitle);
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
					else if(chart==18)
					{
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="leaderboard" selected >Leaderboard</option><option value="table">Table</option>');	
						}
						$('#switch-charts').show();
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
                                            globalmetadata ={'drilldown':'enable','x':'assignee','xcol':'Normalized Portfolio size(Count)','ycol':'Normalized Portfolio strength(Score)','zcol':'Assignee Name'};
							                if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('leaderboard',charttitle);
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
					else if(chart==20)
					{
						$('#top-companies-filters').show();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="3Dmap">Map</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'marketcountry','y':'assignee','xcol':'Market Country','ycol':'Assignee Name','zcol':'Count'};
											
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
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="3Dmap">Map</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'innovationcountry','y':'assignee','xcol':'Country of innovation','ycol':'Assignee Name','zcol':'Count'};
											
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
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="scatter">Scatter</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'overalllegalstatus','y':'count','xcol':'Overall Family Leagal Status','ycol':'Count'};
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
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'typeofassignee','y':'overalllegalstatus','xcol':'Type of assignee','ycol':'Overall Family Legal Status','zcol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'overalllegalstatus','xcol':'Assignee Name','ycol':'Overall family legal status','zcol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','y':'overalllegalstatus','x':'categorisation','hlevel':hlevel,'toplevel':level,'xcol':'Category','ycol':'Overall family legal status','zcol':'Count'};	
											
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
						  $('#switchchart').html('').append('<option value="pie" selected>Pie</option><option value="hbar">Horizontal Bar</option><option value="bar">Vertical Bar</option><option value="scatter">Scatter</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'familymemberslegalstatus','y':'count','xcol':'Family members legal status','ycol':'Count'};
											
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
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'assignee','y':'familymemberslegalstatus','xcol':'Assignee Name','ycol':'Family members legal status','zcol':'Count'};	
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
						  $('#switchchart').html('').append('<option value="3Dbar" selected>Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dscatter">Scatter</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
											globalmetadata ={'drilldown':'enable','x':'marketcountry','y':'familymemberslegalstatus','xcol':'Market Country','ycol':'Family members legal status','zcol':'Count'};	
											
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
					else if(chart==31)
					{
						$('#top-companies-filters').hide();
						if(currentchartid!=previouschartid)
						{
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option>');	
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
							    $('#insightdescription').html("This chart highlights the preferred classes in the subject technology domain.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].ipc);
										  y.push(data[i].count);
										  z.push(data[i].ipc_definition);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'ipc','y':'count'};
											
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
					else if(chart==32)
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
							  $('#insightdescription').html("THIS CHART HIGHLIGHTS THE TOP IPC CLASSES WHICH ARE PREFERRED BY THE TOP COMPANIES."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  var classdef = [];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].ipc);
										  z.push(data[i].count);
										  y.push(data[i].parentassignee);
										  classdef.push(data[i].ipc_definition);
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalclasseshover = classdef;
											globalmetadata ={'drilldown':'enable','x':'ipc','y':'assignee'};
											
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
					else if(chart==33)
					{
						$('#top-companies-filters').hide();
						if(currentchartid!=previouschartid)
						{
						$('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="scatter">Scatter</option>');	
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
							    $('#insightdescription').html("This chart highlights top cpc classes preferred in the subject technology domain.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cpc);
										  y.push(data[i].count);
										  z.push(data[i].cpc_definition);
										  
									  } 
										    globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'cpc','y':'count'};
											
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
					else if(chart==34)
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
							  $('#insightdescription').html("THIS CHART HIGHLIGHTS THE TOP CPC ClASSES PREFERRED BY THE TOP COMPANIES."); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z =[];
									  var classdef =[];
									  for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].cpc);
										  z.push(data[i].count);
										  y.push(data[i].parentassignee);
										  classdef.push(data[i].cpc_definition)
									  }
									  
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalclasseshover = classdef;
											globalmetadata ={'drilldown':'enable','x':'cpc','y':'assignee'};
											
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
					else if(chart==35)
					{
						$('#top-companies-filters').hide();
						
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline" selected>Mutiple Line</option><option value="bubble">Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');	
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
							    $('#insightdescription').html("This chart presents a comparison among the year-by-year patent filings by various business type of assignees working in the subject technology domain.");
							    $('#processing-div').remove();
							    if(data!=0)
								  {
									  exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  y.push(data[i].typeofassignee);
										  x.push(data[i].pdate);
										  z.push(data[i].z);
										  
									  }
									 
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'typeofassignee','xcol':'Priority/Filing Year','ycol':'Type of Assignee','zcol':'Count'};
											
											if(currentchartid==previouschartid)
											{
									            changecharttype($('#switchchart').val(),charttitle);
											}
											else
											{
												changecharttype('3Dline',charttitle);
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
					else if(chart==36)
					{
						
						$('#top-companies-filters').hide();
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');		
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var typeofassignee = $(this).data('typeofassignee');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&typeofassignee="+encodeURIComponent($(this).data('typeofassignee'))+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							  $('#insightdescription').html("This chart highlights the year-by-year patent filings based on the region of the various "+typeofassignee+" players filing patents in this domain."); 
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
										  y.push(data[i].headquarter);
										  z.push(data[i].z);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'headquarter','z':'typeofassignee','zvalue':typeofassignee,'xcol':'Priority/Filing Year','ycol':'Companies Region/headquarter','zcol':'Count'};	
											
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
					else if(chart==37)
					{
						
						$('#top-companies-filters').hide();
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="pie">Pie</option><option value="hbar">Horizontal Bar</option><option value="bar" selected>Vertical Bar</option><option value="line">Line</option><option value="table">Table</option>');	
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var typeofassignee = $(this).data('typeofassignee');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&typeofassignee="+encodeURIComponent($(this).data('typeofassignee')),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							  $('#insightdescription').html("This chart illustrates the number of "+typeofassignee+" players corresponding to different regions"); 
							  $('#processing-div').remove();
							 	 if(data!=0)
								  {
									 exportdata = data;
									  var x =[];
									  var y =[];
									  var z=[];
									 for (i = 0; i < data.length; i++) 
								      {
										  x.push(data[i].headquarter);
										  y.push(data[i].count);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'headquarter','y':'count','z':'typeofassignee','zvalue':typeofassignee,'xcol':'Companies region/headquarter','ycol':'Count'};	
											
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
					else if(chart==39)
					{
						
						$('#top-companies-filters').show();
						if(currentchartid!=previouschartid)
						{
						  $('#switchchart').html('').append('<option value="3Dbar">Vertical Bar</option><option value="h3Dbar">Horizontal Bar</option><option value="3Dline">Mutiple Line</option><option value="bubble" selected>Bubble</option><option value="heatmap">Heatmap</option><option value="table">Table</option>');		
						}
						$('#switch-charts').show();
						$('#insight-suboption').show();
						
						var typeofassignee = $(this).data('typeofassignee');
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&reportid="+reportid+"&chart="+chart+"&typeofassignee="+encodeURIComponent($(this).data('typeofassignee'))+"&topvalue="+$("#top-comp-opt").val()+"&pdateopt="+$('#priority-year-opt').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						  {
							  $('#insightdescription').html("This chart presents a comparison among the year-by-year patent filings by various "+typeofassignee+" players working in the subject technology domain"); 
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
										  y.push(data[i].parentassignee);
										  z.push(data[i].z);
										  
									  }
									        globalx = x;
											globaly  = y;
											globalz = z;
											globalmetadata ={'drilldown':'enable','x':'pdate','y':'assignee','z':'typeofassignee','zvalue':typeofassignee,'xcol':'Priority/Filing Year','ycol':'Assignne Name','zcol':'Count'};	
											
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
					else if(chart==40)
					{
						$('#top-companies-filters').hide();
						$('#switch-charts').hide();
						$('#emerging-companies-filters').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=getinsight&reportid="+reportid+"&chart="+chart+"&emergingplayer="+$('input[name=emergingplayer]:checked').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (result) 
						  {
							  if($('input[name=emergingplayer]:checked').val()=='precovid')
							  {
								  $('#insightdescription').html("This chart shows list of all companies that were only filing before covid");
							  }
							  else if($('input[name=emergingplayer]:checked').val()=='postcovid')
							  {
								  $('#insightdescription').html("This chart shows list of all companies that have emerged after covid");
							  }
							  else if($('input[name=emergingplayer]:checked').val()=='prendpostcovid')
							  {
								  $('#insightdescription').html("This chart shows list of all companies that are actively filing in both eras");
							  }
							  
							  
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
					else if(chart==41)
					{
						$('#top-companies-filters').hide();
						$('#switch-charts').hide();
						$('#emerging-companies-filters').show();
						$('#insight-suboption').show();
						$.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: "action=getinsight&reportid="+reportid+"&chart="+chart+"&emergingplayer="+$('input[name=emergingplayer]:checked').val(),
                           timeout: 600000,
						   dataType: "json",
						   success: function (result) 
						  {
							  if($('input[name=emergingplayer]:checked').val()=='precovid')
							  {
								  $('#insightdescription').html("This chart highlights the region of the companies filing patents before COVID-19");
							  }
							  else if($('input[name=emergingplayer]:checked').val()=='postcovid')
							  {
								  $('#insightdescription').html("This chart highlights the region of the companies that only started filing patents post-COVID-19");
							  }
							  else if($('input[name=emergingplayer]:checked').val()=='prendpostcovid')
							  {
								  $('#insightdescription').html("This chart highlights the region of the companies filing patents in both the pre and post-COVID-19 phase");
							  }
							  
							  
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
				
				$('body').on('change','input[type=radio][name=emergingplayer]',function(){ 
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
				
/*-------------------------comment---------------------*/
				$("#updatecmtbtn").click(function (e) {
                      e.preventDefault();
                     
					if(sectiontoshow_after_applyingfilter==2)
					{
					var chartid = showchart_after_applyingfilter;
					}
					else if(sectiontoshow_after_applyingfilter==3)
					{
					var chartid = $('#pubno').text();	
					}
					
                    var comment = $('#comment').val();
					
					
					comment = encodeURIComponent(comment);
		           $("#updatecmtbtn").prop("disabled", true);

                     $.ajax({
                           type: "POST",
                           url: "getcomments.php",
                           data: "comment="+comment+"&action=addcomment&reportid="+reportid+"&chartid="+chartid,
						   dataType: "json",
                          success: function (data) 
						  {
                             $("#updatecmtbtn").prop("disabled", false);
							        if(data[0].accessright==0)
									 {
										 alert('You are not authorised to use this Module.');
										 window.location ="home.php";
										 return false;
									 }
									 if(data[0].rid!='')
									 {
									   
									  $("#comment").val('');
									  fetchcommentrecords(data[0].rid,data[0].chartid);
									  
									 }
									 else
									 {
							           alert('Something Went Wrong');
									 }
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           $("#updatecmtbtn").prop("disabled", false);
                          }
        });

    });
	
	     //fetchcommentrecords(reportid,showchart_after_applyingfilter);       
         function fetchcommentrecords(reportid,chartid)
		 {
			           $.ajax({
                                type: 'POST',
                                url: 'getcomments.php',
								data:'action=viewchartcomment&reportid='+reportid+'&chartid='+chartid,
								dataType: "json",
                                success: function(data)
			                     {
									 
									 if(data==0)
									 {
										$('#commentbody').empty();
									    $('#commentbody').append("<div>Currently, No comments Available</div>");
									 }
									 else 
									 {
										 if(data[0].accessright==0)
									     {
										 alert('You are not authorised to use this Module.');
										 window.location ="home.php";
										 return false;
									     }
									 
									     var content ="";
							            for (i = 0; i < data.length; i++) 
								           {
											
											/*var deletemsg ='';
											if(data[i].flag=='myself')
											{
												deletemsg = "<a class='commentdelbtn' style='color:#ff5722;margin-left:5px;cursor:pointer;'  title='Delete this comment' data-cid='"+data[i].cid+"'>Delete <i class='fa fa-trash' style=''></i></a>";
											}*/
                                            content = content+"<div><div style='text-align:right;'>"+data[i].ctime+" "+data[i].creationdate+"</div><div class='"+data[i].flag+"'><div class='msg-outer'><div class='msg-createdby'>"+data[i].commentby+"</div>"+data[i].comment.replace(/\n/g, "<br />")+"</div></div></div>";
											}
									  $('#commentbody').empty();
									  $('#commentbody').append(content);
									  $('.profile').initial();
									 }
									 
								 }
					   });
		 }
		 
		 $('body').on('click','.delcat',function(){
				 var cid = $(this).data("cid");
				 var req = confirm("Are you sure you want to delete it!! ");
		           if(req)
			       {
			          $.ajax({
                                type: 'POST',
                                url: 'request.php',
								data:'cid='+cid+"&action=deletecat&reportid="+reportid,
								dataType: "json",
                                success: function(data)
			                     {
									if(data[0].accessright==0)
									 {
										 alert("You don't have right to delete this category.");
										 return false;
									 }
									 
									 if(data[0].cid!='')
									 {
									  alert('Deleted');
									  retrievetext(reportid, $('#pubno').text());
		                              
									 }
									 else
									 {
							           alert('Something Went Wrong');
									 }
									 
							       
								 }
					   });
			       }
			  });
			  
			  $("#categoryForm").validate();
			  
			  $("#savecatbtn").click(function (e) {
                      e.preventDefault();
                     
				if($('#categoryForm').valid()==true)
					  {	
					var addtopubno = $('#pubno').text();
					
		           $("#savecatbtn").prop("disabled", true);
                     var catfd = $("#categoryForm").serialize();
                     $.ajax({
                           type: "POST",
                           url: "request.php",
                           data: catfd+"&action=addcatrow&reportid="+reportid+"&pubno="+addtopubno,
						   dataType: "json",
                          success: function (data) 
						  {
                             $("#savecatbtn").prop("disabled", false);
							        if(data[0].accessright==0)
									 {
										 alert('You are not authorised to use this Module.');
										 window.location ="home.php";
										 return false;
									 }
									 if(data[0].cid!='')
									 {
									  //$("#addCatForm").modal('hide');
                                       $('#addcatbtn').addClass('category-area-not-expanded');
		                               $('#category-right-side-view-wrapper-popup').hide();									  
									  retrievetext(reportid, addtopubno);
									  
									 }
									 else
									 {
							           alert('Something Went Wrong');
									 }
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           $("#savecatbtn").prop("disabled", false);
                          }
        });
					  }

    });
	
	$("body").on("change","input[type=radio][name=taggedvalue]",function(){
		   if($(this).val()=='Other')
		   {
			   $('#hiddentag').css({'display':''});
			   $('#definetag').prop('required',true);
		   }
		   else
		   {
			    $('#hiddentag').css({'display':'none'});
				$('#definetag').prop('required',false);
		   }
	   }) ;  

        $("#taggingForm").validate();
			  
			  $("#savetaggingbtn").click(function (e) {
                      e.preventDefault();
                     
				if($('#taggingForm').valid()==true)
					  {	
					var addtopubno = $('#pubno').text();
					
		           $("#savetaggingbtn").prop("disabled", true);
                     var catfd = $("#taggingForm").serialize();
                     $.ajax({
                           type: "POST",
                           url: "request.php",
                           data: catfd+"&action=addtagging&reportid="+reportid+"&pubno="+addtopubno,
						   dataType: "json",
                          success: function (data) 
						  {
                             $("#savetaggingbtn").prop("disabled", false);
							        if(data[0].accessright==0)
									 {
										 alert('You are not authorised to use this Module.');
										 window.location ="clienthome.php";
										 return false;
									 }
									 if(data[0].success!='')
									 {
									  $("#tagging-right-side-view-wrapper-popup").hide();	 
									  $('.viewpub').each(function(){
										  if($(this).data('pubno')==addtopubno)
										  {
											  listrowid = $(this).data('listrowid');
											  if(data[0].tagging!='' && data[0].tagging!=null)
											  {
											  $('#list-tagging-'+listrowid).text(data[0].tagging);
											  }
										  }
									  });
									     
										 if(data[0].tagging!='' && data[0].tagging!=null)
										  {
										  $('#pubtagging').html('<img src="images/tagging-icon.svg" height="15"/> '+data[0].tagging);
										  }
										  else
										  {
										  $('#pubtagging').html('');  
										  }
									  //retrievetext(reportid, addtopubno);
									  getcustometags(reportid);
									  appendtotaggingfilter(data[0].tagging);
									 }
									 else
									 {
							           alert('Something Went Wrong');
									 }
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           $("#savetaggingbtn").prop("disabled", false);
                          }
        });
					  }

    });	 
	
	var customtags = [];
       getcustometags(reportid);
       function getcustometags(reportid)
		 {
			 customtags = [];
			           $.ajax({
                                type: 'POST',
                                url: 'request.php',
								data:'action=custometags&reportid='+reportid,
								dataType: "json",
                                success: function(data)
			                     {
									 
									 
									 if(data==0)
									 {
										
									 }
									 else 
									 {
										
										 if(data[0].accessright==0)
									     {
										 alert('You are not authorised to use this Module.');
										 window.location ="clienthome.php";
										 return false;
									     }
									 
									     
							            for (var i = 0; i < data.length; i++) 
								           {
											 if(data[i]!='' && data[i]!=null)
											 {
											  customtags.push(data[i]);
											  //alert(data[i]);
											 }
											
                                            //content = content+'<input type="radio" id="st-ctag-'+i+'" name="taggedvalue" value="'+data[i].tagging+'"><label for="st-ctag-'+i+'">'+data[i].tagging+'</label><br/>';
											}
									  
									  
									 }
									 appendcustomeopt();
								 }
								 
					   });
					   
					   
		 }
         
function appendcustomeopt()
{
	
	$('#custom-tags').empty();
	 var content ='';
	 
	                                     for (var i = 0; i < customtags.length; i++) 
								           {
											
                                            content = content+'<div class="pretty p-default p-round"><input type="radio" id="st-ctag-'+i+'" name="taggedvalue" value="'+customtags[i]+'"><div class="state p-danger"> <label for="st-ctag-'+i+'">'+customtags[i]+'</label></div></div><br/>';
											}
											
											
									    $('#custom-tags').html(content);
									  
									     /*$('body .iradio').iCheck({
                                               checkboxClass: 'iradio_minimal-red',
                                               radioClass: 'iradio_minimal-red',
                                               increaseArea: '20%' // optional
                                            });*/
}
function appendtotaggingfilter(optval)
{
	var optneedtoopend = 1;
	$('#tagging > option').each(function(){
		if($(this).val()==optval)
		{
			optneedtoopend = 0;
			return false;
		}
		
	});
	
	if(optneedtoopend==1)
	{
		$('#tagging').append('<option>'+optval+'</option>');
	}
}
function exportchartdata()
{
	var x = new CSVExport(exportdata);
			return false;
}

function changecharttype(charttype,charttitle)
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
		   
		   
									  $('#tester').remove();
					                  $('.mc-iv-right-tester-outer').append("<div id='tester' style='width:100%;height:100%;'></div>");
									  TESTER = document.getElementById('tester');
									  
									 
									  
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
												  
												  if($('.ins-selected').data('chart')=='31' || $('.ins-selected').data('chart')=='33')
												  {
													  data[0].customdata = globalz;
													  data[0].hovertemplate = "<b>%{x}</b> :%{y} <br>%{customdata}<extra></extra>";
												  }
												  
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
									   textposition: 'auto',											   
											      }];
												  
											if($('.ins-selected').data('chart')=='31' || $('.ins-selected').data('chart')=='33')
												  {
													  data[0].customdata = globalz;
													  data[0].hovertemplate = "<b>%{y}</b> :%{x} <br>%{customdata}<extra></extra>";
												  }
	  
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
										
                                          if($('.ins-selected').data('chart')=='32' || $('.ins-selected').data('chart')=='34')
												  {
													  data[0].customdata = globalclasseshover;
													  data[0].hovertemplate = "<b>%{x}</b> :%{y}(%{text})<br>%{customdata}<extra></extra>";
												  }										
												  
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
												  
											if($('.ins-selected').data('chart')=='32' || $('.ins-selected').data('chart')=='34')
												  {
													  data[0].customdata = globalclasseshover;
													  data[0].hovertemplate = "<b>%{y}</b> :%{x}(%{text})<br>%{customdata}<extra></extra>";
												  }	
												  
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
											TESTER = document.getElementById('tester');
											
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
													   if(typeof globalmetadata.z !== "undefined")
													   {
		                                               var pointclicked_zcol = globalmetadata.z;
		                                               pts.push('filtercolumn[]='+encodeURIComponent(pointclicked_zcol));
		                                               var pointclicked_zvalue = globalmetadata.zvalue;
		                                               ptsval.push('filtercolumnvalues[]='+encodeURIComponent(pointclicked_zvalue));
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
										   
										
                                        var leaderboardlayout	= {font: {family: 'Bauziet, Telegraf,Bahnschrift, sans-serif'},
										    colorway : tracecolor,title:{text:charttitle.toUpperCase(),xref:'container',x:0,xanchor:'left',family: 'Bauziet', color:'#1E150B', size:24, pad:{l : 20}},paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1',
											yaxis: {automargin: true,showgrid : false,zeroline : false, title:'Score >>'},
											xaxis: {showgrid : false, zeroline : false, title:'Portfolio size >>'},
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
												]};	
                                        <?php
					                          if(isset($_SESSION["theme"]) && $_SESSION["theme"]!='')
			                                  {
				                               if($_SESSION["theme"]=='blue')
				                               {
					                            ?>
							                     leaderboardlayout.paper_bgcolor ='#f0efef';
								                 leaderboardlayout.plot_bgcolor ='#f0efef';
								        
							                     <?php
				                                }
				  
			                                   }
					                      ?>												
                                        Plotly.plot(
										TESTER, 
										data,
										leaderboardlayout, 
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
									  else if(charttype=='table')
									  {
										  
										  
										  
										  var tableheader =[];
										  var tabledata =[];
										       if(typeof globalmetadata.xcol !== "undefined")
													   {
														   tableheader.push("<th>"+globalmetadata.xcol+"</th>");
		                                                     for(var i=0; i < globalx.length; i++)
	                                                          {
		                                                              tabledata.push("<td>"+globalx[i]+"</td>");
                                                              }
													   }
												if(typeof globalmetadata.ycol !== "undefined")
													   {
														   tableheader[0] = tableheader[0]+"<th>"+globalmetadata.ycol+"</th>";
		                                                     for(var i=0; i < globaly.length; i++)
	                                                          {
		                                                              tabledata[i] = tabledata[i]+"<td>"+globaly[i]+"</td>";
                                                              }
													   }
												if(typeof globalmetadata.zcol !== "undefined")
													   {
														   tableheader[0] = tableheader[0]+"<th>"+globalmetadata.zcol+"</th>";
		                                                     for(var i=0; i < globalz.length; i++)
	                                                          {
		                                                              tabledata[i] = tabledata[i]+"<td>"+globalz[i]+"</td>";
                                                              }
													   }
                                                      
													   var tablebody = '';
													   for(var i=0; i < tabledata.length; i++)
	                                                          {
		                                                              tablebody =  tablebody+"<tr>"+tabledata[i]+"</tr>";
                                                              }
													   
													   $('#tester').append('<div class="ins-datatbl-outer"><table class="table ins-datatbl"><thead>'+tableheader[0]+'</thead><tbody>'+tablebody+'</tbody></table></div>')
										 
												  
												  
												  
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
			
			if(chart!=5 && chart!=17 && chart!=18 && chart!=40 && chart!=41)
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
			
			if(chart==3 || chart==8 || chart==9 || chart==14 || chart==15 || chart==35 || chart==39 || chart==36)
			{
				$('#pdate-filter').show();
			}
			else
			{
				
				$('#pdate-filter').hide();
			}
		}
		
		function showemergingplayerfilter(chart)
		{
			
			if(chart==40 || chart==41)
			{
				$('#emerging-companies-filters').show();
			}
			else
			{
				
				$('#emerging-companies-filters').hide();
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
			   if($(this).hasClass('notopened'))
			   {
				   $(this).removeClass('notopened')
				   $(this).addClass('opened')
				   openFullscreen();
			   }
			   else
			   {
				   $(this).removeClass('opened')
				   $(this).addClass('notopened')
				   exitFullscreen();
			   }
			   
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

$(elem).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function(e) {
    var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;
    var event = state ? 'FullscreenOn' : 'FullscreenOff';
   
	if(event == 'FullscreenOn')
	{
		$('#insight-navigation').show();
	}
	else
	{
		$('#insight-navigation').hide();
		$('#open-max-window').removeClass('opened');
		$('#open-max-window').addClass('notopened');
	}
});
	//----------context-menu-----------
	
	$("#claims").mouseup(function (e)
	{
	
	  textselection = window.getSelection();
    
      if(textselection.toString().length > 1)
	  {
		$('#saveclaims').show();
	    var selctionrange = textselection.getRangeAt(0);
        var newNode = document.createElement("span");
		newNode.setAttribute("class","texttohighlight");
        selctionrange.surroundContents(newNode);  		
      }
      
	  textselection.removeAllRanges();
  
 });
	  
	  $("#claims").on("contextmenu","span.texttohighlight",function(e){
		  $('#saveclaims').show();
		  $('span.texttohighlight').removeClass('currenthighlightedspan');
		  $(this).addClass('currenthighlightedspan');
		   e.preventDefault();
		   e.stopPropagation();
		   //var pos=$("#contextbox").position();
		  $("#contextbox").css({top : e.pageY, left:e.pageX });
		  $("#contextbox").show();
		  
		 });
	       
	   	   $("#contextbox").on("click","#underline",function(e){
			  
		    $(".currenthighlightedspan").css({"text-decoration":"underline"});
			$(".currenthighlightedspan").css("background-color","transparent");
		  
	  });
	  
	        $("#contextbox").on("click","#removehighlight",function(e){
			   var currentdata   =   $(".currenthighlightedspan").text();
			    $(".currenthighlightedspan").before(currentdata);
				$(".currenthighlightedspan").remove();
				$("#colorboxdiv").hide();
				$("#contextbox").hide();
			 
		  
	  });
	  
	  $("#contextbox").on("click","#bold",function(e){
			  
			    $(".currenthighlightedspan").css("background-color","transparent");
				$(".currenthighlightedspan").css({"font-weight":"bold"});
		   
		  
	  });
	 
	  $("#contextbox").on("click","#colorbox",function(e){
	      $("#colorboxdiv").css({top : e.pageY, left:e.pageX });
		  $("#colorboxdiv").show();
		  $("#contextbox").hide();
			  
	  });  
	  
	  $("#colorboxdiv").on("click","div",function(){
	   var colorh =$(this).css('backgroundColor');
		$(".currenthighlightedspan").css("background-color","transparent");
		$(".currenthighlightedspan").css("color",colorh);
	  });
	  
	  
	  
	   $("#claims").on("click",function(){
		  $("#contextbox").hide();
          		  
		   
	   });
	   
	   $("#colorboxdiv div").css({display:"inline-block", border:"1px solid #1b75bc", margin:"5px", height:"22px",width:"22px",cursor:"pointer"});
	$("#closecolor").on("click",function(){
	$("#colorboxdiv").hide();
	});
		$("#saveclaims").click(function (e) {
                      
				   e.preventDefault();
                    var claims = $('#claims').html();
					    claims = encodeURIComponent(claims);
					var pid = $(this).prop('data-pid');
					
					
		           $("#saveclaims").prop("disabled", true);
                   $("#saveclaims").html("Saving....<img src='images/process.gif'/>");
                     $.ajax({
                           type: "POST",
                           url: "request.php",
                           data: "updatedclaim="+claims+"&pid="+pid+"&action=updateclaims&reportid="+reportid,
						   dataType: "json",
                          success: function (data) 
						  {
                             $("#saveclaims").prop("disabled", false);
							  $("#saveclaims").text("Save");      
									 if(data[0].success!='')
									 {
									  
                                             //saved
									 
									  
									 }
									 else
									 {
							            alert('Sorry, We can not save your changes at this moment.');
									 }
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           $("#saveclaims").prop("disabled", false);
                          }
        });
		});	
		
		$('#pre-ins').on('click',function(){
			var indexv = 0;
			$('.getinsight').each(function(index){
				if($(this).hasClass('ins-selected'))
			     {
				  indexv =  index;
				  return false;
			     }
			})
			if(indexv!=0)
			{
			   $('.getinsight').eq(indexv-1).trigger('click');
			}
		});
		$('#nxt-ins').on('click',function(){
			
			var indexv = 0;
			$('.getinsight').each(function(index){
				if($(this).hasClass('ins-selected'))
			     {
				  indexv =  index;
				  return false;
			     }
			})
			
			$('.getinsight').eq(indexv+1).trigger('click');
			
		});
		
		$("#closetaggingbtn").on('click',function(){
		   $('#tagging-right-side-view-wrapper-popup').hide();
	   });
	   
	   $("#closecategorybtn").on('click',function(){
		   $('#addcatbtn').addClass('category-area-not-expanded');
		   $('#category-right-side-view-wrapper-popup').hide();
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
	
  