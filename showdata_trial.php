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
	     
		 if(isset($_GET['corporatecriteria']) && trim($_GET['corporatecriteria'])=='activeauthority' && isset($_GET['criteriacc']) && trim($_GET['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_GET["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_GET['corporatecriteria']) && trim($_GET['corporatecriteria'])=='family' && isset($_GET['criteriacc']) && trim($_GET['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_GET["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
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
		  
		   if(isset($columnname[$i]) && trim($columnname[$i])!='' && trim($columnname[$i])=='activecountry')
		  {
			 $parameter = "activeauthority like :clickedactiveauthority";
			 array_push($myCondition,$parameter);
		     $bindparameter[':clickedactiveauthority'] = '%'.$columnvalues[$i].'%';
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
	
	/*------------Setting limit and offset----------*/		  
		 
		
         if (isset($_GET['pageno']) && $_GET['pageno']!="") 
	       {
            $page_no = $_GET['pageno'];
           } 
	       else 
	       {
            $page_no = 1;
           }
	       $total_records_per_page = 5000;
		   
		   if (isset($_GET['perpagerecord']) && $_GET['perpagerecord']!="") 
	       {
            $total_records_per_page = $_GET['perpagerecord'];
           } 
		   
	       $offset = ($page_no-1) * $total_records_per_page;
        /*----------------------------------------------*/	
		 
	if(count($myCondition) > 0)
		         {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT r.rid,r.pubno,r.pubtitle,r.appdate,r.pdate,r.pubdate,r.parentassignee,r.updateddate,r.relevancy,r.relevantpatent_legalstatus,r.tagging,r.family,r.familymembers_legalstatus from relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by r.pubno limit ".$offset.",".$total_records_per_page; 
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
						   $row['srno'] = $offset + $num + 1;
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
				   $row['srno'] = $offset + $num + 1;
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
  <div class='header'><button title='Show result set' class='actionbtn' id='bcktolst' style='float:right;'>Result set</button><button title='show/hide comments' class='actionbtn comment-area-not-expanded' id='showcomments' style='float:right;'>Comments</button></div>
  <div id='middle-container-outer-wrapper'>
  <div id='middle-container-left-wrapper'>
  <div class='filter-card-wd-100'>
  <div class='site-header'>Drill Down Publication List </div>
  </div>
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
	  
	      <div><b>Total Records : </b><span id='totalRecord'></span> 
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
								  	  
									  
								 
					       
	}
	
	
	function relevantpatentpagination(totalrecords,page_no)
		{
			
			
									 $('.pagination_new').empty();
									 $("#totalRecord").html(totalrecords);
									 
									 var recordsperpage = 5000;
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
		var currenturl      = window.location.href;
	      var  url= new URL(currenturl);
	      var params = new URLSearchParams(url.search);
	       params.set('pageno',currentpage);
		   var redirecturl = url.origin+url.pathname+'?'+params.toString();
		   window.location.href = redirecturl;
			//getrelevantpatents(reportid, currentpage);
		    
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
			    currentpage= '<?php if(isset($_GET["pageno"])){echo $_GET["pageno"]; }else{echo "1";}?>';
				$('#pagination').show();
				fd = $("form").serialize();
			    getrelevantpatents(reportid,currentpage);
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
	
  