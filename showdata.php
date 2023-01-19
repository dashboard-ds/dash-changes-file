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
    
	$data= array();
if($_GET['action'] =='showdata')
{
				 
    $columnname = $_GET['filtercolumn']; 
	$columnvalues = $_GET['filtercolumnvalues']; 
	
	if(isset($_GET['hlevel']))
	{
	$hlevel = $_GET['hlevel']; 
	}
	
	if(isset($_GET['toplevelcat']))
	{
		$toplevelcat = $_GET['toplevelcat']; 
	}
	
	/*------------Setting limit and offset----------*/		
	//echo count($columnname);
		
        /* if (isset($_POST['page_no']) && $_POST['page_no']!="") 
	       {
            $page_no = $_POST['page_no'];
           } 
	       else 
	       {
            $page_no = 1;
           }
	       $total_records_per_page = 50;
		   
		   if (isset($_POST['perpagerecord']) && $_POST['perpagerecord']!="") 
	       {
            $total_records_per_page = $_POST['perpagerecord'];
           } 
		   
	       $offset = ($page_no-1) * $total_records_per_page;*/
        /*----------------------------------------------*/		   
			
		/*-------making connection-------*/	
	     try 
		 {
           $dbhost = 'localhost';
           $dbname='icueriou_dashrep';
           $dbuser = 'icueriou_dashrep';
           $dbpass = 'GPHSiTn9Qw8E';
           $connec = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=UTF8", $dbuser, $dbpass);
         }
		 catch (PDOException $e) 
		 {
           echo "Error : " . $e->getMessage() . "<br/>";
           die();
         }
		 
		 /*------------Declaring global arrays----*/
		 $myCondition = Array();
		 $bindparameter = Array();
		 $records = Array();
		 $metainfo = Array();
		 
		 
		 /*-----------check filter-------------*/
		 
	    if(isset($_GET['reportid']) && trim($_GET['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_GET["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 $data['records'] = '';
			 $data['metainfo'] = '';
			 $data['error'] = 'Report Id missing';
			 exit;
		 }
	if(isset($_GET['pubdate']) && trim($_GET['pubdate'])!='' && trim($_GET['pubdate'])=='pubdate')
		 {
			 $operator = $_GET['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_GET["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_GET["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_GET["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_GET["pubstart"];
				   $bindparameter[':pubend'] = $_GET["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_GET['pdate']) && trim($_GET['pdate'])!='' && trim($_GET['pdate'])=='pdate')
		 {
			 $operator = $_GET['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_GET["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_GET["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_GET["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_GET["pstart"];
				   $bindparameter[':pend'] = $_GET["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_GET['appdate']) && trim($_GET['appdate'])!='' && trim($_GET['appdate'])=='appdate')
		 {
			 $operator = $_GET['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_GET["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_GET["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_GET["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_GET["appstart"];
				   $bindparameter[':append'] = $_GET["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		if(isset($_GET['updationdate']) && trim($_GET['updationdate'])!='' && trim($_GET['updationdate'])=='updationdate')
		 {
			 $operator = $_GET['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_GET["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_GET["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_GET["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_GET["updationstart"];
				   $bindparameter[':updateddateend'] = $_GET["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_GET['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_GET['relevancy'] as $selectedOption)
		             {
			           $parameter ="relevancy = :relevancy".$s; 
			           array_push($relevancy,$parameter);
					   $bindparameter[':relevancy'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($relevancy)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $relevancy).")");
		            }
		           else
		           {
			        array_push($myCondition,$relevancy);
		           } 
		 }
		  
		 if(isset($_GET['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_GET['assignee'] as $selectedOption)
		             {
			           $parameter ="parentassignee = :assignee".$s; 
			           array_push($assignee,$parameter);
					   $bindparameter[':assignee'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($assignee)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $assignee).")");
		            }
		           else
		           {
			        array_push($myCondition,$assignee);
		           } 
		 }
		 
		 if(isset($_GET['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_GET['level1'] as $selectedOption)
		             {
			           $parameter ="level1 = :level1".$s; 
			           array_push($level1,$parameter);
					   $bindparameter[':level1'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($level1)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $level1).")");
		            }
		           else
		           {
			        array_push($myCondition,$level1);
		           } 
		 }
		 
		 if(isset($_GET['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_GET['level2'] as $selectedOption)
		             {
			           $parameter ="level2 = :level2".$s; 
			           array_push($level2,$parameter);
					   $bindparameter[':level2'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($level2)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $level2).")");
		            }
		           else
		           {
			        array_push($myCondition,$level2);
		           } 
		 }
		 
		 if(isset($_GET['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_GET['level3'] as $selectedOption)
		             {
			           $parameter ="level3 = :level3".$s; 
			           array_push($level3,$parameter);
					   $bindparameter[':level3'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($level3)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $level3).")");
		            }
		           else
		           {
			        array_push($myCondition,$level3);
		           } 
		 }
		 
		 if(isset($_GET['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_GET['level4'] as $selectedOption)
		             {
			           $parameter ="level4 = :level4".$s; 
			           array_push($level4,$parameter);
					   $bindparameter[':level4'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($level4)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $level4).")");
		            }
		           else
		           {
			        array_push($myCondition,$level4);
		           } 
		 }
		 
		 if(isset($_GET['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_GET['tagging'] as $selectedOption)
		             {
			           $parameter ="tagging = :tagging".$s; 
			           array_push($tagging,$parameter);
					   $bindparameter[':tagging'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($tagging)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $tagging).")");
		            }
		           else
		           {
			        array_push($myCondition,$tagging);
		           } 
		 }
	

    $marketcoutryflag = '';	
	$familymemberslegalstatusflag = '';
	for($i=0;$i<count($columnname);$i++)
	{
			           
		  if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='pdate')
		  {
			 $parameter = "year(pdate) = :clickedpstart";
			 array_push($myCondition,$parameter);
		     $bindparameter[':clickedpstart'] = $columnvalues[$i];
          }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='assignee')
		 {
			    $parameter ="parentassignee = :clickedassignee"; 
			    array_push($myCondition,$parameter);
			    $bindparameter[':clickedassignee'] = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='innovationcountry')
		 {
			     $parameter ="left(r.epriorityno,2)= :clickedinnovationcc"; 
			     array_push($myCondition,$parameter);
			     $bindparameter[':clickedinnovationcc'] = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='marketcountry')
		 {
			    $parameter ="family like :clickedmarketcc"; 
			    array_push($myCondition,$parameter);
			    $bindparameter[':clickedmarketcc'] = '%'.$columnvalues[$i].'%';
				$marketcoutryflag  = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='relevancy')
		 {
			     $parameter ="relevancy = :clickedrelevancy"; 
			     array_push($myCondition,$parameter);
			     $bindparameter[':clickedrelevancy'] = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='typeofassignee')
		 {
			     $parameter ="typeofassignee = :clickedtypeofassignee"; 
			     array_push($myCondition,$parameter);
			     $bindparameter[':clickedtypeofassignee'] = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='headquarter')
		 {
			     $parameter ="headquarter = :clickedheadquarter"; 
			     array_push($myCondition,$parameter);
			     $bindparameter[':clickedheadquarter'] = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='overalllegalstatus')
		 {
			     $parameter ="legalstate = :clickedlegalstate"; 
			     array_push($myCondition,$parameter);
			     $bindparameter[':clickedlegalstate'] = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='familymemberslegalstatus')
		 {
			    $parameter ="familymembers_legalstatus like :clickedfamilymemberslegalstatus"; 
			    array_push($myCondition,$parameter);
			    $bindparameter[':clickedfamilymemberslegalstatus'] = '%'.$columnvalues[$i].'%';
				$familymemberslegalstatusflag = $columnvalues[$i];
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='ipc')
		  {
			 $parameter = "ipc = :clickedipc";
			 array_push($myCondition,$parameter);
		     $bindparameter[':clickedipc'] = $columnvalues[$i];
          }
		  
		  if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='cpc')
		  {
			 $parameter = "cpc = :clickedcpc";
			 array_push($myCondition,$parameter);
		     $bindparameter[':clickedcpc'] = $columnvalues[$i];
          }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='categorisation' && $hlevel=='l1')
		 {
			      $s=0;
				  if($columnvalues[$i]==$toplevelcat)
				  {
					   $parameter ="level1 = :clickedlevel1".$s; 
				       array_push($myCondition,$parameter);
			           $bindparameter[':clickedlevel1'.$s] = $columnvalues[$i];
				       $s++;
				  }
				  else
				  {
					  $parameter ="level2 = :clickedlevel2".$s; 
				      array_push($myCondition,$parameter);
			          $bindparameter[':clickedlevel2'.$s] = $columnvalues[$i];
				      $s++;
				  }
				  
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='categorisation' && $hlevel=='l2')
		 {
			       $s=0;
				   if($columnvalues[$i]==$toplevelcat)
				   {
	                $parameter ="level2 = :clickedlevel2".$s; 
				    array_push($myCondition,$parameter);
			        $bindparameter[':clickedlevel2'.$s] = $columnvalues[$i];
				    $s++;
				   }
				   else
				   {
					 $parameter ="level3 = :clickedlevel3".$s; 
				     array_push($myCondition,$parameter);
			         $bindparameter[':clickedlevel3'.$s] = $columnvalues[$i];
				     $s++;  
				   }
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='categorisation' && $hlevel=='l3')
		 {
			        $s=0;
					if($columnvalues[$i]==$toplevelcat)
				    {
					 $parameter ="level3 = :clickedlevel3".$s; 
					 array_push($myCondition,$parameter);
			         $bindparameter[':clickedlevel3'.$s] = $columnvalues[$i];
					 $s++;
					}
					else
					{
					 $parameter ="level4 = :clickedlevel4".$s; 
					 array_push($myCondition,$parameter);
			         $bindparameter[':clickedlevel4'.$s] = $columnvalues[$i];
					 $s++;
					}
		 }
		 
		 if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='categorisation' && $hlevel=='l4')
		 {
			         $s=0;
					if($columnvalues[$i]==$toplevelcat)
				    {
                      $parameter ="level4 = :clickedlevel4".$s; 
					  array_push($myCondition,$parameter);
			          $bindparameter[':clickedlevel4'.$s] = $columnvalues[$i];
					  $s++;
					}
					else
					{
						$parameter ="level5 = :clickedlevel5".$s; 
					    array_push($myCondition,$parameter);
			            $bindparameter[':clickedlevel5'.$s] = $columnvalues[$i];
					    $s++;
					}
		 }
		 
		 
	}
		 
	if(count($myCondition) > 0)
		         {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT r.rid,r.pubno,r.pubtitle,r.appdate,r.pdate,r.pubdate,r.parentassignee,r.updateddate,r.relevancy,r.relevantpatent_legalstatus,r.tagging,r.family,r.familymembers_legalstatus from relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by r.pubno"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   if($marketcoutryflag !='' && $familymemberslegalstatusflag!='')
			   {
				   $currentrowlegalstate = $row['familymembers_legalstatus'];
			   if($row['family']!= null && trim($row['family'])!='')
			   {
				   $row['family'] =  preg_replace('/\[.*\]/', '', $row['family']);
				   
				   $familyarray = explode("\n", $row['family']);
				   $familylegalstatus = explode("\n", $row['familymembers_legalstatus']);
				   $innerarray = Array();
				   for($s=0;$s<count($familyarray);$s++)
				   {
					   if(substr($familyarray[$s],0,2) == $marketcoutryflag && $familylegalstatus[$s]==$familymemberslegalstatusflag)
					   {
						   $row['srno'] = $num + 1;
			               $num++;
			               array_push($records,$row); 
						   break;
					   }
				           
					   
				   }
				   
				    //$innerarray = array_unique($innerarray);
					
					
					
					
			   }
			   }	   
			   else
			   {
				   $row['srno'] = $num + 1;
			       $num++;
			       array_push($records,$row); 
			   }
			   
		   }
		   if ( $num> 0) 
               { 
	                  $data['records'] = $records;
			         
	           }
		

        if($marketcoutryflag !='' && $familymemberslegalstatusflag!='')
	    {
				   $data['totalrecords'] = $num;
	    }
        else
		{
			$sql =    "SELECT * FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by r.pubno";
		
		$sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute($bindparameter);		 
                
				 
		  $num = 0;
		   if($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   $total_records = $sth->rowCount();
			   $data['totalrecords'] = $total_records;
		   }
		}
	
		
		   
		         //echo json_encode($data);
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
	<!-- Oue select dropdown -->
    <link rel="stylesheet" href="css/chosen.css">
	<link href="css/sumoselect.css" rel="stylesheet"/>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
	<script src="https://use.fontawesome.com/9f102ed215.js"></script>
	<link href="css/tipso.css" rel="stylesheet">
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
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
@font-face 
{
  font-family: 'Bauziet';
  src: url('css/font/Bauziet-Norm-Regular.otf');  
}
@font-face 
{
  font-family: 'Telegraf';
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
.new-header-child svg
{
	height:50px;
}
.wl-pr-text
{
  color : #1E150B;
  text-decoration : underline;
}
html,body
   {
	padding:0px;
	margin:0px;
	width:100%;
	height:100%;
	color: #777777;
    font-family: 'Bauziet','Telegraf','Bahnschrift', sans-serif;
	background-color:#F6EEE1;
    font-size:12px;                                             
	}
.wrapper
{
  display: flex;
  flex-direction: column;
  flex-wrap:nowrap;
  justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  
}	
.header
{
 width:100%;
 height:auto !important;
}
.bottom
{
  width:100%;
  height:auto;
  
}
#middle-container-outer-wrapper
{
display: flex;
flex-direction: row;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
width:100%;
flex-grow:1;
flex-basis:0;
border-top:1px solid #ddd;
border-bottom:1px solid #ddd;

}
#middle-container-left-wrapper
{
display: flex;
flex-direction: row;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
width:100%;
flex-grow:1;
flex-basis:0;
border-right: 1px solid #ddd;
border-left: 1px solid #ddd;
overflow:auto;
}
.filter-right-side-view-wrapper
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
width:200px;
display:none;
padding:5px;
}
.filter-right-side-view
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
flex-grow: 1;
overflow: auto;
flex-basis: 0;
}
.all-filter-options-outer
{
flex-grow: 1;
overflow: auto;
flex-basis: 0;	
}
.all-filter-apply-outer
{
height:auto;
align-items:center;
text-align:center;
padding-top: 5px;	
}
.middle-container {
  display: flex;
  flex-direction: column;
  flex-wrap: nowrap;
  justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  flex-grow:1;
    flex-basis:0;

display:none;
}
.mc-view-publication-inner
{
	display:flex;
	flex-direction: row;
	flex-grow:1;
	
}
.mc-left {
  
  flex-grow: 1;
   
  padding:10px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
  flex-basis:0;
}

.mc-middle {
 
  flex-grow: 1;
  border-right:1px solid #ddd;
  border-left:1px solid #ddd;
  padding:10px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
 flex-basis:0;
 
}

.mc-right {
 
  flex-grow: 1;
  
  padding:10px;
 display:flex;
flex-direction:column;
flex-wrap:nowrap;
 flex-basis:0;
  
}

.logo-header
{
 padding:5px;
}
.project-title
{
padding-bottom:5px;
padding-left:5px;}
.bottom-content
{
padding:5px;}
.mc-left-pubno #pubno
{
  height:auto;
  font-weight:bold;
  font-size:18px;
}
.mc-left-abstract
{
  flex-grow:1;
  overflow-y:auto;
  flex-basis:0;

}
.mc-left-biblio
{
flex-grow:1;
overflow-y:auto;
flex-basis:0;
border-bottom: 2px dotted #ddd;
}

.mc-middle-nav
{
height:auto;
}
.mc-middle-relevant-claim
{
 flex-grow:1;
  display:none;
  overflow:auto;
   flex-basis:0;
   white-space: pre-line;
}
.mc-middle-all-claim
{
  flex-grow:1;
  overflow:auto;
   flex-basis:0;
  
}
#claims
{
	white-space: pre-line;
}
.mc-right-cat
{
flex-grow:1;
overflow:auto;
flex-basis:0;
}
.mc-right-tagging
{
flex-grow:1;
overflow-y:auto;
flex-basis:0;
}
/* Style the buttons */
		   #filterContainer
		   {
		  
		   }
        .filterbtn {
                  border: none;
                 outline: none;
                 padding: 12px 16px;
                 background-color: #f1f1f1;
               cursor: pointer;
			   margin:2px;
                  }

/* Add a light #ddd background on mouse-over */
.filterbtn:hover {
  background-color: #ddd;
}

/* Add a dark background to the active button */
.filterbtn.active {
  background-color: #666;
  color: white;
}
div.sticky {
  position: -webkit-sticky; /* Safari */
  position: sticky;
  top: 0;
  font-size:18px;
  background-color:#F6EEE1;
  padding:5px 0px;
  color:#EF3C18;
  font-family: 'Telegraf';
  
}
::-webkit-scrollbar-track 
	{
    background: #F6EEE1;
    }
	::-webkit-scrollbar {
    width: 13px;
    height: 13px;
	
}

::-webkit-scrollbar-thumb {
    border: 3px solid #F6EEE1;
    border-radius: 10px;
    cursor: pointer;
    background-color: rgba(0,0,0,.2);
}
.cat-box
{
	 padding:5px 8px;
		  
		  background-color:white;
		  color:blue;
		  border:1px solid blue;
		  margin:2px;
		  cursor:pointer;
		  font-size:8px;
		  display:inline-block;
		  color: white;
         font-size: 11px;
         padding: 4px 11px;
         border-radius: 0px 17.5px  0px 17.5px;
		 border-radius: 0px 17.5px  17.5px 0px;
		 border-radius:17.5px;
         background-color: #fff;
         text-transform: capitalize;
         border: 1px solid #d3d3d3;
         margin-top: 6px;
         display: inline-block;
}
.l1
{
 //color:#607d8b;
}
.l2
{
	color:orange;
	color:black;
}
.l3
{
	//color:#f44336;
}
.l4
{
	color:blue;
	color:black;
}
.cat-separator:nth-of-type(odd) 
{
	color:#e4e1e3;
	padding:5px;
}
.cat-separator:nth-of-type(even) 
{
	
	padding:5px;
}

.middle-container-consolidate-view
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
width:100%;
flex-grow:1;
flex-basis:0;
overflow:auto;
padding:10px;
}
.mc-cv-list-view
{
	flex-grow:1;
   overflow:auto;
   flex-basis:0;
}
.mc-cv-list-view table
{
	border-collapse: collapse;
}
.mc-cv-list-view table th,.srno
{
	
	color:#C8BEAA;
	font-weight:normal;
}
.mc-cv-list-view table a:hover
{
	
	text-decoration:none;
}
.mc-cv-list-view table td
{
	
	border-bottom:0.5px solid black;
}
.mc-cv-page-navigation
{
	width:100%;
    height:auto;
}
.mc-pubview-navigation
{
	width:100%;
    height:auto;
}
.visibility-true
{
	display:flex;
}
table
{
	border-collapse: collapse;
}
thead{
	background-color:#EF3C18;
	background-color:#F6EEE1;
	//color: #ffffff;
	text-align: left;
}
thead tr {
    background-color: #EF3C18;
	background-color:#F6EEE1;
    //color: #ffffff;
    text-align: left;
}
table thead{
	position: sticky;
    top: 0;
    z-index: 1000;
}

table th,
table td {
    padding: 3px 4px;
	//width:200px;
	font-size:11px;
}
#categorisation table tbody tr {
    border-bottom: 1px dotted #ddd;
}
#categorisation table tbody td {
    border:none;
}

.headcol {
  position: sticky;
  left:0px;
 
  
}
td.headcol
{
	background-color:#F6EEE1;
}
th.headcol
{
	background-color:#f44336;
	background-color:#F6EEE1;
}

.middle-container-filter-view
{
	 display: flex;
     flex-direction: column;
     flex-wrap: nowrap;
    justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  flex-grow:1;
    flex-basis:0;

overflow:auto;
display:none;
}

#bcktolst,#showcomments
{
	display:none;
}
.pagination_new,.pagination_pubview
{
	
	 list-style-type: none;
  margin: 0;
  padding: 0;
  display:inline-block;
}
.pagination_new li,.pagination_pubview li
{
	display:inline-block;
	margin-left:5px;
	cursor:pointer;
	text-decoration:none;
}
.viewpub
{
	cursor:pointer;
	color:#EF3C18;
	font-weight:bold;
}
.viewpub a
{
	text-decoration:none;
}
.middle-container-insight-view
{
	 display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  flex-grow:1;
    flex-basis:0;

display:none;
}
.mc-iv-left-outer {
  
  width: 250px;
  padding:4px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
 font-size:10px;
}
.mc-iv-left
{
	flex-grow:1;
   overflow:auto;
   flex-basis:0;
}
.mc-iv-right {
 
  flex-grow: 1;
  border-right:1px solid #ddd;
  border-left:1px solid #ddd;
  //padding:4px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
 flex-basis:0;
 background-color:#F6EEE1;
}
.mc-iv-right-navigation
{
	height:auto;
	padding:5px;
	/*background-color: #ecdddd;
	background-color:#e3ddd4;
	background-color:#f7e4c6;*/
	border-bottom: 1px solid #ddd;
}
.mc-iv-right-tester-outer
{
	flex-grow: 1;
}
body .getinsight
{
	cursor:pointer;
	text-transform: uppercase;
}
.insight-group
{
	color:#f44336;
	text-transform: uppercase;
	font-weight:bold;
	margin:5px 0px;
}
.actionbtn
{
	margin:5px;
	color:#f44336;
	background-color: #77777773;
	background-color:#C8BEAA;
    border: none;
	border-radius: 25px;
    color: white;
    padding: 5px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 10px;
    margin: 4px 2px;
    transition-duration: 0.4s;
    cursor: pointer;
	text-transform:uppercase;
	font-family : 'Telegraf';
  font-size : 10px;
  line-height : 14px;
  text-transform : uppercase;
  color : #FFFFFF;
  color : rgb(255, 255, 255);
}
.node circle {
	  fill: #fff;
	  stroke: #f44336;
	  stroke-width: 1px;
	}

	.node text { font: 6px sans-serif; }

	.link {
	  fill: none;
	  stroke: #e7e6e6fa;
	  stroke-width: 1px;
	}
	.insight-main-group
{
	color:#f44336;
	text-transform: uppercase;
	font-weight:bold;
	margin:5px 0px;
	font-size:18px;
}
#filtersform select,#filtersform input[type='date']
{
	border:0px;
	width:170px;
	font-size: .8rem;
    height: 2.5em;
	border: 1px solid #A4A4A4;
    min-height: 14px;
    background-color: #fff;
    border-radius: 2px;
    margin: 0;
	
}
#filtersform label
{
	color:#EF3C18;
}
.filter-label-opt
{
	
}
.mc-iv-left ul{
	list-style-type: none;
	padding-left:10px;
}
.signout
{
	text-decoration:none;
	/*float:right;*/
	color:#777777;
	color:white;
	cursor:pointer;
}
.signout:hover
{
	color:white;
}
.signout:active
{
	color:white;
}
#insightdescription
{
	font-size:8px;
	margin-left:5px;
	color:#757575;
	text-transform: uppercase;
}
.comment-right-side-view-wrapper
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
width:250px;
display:none;
padding:5px;
border-right: 1px solid #ddd;
    border-left: 1px solid #ddd;
}
.comment-right-side-view
{
	flex-grow: 1;
overflow: auto;
flex-basis: 0;
font-size:10px;
}
#cmntbtn_intialconversation
{
	cursor:pointer;
}
.myself{
                background-color:#bfbfbf47;
                border-radius:10px 10px 0px 10px;
                padding:5px 20px 5px 20px;
                min-width: 200px;
                //display:inline-block;
                //float:right;
                //clear:both;
				position:relative;
                
            }
            .other
            {
                background-color: #af473c17;
                 border-radius:0px 10px 10px 10px;
                  padding:5px 20px 5px 20px;
                 min-width: 200px;
                //display:inline-block;
                //float:left;
                //clear:both;
				position:relative;
            }
                   .myselftime{
               
                
                //display:inline-block;
                //float:right;
                //clear:both;
                //margin:0px;
				margin:5px 0px 5px 0px;
				color:#286090;
            }
            .othertime
            {
                
                 //margin-left:10px;
                 //display:inline-block;
                 //float:left;
                //clear:both;
                //margin:0px;
				margin:5px 0px 5px 0px;
				color:#286090;
            }
			.profile-circle
			{
				border-radius:50%;
				width:10px;
				height:10px;
				margin-right:10px;
				
			}
			.profile-outer
			{
				display:inline-block;
				vertical-align:top;
				
			}
			.msg-outer
			{
				
			    display:inline-block;
                vertical-align:top;
				color:#286090;
			}
			.msg-createdby
			{
				font-weight:bold;
			}
			.lnkbtn
			{
				display:inline-block;
				//border: 2px solid #cccccc;
                padding: 5px 5px;
                text-decoration: none;
                border-radius: 8px;
				margin-right:5px;
				background-color:#C8BEAA;
			}
			.mc-left-family
			{
				   
                   //flex-grow:1;
				   overflow-y: auto;
				   //flex-basis:0px;
				   height:80px;
				   max-height:120px;
                  border-bottom: 2px dotted #ddd;
			}
			#pubtagging
			{
			color: #ff9800;
			font-weight:bold;
			}
			#exportbtn
			{
				text-decoration:none;
				color:white;
			}
			#insight-suboption div
			{
				display:inline-block;
				margin:5px;
				
			}
			#switchchart,#top-comp-opt,#priority-year-opt
			{
				border:none;
				background-color:#F6EEE1;
			}
			#layoutsettings
			{
				float:right;
			}
			.info-header
			{
				
				background-color:#1E150B;
				display:flex;
				flex-direction: row;
                flex-wrap:nowrap;
                justify-content: normal;
                align-items: normal;
                align-content: normal;
				height:auto;
                width:100%;
			}
			.info-header-child
		    {
				flex-grow:1;
				color:#fff;
				padding:5px;
				font-size:8px;
			}
			.new-header
			{
				display:flex;
				flex-direction: row;
                flex-wrap:nowrap;
                justify-content: center;
                align-items: center;
                align-content: normal;
				height:auto;
                width:100%;
			}
			.new-header-child
		    {
				flex-grow:1;
				padding:5px;
			}
			.project-title
			{
				font-size:32px;
				display:inline-block;
			}
			.top-option-bar-outer
			{
				padding:0px;
				background-color:#ef3f23;
				height:100%;
			}
			.top-option-bar
			{
				display:flex;
				flex-direction:row;
				width:100%;
				height:100%;
				background-color:#ef3f23;
				justify-content: center;
                align-items: center;
				
			}
			.top-option-bar-option
			{
				flex-grow:1;
				text-align: center;
				color:white;
				font-size:25px;
			}
			.logo-text
			{
				font-size:20px;
			}
			#middle-container a
			{
				text-decoration:none;
			    color: #777777;
			}
			#open-max-window
			{
				float:right;
				margin-right:10px;
				cursor:pointer;
			}
			#processing-div
			{
				padding:5px;
			}
			.full-screen 
{
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background: rgba(0,0,0,0.6);
  Z-index:1001;
}

.flex-container-center {
  display: flex;
  flex-direction: row;
  justify-content: right;
  height:100%;
  width:100%;
    align-items: flex-start;
}

.hidden-outer
{
	display:none;
}
.layout-modal
{
	display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    justify-content: normal;
    align-items: normal;
    align-content: normal;
	background-color:#F6EEE1;
	overflow-y:auto;
	height:80%;
	padding:20px;
	margin-top:auto;
	width:300px;
    border-radius:20px;
	
}
#layout-insight-header
{
  
  font-size : 20px;
  color : #EF3C18;
  color : rgb(239, 60, 24);
}
#closelayoutbtn
{
  font-size : 20px;
  color : #EF3C18;
  color : rgb(239, 60, 24);
  float:right;
  cursor:pointer;
}
.colors-pallets
{
	display:flex;
	flex-direction:column;
}
.colors-pallets div{
	display:flex;
	flex-direction:row;
	//padding:5px;
}
.colors-pallets div span{
	padding:2px;
}
.colors-pallets div span div:first-child{
	border-top-left-radius:5px;
	border-bottom-left-radius:5px;
}
.colors-pallets div span div:last-child{
	border-top-right-radius:5px;
	border-bottom-right-radius:5px;
}
.mc-iv-left .l2
{
	padding-left:10px;
	color:#777777;
}
.mc-iv-left .l2::before
{
	content:'\00bb';
	//content:'\2219';
}
.mc-iv-left .l3
{
	padding-left:15px;
	color:#777777;
}
.mc-iv-left .l3::before
{
	content:'\00bb\00bb';
}
.mc-iv-left .l4
{
	padding-left:20px;
	color:#777777;
}
#tester{
	overflow:auto;
}
div#customiziedlogo 
{
    color: #ef3f23;
    font-size: 32px;
    font-family: 'Bauziet';
}
div#customiziedlogo .logo-dash
{
	font-family: 'Telegraf-UltraLight', Telegraf;
    font-weight: 100;
}
.new-header-child a
{
	text-decoration:none;
}
.new-header-child a:hover
{
	text-decoration:none;
}
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
  <div class='header'><button title='Show result set' class='actionbtn' id='bcktolst' style='float:right;'>Result set</button><button title='show/hide comments' class='actionbtn' id='showcomments' style='float:right;'>Comments</button></div>
  <div id='middle-container-outer-wrapper'>
  <div id='middle-container-left-wrapper'>
  <div id='middle-container' class='middle-container'>
  <div class='mc-view-publication-inner'>
    <div class='mc-left'>
	    <div class='mc-left-pubno'>Publication No.<br/><span id='pubno'></span> <span id='relevancy'></span><div id='pubtagging'></div><div id='alllinks'></div></div>
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
		       <div class='sticky'>All Claims</div>
			   <div id='claims'></div>
		  
		  </div>
		  
		  
	</div>
	<?php if($reportmetainfo['categorization']==1)
	{?>
	<div class='mc-right'>
	    <div class='mc-right-cat'> 
		     <div class='sticky'>Categorisation <span style='float:right;cursor:pointer;margin-right:15px;' data-toggle="modal" data-target="#addCatForm" title='Add Category'><i class="fa fa-plus" aria-hidden="true"></i></span></div>
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
<div class='mc-pubview-navigation'>
    <div id='pagination_pubview_outer'>
	  
	      <div>
	      <ul class='pagination_pubview'>
		      <li><a onclick="previous_pub()">Previous</a></li><li><a onclick="next_pub()">Next</a></li>
		  </ul>
		  </div>
	  </div>
</div>
  </div>
  <div id='middle-container-consolidate-view' class='middle-container-consolidate-view visibility-true'>
     <div class='mc-cv-list-view'>
	
	 <table width='100%'>
	 <thead><th>Sr. No.</th><th class='headcol' style="">Publication Number</th><th style='width:300px;'>Title</th><th>App. Date</th><th>Priority Date</th><th>Publication Date</th><th>Applicant/Assignee</th><th>Archieve Date</th><th>Tagging</th></thead>
	 <tbody id='relevantpatents'>
	 

	 </tbody>
	 </table>
	 </div>
	 <div class='mc-cv-page-navigation'>
	 <div id='pagination'>
	  
	      <div><b>Total Records : </b><span id='totalRecord'></span> 
	      <ul class='pagination_new'>
		  </ul>
		  </div>
	  </div>
	 </div>
	 
  </div>
  <div id='middle-container-filter-view' class='middle-container-filter-view'>
  <div class='mc-cv-filters'>
   
  </div>
  </div>

    
  </div> 

  <div class='comment-right-side-view-wrapper' id='comment-right-side-view-wrapper'>
   <div class='comment-right-side-view'>
      <section>
		        <h5>Comments</h5>
				<div class='projectprogress'>
				
				 <div style='text-align:right;'><a class='button button1' data-toggle="modal" data-target="#addCommentForm" id='cmntbtn_intialconversation'>Add +</a></div>
                 <div id='commentbody'></div>							
			   </div>
		</section>
	</div>
  </div>    
  </div>
  <div class='bottom'><div class='bottom-content'>© iCuerious 2012-2022. All Rights Reserved<a style='float:right' id="google_translate_element"></a></div></div>
  
  </div>
  
<!---Comment form--------------> 
 <div id="addCommentForm" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Comment</h4>
      </div>
      <div class="modal-body">
        <form method="POST" id="commentUpdateForm">
		<input type='hidden' name='reverton' id='reverton' />
	    <input type='hidden' name='remail' id='remail' />
		<input type='hidden' name='private' id='rprivate' />
		     <div class='row'>
			      <div class='col-md-12'>
				       <div class='form-group'>
					       <textarea required name='comment' id='comment' rows='5' class='form-control' style='resize:none;'></textarea>
					   </div>
				  </div>
				  <div class='col-md-12'>
				        <button class='button button1' id='updatecmtbtn'>Save</button>
						
				  </div>
			 </div>
		</form>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>

  </div>
</div>      
<!---Change categorization form--------------> 
 <div id="addCatForm" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ADD Category</h4>
      </div>
      <div class="modal-body">
        <form method="POST" id="categoryForm">
		
		     <div class='row'>
			      <div class='col-md-3'>
				       <div class='form-group'>
					       <label>Level1</label>
						   <select id='formlevel1' name='level1'  class='form-control' required>
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
</div> 
<!---Add tagging form-------------->
 <div id="addTaggingForm" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
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
								              usptolink = "http://patft.uspto.gov/netacgi/nph-Parser?Sect2=PTO1&Sect2=HITOFF&p=1&u=/netahtml/PTO/search-bool.html&r=1&f=G&l=50&d=PALL&RefSrch=yes&Query=PN/"+serailno+"/";
											  espacenetlink = "https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+cc+""+serailno;	
											  icuemaplink = "https://www.icuerious.com/icuemapnew/?pubno="+data[i].pubno;
											  alllinksbtn ="<a class='lnkbtn' href='"+googlelink+"' target='_blank' title='Google Patents'><img src='images/googlepatent.svg' height='20px'/></a><a class='lnkbtn' href='"+usptolink+"' target='_blank' title='Uspto'><img src='images/uicon.svg' height='20px'/></a><a class='lnkbtn' href='"+espacenetlink+"' target='_blank' title='EspaceNet'><img src='images/espacenet.svg' height='20px'/></a><a class='lnkbtn' href='"+icuemaplink+"' target='_blank' title='Patent Family Tree'><img src='images/icuemap.svg' height='20px'/></a><a class='lnkbtn' title='Tagging' data-toggle='modal' data-target='#addTaggingForm' style='cursor:pointer;'><img src='images/tagging.svg' height='20px'/></a>";
										  
										  }
                                          else if(cc=='US' && serailno.length>8)
										  {
											  if(serailno.length == 10)
							                  {
								                serailno = serailno.substr(0,4) + "0" + serailno.substr(4);
								                
							                  }
											  googlelink ="https://patents.google.com/patent/"+cc+""+serailno;
								              usptolink ="http://appft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PG01&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.html&r=1&f=G&l=50&s1=%22";
			                                  var s1 = "%22.PGNR.&OS=DN/";
                                              var s2 = "&RS=DN/";			                                  
			                                  usptolink = usptolink+serailno+s1+serailno+s2+serailno;
											  espacenetlink = "https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+cc+""+serailno;
										      icuemaplink = "https://www.icuerious.com/icuemapnew/?pubno="+data[i].pubno;
											  alllinksbtn ="<a class='lnkbtn' href='"+googlelink+"' target='_blank' title='Google Patents'><img src='images/googlepatent.svg' height='20px'/></a><a class='lnkbtn' href='"+usptolink+"' target='_blank' title='Uspto'><img src='images/uicon.svg' height='20px'/></a><a class='lnkbtn' href='"+espacenetlink+"' target='_blank' title='EspaceNet'><img src='images/espacenet.svg' height='20px'/></a><a class='lnkbtn' href='"+icuemaplink+"' target='_blank' title='Patent Family Tree'><img src='images/icuemap.svg' height='20px'/></a><a class='lnkbtn' title='Tagging' data-toggle='modal' data-target='#addTaggingForm' style='cursor:pointer;'><img src='images/tagging.svg' height='20px'/></a>";
										  }
                                          else
										  {
											  googlelink ="https://patents.google.com/patent/"+data[i].pubno;
											  espacenetlink = "https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query="+cc+""+serailno;
										      icuemaplink = "https://www.icuerious.com/icuemapnew/?pubno="+data[i].pubno;
											  alllinksbtn ="<a class='lnkbtn' href='"+googlelink+"' target='_blank' title='Google Patents'><img src='images/googlepatent.svg' height='20px'/></a><a class='lnkbtn' href='"+espacenetlink+"' target='_blank' title='EspaceNet'><img src='images/espacenet.svg' height='20px'/></a><a class='lnkbtn' href='"+icuemaplink+"' target='_blank' title='Patent Family Tree'><img src='images/icuemap.svg' height='20px'/></a><a class='lnkbtn' title='Tagging' data-toggle='modal' data-target='#addTaggingForm' style='cursor:pointer;'><img src='images/tagging.svg' height='20px'/></a>";
										  
										  }	
										  
										  $('#pubno').text(data[i].pubno);
										  if(data[i].tagging!='' && data[i].tagging!=null)
										  {
										  $('#pubtagging').html('<i class="fa fa-tags"></i> '+data[i].tagging);
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
										   $('#claims').text(data[i].claims);
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
										   $(".pagination_pubview").append("<li><a data-pubno='"+data[i].pubno+"' class='pre_pub'>Previous</a></li><li><a data-pubno='"+data[i].pubno+"' class='after_pub'>Next</a></li>");
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
		 
                                
								  $("#processing-div2").remove();
								  var content ="";
								  var amount = 0;
								  var result = <?php echo json_encode($data);?>;
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
										  content = content+"<tr><td class='srno'>["+data[i].srno+"]</td><td class='headcol'><a class='viewpub' title='View Publication' data-listrowid='"+data[i].srno+"' data-pubno='"+data[i].pubno+"' data-reportid='"+data[i].rid+"'>"+data[i].pubno+""+relevancy+"</a>"+memberlegalstatus+"</td><td>"+data[i].pubtitle+"</td><td>"+data[i].appdate+"</td><td>"+data[i].pdate+"<td>"+data[i].pubdate+"</td><td>"+data[i].parentassignee+"</td><td>"+data[i].updateddate+"</td><td id='list-tagging-"+data[i].srno+"'>"+data[i].tagging+"</td>";
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
								       $("#totalRecord").html(result['totalrecords']);
									  }
									  else
									  {
										  $("#totalRecord").html('0');
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
	
	
	
	$('#showcomments').on('click',function(){
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
			$('#comment-right-side-view-wrapper').hide();
			$('#comment-right-side-view-wrapper').removeClass('visibility-true');
		}
		
		
		
	});
	
	$('body').on('click','.viewpub',function(){
		sectiontoshow_after_applyingfilter =3;
		
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
		//$('#showinsights').hide();
		$('#showcomments').show();
		$('#bcktolst').show();
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
					var reverton = $('#reverton').val();
					
					comment = encodeURIComponent(comment);
		           $("#updatecmtbtn").prop("disabled", true);

                     $.ajax({
                           type: "POST",
                           url: "getcomments.php",
                           data: "comment="+comment+"&reverton="+reverton+"&action=addcomment&reportid="+reportid+"&chartid="+chartid,
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
									  $("#addCommentForm").modal('hide');	 
									  //alert('Done');
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
                                            content = content+"<div><div>"+data[i].ctime+" "+data[i].creationdate+"</div><div class='"+data[i].flag+"'><div class='msg-outer'><div class='msg-createdby'>"+data[i].commentby+"</div>"+data[i].comment.replace(/\n/g, "<br />")+"</div></div></div>";
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
									  $("#addCatForm").modal('hide');	 
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
									  $("#addTaggingForm").modal('hide');	 
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
										  $('#pubtagging').html('<i class="fa fa-tags"></i> '+data[0].tagging);
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
											
                                            content = content+'<input type="radio" id="st-ctag-'+i+'" name="taggedvalue" value="'+customtags[i]+'"><label for="st-ctag-'+i+'">'+customtags[i]+'</label><br/>';
											}
											
											
									  $('#custom-tags').html(content);
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
	
  