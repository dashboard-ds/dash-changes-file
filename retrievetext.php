<?php
session_start();
include_once('function/function.php');
include_once('ipcclass.php');
include_once('cpcclass.php');
date_default_timezone_set('Asia/Kolkata');
if (isset( $_SESSION["clientemail"]))
{
	if(!isset($_POST["action"]))
	{
		echo "Acccess Denied";
		exit;
	}
include 'dbconnection.php';
$data= array();
$num =0;
if (!$conn) 
{
    echo 0;
}
else
{
	if($_POST['action'] =='retrievetext')
			  {
				   mysqli_select_db($conn,$dbname);
		               $sql ="SELECT r.rid,r.pid,r.pubno,r.pubtitle,r.relevancy,r.pdate,r.appdate,r.pubdate,r.parentassignee,r.assignee,r.inventor,r.abstract,r.claims,r.relevantpatent_legalstatus,r.legalstate,r.familymembers_legalstatus,r.family,r.tagging,c.cid,c.level1,c.level2,c.level3,c.level4,c.level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno where r.pubno=? && r.rid=?  order by r.pubno";
					   if($stmt = mysqli_prepare($conn, $sql))
			            {
				         mysqli_stmt_bind_param($stmt, "ss", $pubno,$reportid);
						 $pubno = $_POST['pubno'];
				         $reportid = $_POST['reportid'];
			            }
						if (mysqli_stmt_execute($stmt))
			             {
				           mysqli_stmt_bind_result($stmt,$rid,$pid,$pubno,$pubtitle,$relevancy,$pdate,$appdate,$pubdate,$parentassignee,$assignee,$inventor,$abstract,$claims,$relevantpatent_legalstatus,$legalstate,$familymembers_legalstatus,$family,$tagging,$cid,$level1,$level2,$level3,$level4,$level5);
				           
						   $row = array();
						   $cat = array();
						   $pre_pubno = '';
				           $num =0;
				           
						    while(mysqli_stmt_fetch($stmt)) 
				            {
								if($pubno!=$pre_pubno)
								{
					              $num =1;
                                  $row['rid'] = $rid;
					              $row['pid'] = $pid;
							      $row['pubno'] = $pubno;
							      $row['pubtitle'] = utf8_encode($pubtitle);
							      $row['relevancy'] = $relevancy;
							      $row['pdate'] = $pdate;
							      $row['pubdate'] = $pubdate;
							      $row['appdate'] = $appdate;
							      $row['parentassignee'] = utf8_encode($parentassignee);
							      $row['assignee'] = utf8_encode($assignee);
							      $row['inventor'] = utf8_encode($inventor);
								  $row['pubabstract'] = utf8_encode($abstract);
							      $row['claims'] = utf8_encode($claims);
								  $row['relevantpatent_legalstatus'] = $relevantpatent_legalstatus;
								  $row['legalstate'] = $legalstate;
								  $row['familylegalstatus'] = $familymembers_legalstatus;
								  $row['family'] = $family;
								  $row['tagging'] = $tagging;
								  $temp = array();
								  $temp['cid'] = $cid;
								  $temp['level1'] = utf8_encode($level1);
								  $temp['level2'] = utf8_encode($level2);
								  $temp['level3'] = utf8_encode($level3);
								  $temp['level4'] = utf8_encode($level4);
								  $temp['level5'] = utf8_encode($level5);
								  array_push($cat,$temp);
							      
								}
								else
								{
									/*-----previous publication equals to second row pubno------*/
								  $temp = array();
								  $temp['cid'] = $cid;
								  $temp['level1'] = $level1;
								  $temp['level2'] = $level2;
								  $temp['level3'] = $level3;
								  $temp['level4'] = $level4;
								  $temp['level5'] = $level5;
								  array_push($cat,$temp);
								}
							 
                            }
				
		                   if ( $num> 0) 
                           {
							 $row['categorization'] = $cat;
					         array_push($data,$row);
			                 echo json_encode($data);
			               }
		                   else
		                   {
			                 echo 0;
		                   }
			             }
			 
			           mysqli_close($conn);
			  }
			  elseif($_POST['action'] =='relevantpatent')
			  {
				  /*
				  checking authentication
				  if(checkrights($_SESSION["email"],106)!=1)
		            {
			          $row = array();
			          $row['accessright'] = 0;
			          array_push($data,$row);
                      echo json_encode($data); 
			          exit;
		          }*/
                  
		/*------------Setting limit and offset----------*/		  
		 
		
         if (isset($_POST['page_no']) && $_POST['page_no']!="") 
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
		   
	       $offset = ($page_no-1) * $total_records_per_page;
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
		 
	    if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 $data['records'] = '';
			 $data['metainfo'] = '';
			 $data['error'] = 'Report Id missing';
			 
		 }
		 
		 /*----------------Joining Condition--------------------*/
		    //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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
		 
		 if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		
		 if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT r.pid,r.rid,r.pubno,r.pubtitle,r.appdate,r.pdate,r.pubdate,r.parentassignee,r.updateddate,r.relevancy,r.relevantpatent_legalstatus,r.tagging from relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by r.pubno order by r.pid limit ".$offset.",".$total_records_per_page; 
		 //echo $sql;
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $row['srno'] = $offset + $num + 1;
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                  $data['records'] = $records;
			         
	           }
		       
		//$sql =    "SELECT * FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." group by r.pubno";
		$sql =    "SELECT count(distinct r.pubno) as count FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition;
		
		$sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute($bindparameter);		 
                
				 
		  $num = 0;
		   if($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   //$total_records = $sth->rowCount();
			   $total_records = $row['count'];
			   $data['totalrecords'] = $total_records;
		   }
		   
		   echo json_encode($data);
			  }
elseif($_POST['action'] =='patentminingdata')
			  {
				  /*
				  checking authentication
				  if(checkrights($_SESSION["email"],106)!=1)
		            {
			          $row = array();
			          $row['accessright'] = 0;
			          array_push($data,$row);
                      echo json_encode($data); 
			          exit;
		          }*/
                  
		/*------------Setting limit and offset----------*/		  
		 
		
         if (isset($_POST['page_no']) && $_POST['page_no']!="") 
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
		   
	       $offset = ($page_no-1) * $total_records_per_page;
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
		 
	    if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "pm.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 $data['records'] = '';
			 $data['metainfo'] = '';
			 $data['error'] = 'Report Id missing';
			 
		 }
		 
		 
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 
		
		if(isset($_POST['trscorecheck']) && trim($_POST['trscorecheck'])!='' && trim($_POST['trscorecheck'])=='trscore')
		 {
			 $operator = $_POST['trscore_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "trscore = :trscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':trscore'] = $_POST["trscore"];
                   break;
             case "greater":
                   $parameter = "trscore > :trscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':trscore'] = $_POST["trscore"];
                   break;
             case "less":
                   $parameter = "trscore < :trscore";
				   $bindparameter[':trscore'] = $_POST["trscore"];
				   array_push($myCondition,$parameter);
                   break;
			
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['etrscorecheck']) && trim($_POST['etrscorecheck'])!='' && trim($_POST['etrscorecheck'])=='etrscore')
		 {
			 $operator = $_POST['etrscore_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "etrscore = :etrscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':etrscore'] = $_POST["etrscore"];
                   break;
             case "greater":
                   $parameter = "etrscore > :etrscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':etrscore'] = $_POST["etrscore"];
                   break;
             case "less":
                   $parameter = "etrscore < :etrscore";
				   $bindparameter[':etrscore'] = $_POST["etrscore"];
				   array_push($myCondition,$parameter);
                   break;
			
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['itrscorecheck']) && trim($_POST['itrscorecheck'])!='' && trim($_POST['itrscorecheck'])=='itrscore')
		 {
			 $operator = $_POST['itrscore_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "itrscore = :itrscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':itrscore'] = $_POST["itrscore"];
                   break;
             case "greater":
                   $parameter = "itrscore > :itrscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':itrscore'] = $_POST["itrscore"];
                   break;
             case "less":
                   $parameter = "itrscore < :itrscore";
				   $bindparameter[':itrscore'] = $_POST["itrscore"];
				   array_push($myCondition,$parameter);
                   break;
			
             default:
                   break;
               }
			 
			 
		 }
		 
		  if(isset($_POST['mcscorecheck']) && trim($_POST['mcscorecheck'])!='' && trim($_POST['mcscorecheck'])=='mcscore')
		 {
			 $operator = $_POST['mcscore_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "mcscore = :mcscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':mcscore'] = $_POST["mcscore"];
                   break;
             case "greater":
                   $parameter = "mcscore > :mcscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':mcscore'] = $_POST["mcscore"];
                   break;
             case "less":
                   $parameter = "mcscore < :mcscore";
				   $bindparameter[':mcscore'] = $_POST["mcscore"];
				   array_push($myCondition,$parameter);
                   break;
			
             default:
                   break;
               }
			 
			 
		 }
		 
		  if(isset($_POST['ciscorecheck']) && trim($_POST['ciscorecheck'])!='' && trim($_POST['ciscorecheck'])=='ciscore')
		 {
			 $operator = $_POST['ciscore_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "ciscore = :ciscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':ciscore'] = $_POST["ciscore"];
                   break;
             case "greater":
                   $parameter = "ciscore > :ciscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':ciscore'] = $_POST["ciscore"];
                   break;
             case "less":
                   $parameter = "ciscore < :ciscore";
				   $bindparameter[':ciscore'] = $_POST["ciscore"];
				   array_push($myCondition,$parameter);
                   break;
			
             default:
                   break;
               }
			 
			 
		 }
		 
		if(isset($_POST['eciscorecheck']) && trim($_POST['eciscorecheck'])!='' && trim($_POST['eciscorecheck'])=='eciscore')
		 {
			 $operator = $_POST['eciscore_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "eciscore = :eciscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':eciscore'] = $_POST["eciscore"];
                   break;
             case "greater":
                   $parameter = "eciscore > :eciscore";
				   array_push($myCondition,$parameter);
				   $bindparameter[':eciscore'] = $_POST["eciscore"];
                   break;
             case "less":
                   $parameter = "eciscore < :eciscore";
				   $bindparameter[':eciscore'] = $_POST["eciscore"];
				   array_push($myCondition,$parameter);
                   break;
			
             default:
                   break;
               }
			 
			 
		 }
		 
		 $orderbycolname ='pm.srno';
		 $orderbytype ='asc';
		 if(isset($_POST['sortcolumncheck']) && trim($_POST['sortcolumncheck'])!='' && trim($_POST['sortcolumncheck'])=='sortcolumn')
		 {
			 $orderbycolname = 'pm.'.$_POST['sortcolumnvalue'];
		 }
		 
		 if(isset($_POST['orderbytype']) && trim($_POST['orderbytype'])!='')
		 {
			$orderbytype = $_POST['orderbytype'];
		 }
		 
		 
		 if(isset($_POST['citationtypecheck']))
		 {
			     $s=0;
				 $citationtype = Array();
		            foreach ($_POST['citationtype'] as $selectedOption)
		             {
			           $parameter ="citationtype = :citationtype".$s; 
			           array_push($citationtype,$parameter);
					   $bindparameter[':citationtype'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($citationtype)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $citationtype).")");
		            }
		           else
		           {
			        array_push($myCondition,$citationtype);
		           } 
		 }
		  
		 if(isset($_POST['citationassigneecheck']))
		 {
			     $s=0;
				 $citingowner = Array();
		            foreach ($_POST['citationassignee'] as $selectedOption)
		             {
			           $parameter ="citingowner = :citingowner".$s; 
			           array_push($citingowner,$parameter);
					   $bindparameter[':citingowner'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($citingowner)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $citingowner).")");
		            }
		           else
		           {
			        array_push($myCondition,$citingowner);
		           } 
		 }
		 
		 if(isset($_POST['divisioncheck']))
		 {
			     $s=0;
				 $divisiontype = Array();
		            foreach ($_POST['division'] as $selectedOption)
		             {
			           $parameter ="divisiontype = :divisiontype".$s; 
			           array_push($divisiontype,$parameter);
					   $bindparameter[':divisiontype'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($divisiontype)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $divisiontype).")");
		            }
		           else
		           {
			        array_push($myCondition,$divisiontype);
		           } 
		 }
		 
		 
		
		 if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT pm.ptno,pm.rid,pm.srno,pm.pubno,pm.parentassignee,pm.trscore,pm.etrscore,pm.itrscore,pm.mcscore,pm.ciscore,pm.eciscore,pm.citationtype,pm.citingpatent,pm.citingowner,pm.citingowner2,pm.citingpatent2,pm.divisiontype,pm.updateddate,pm.assignee from patentmining pm ".$myCondition." order by ".$orderbycolname." ".$orderbytype." limit ".$offset.",".$total_records_per_page; 
		 //echo $sql;
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   //$row['srno'] = $offset + $num + 1;
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                  $data['records'] = $records;
			         
	           }
		       
		//$sql =    "SELECT * FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." group by r.pubno";
		$sql =    "SELECT count(pm.pubno) as count FROM patentmining pm ".$myCondition;
		
		$sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute($bindparameter);		 
                
				 
		  $num = 0;
		   if($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   //$total_records = $sth->rowCount();
			   $total_records = $row['count'];
			   $data['totalrecords'] = $total_records;
		   }
		   
		   echo json_encode($data);
			  }			  
elseif($_POST['action']=='level2values')
{
	if(isset($_POST['level1']) && trim($_POST['level1'])!='')
	{
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
		 
		 if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
					  $level1 = Array();
				      $level1  = explode(',',$_POST['level1']);
					  
				     $temp = Array();
		            
					 for($i=0;$i<count($level1);$i++)
		             {
			           $parameter ="level1 = :level1".$i; 
			           array_push($temp,$parameter);
					   $bindparameter[':level1'.$i] = $level1[$i];
					   
		             }
		            
					if(count($temp)>0)
		            {
			          array_push($myCondition,"(".implode(" OR ", $temp).")");
		            }
		            else
		            {
			         array_push($myCondition,$temp);
		            }
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct level2 from categorization ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }				   
	}
}
elseif($_POST['action']=='level3values')
{
	if(isset($_POST['level2']) && trim($_POST['level2'])!='')
	{
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
		 
		 if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
					  $level2 = Array();
				      $level2  = explode(',',$_POST['level2']);
					  
				     $temp = Array();
		            
					 for($i=0;$i<count($level2);$i++)
		             {
			           $parameter ="level2 = :level2".$i; 
			           array_push($temp,$parameter);
					   $bindparameter[':level2'.$i] = $level2[$i];
					   
		             }
		            
					if(count($temp)>0)
		            {
			          array_push($myCondition,"(".implode(" OR ", $temp).")");
		            }
		            else
		            {
			         array_push($myCondition,$temp);
		            }
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct level3 from categorization ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }				   
	}
}
elseif($_POST['action']=='level4values')
{
	if(isset($_POST['level3']) && trim($_POST['level3'])!='')
	{
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
		 
		 if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		 
					  $level3 = Array();
				      $level3  = explode(',',$_POST['level3']);
					  
				     $temp = Array();
		            
					 for($i=0;$i<count($level3);$i++)
		             {
			           $parameter ="level3 = :level3".$i; 
			           array_push($temp,$parameter);
					   $bindparameter[':level3'.$i] = $level3[$i];
					   
		             }
		            
					if(count($temp)>0)
		            {
			          array_push($myCondition,"(".implode(" OR ", $temp).")");
		            }
		            else
		            {
			         array_push($myCondition,$temp);
		            }
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct level4 from categorization ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }				   
	}
}
elseif($_POST['action']=='getinsight')
{
	if($_POST['chart']=='1')
	{
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
		 if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,left(r.pubno,2) as country from relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." group by left(r.pubno,2)"; 
		 //echo $sql;
		 //var_dump($bindparameter); 
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='2')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.relevancy from relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by r.relevancy"; 

		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='3')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         
		 //additional parameter
             $parameter = "r.pdate != '0000-00-00'";
			 array_push($myCondition,$parameter); 			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.pdate) as filingyear FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY year(r.pdate)"; 
		
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='4')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by count desc"; 
		         
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='5')
	{
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
		 $parent = Array();
		 $child = Array();
		 $value = Array();
		 $node = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }			  
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT level1,level2,level3,level4 FROM categorization ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		  function getpatentcount($colname,$serchvalue)
			   {
				   //$servername = "sg2plcpnl0055.prod.sin2.secureserver.net";
$servername ="localhost";
//$username = "icueriousinvoice";
$username = "icueriou_dashrep";
$password = "GPHSiTn9Qw8E";
$dbname = "icueriou_dashrep";
//$dbname = "invoicedatabase";
$conn = mysqli_connect($servername, $username, $password);
				   mysqli_select_db($conn,$dbname);
		           $sql ="select count(*) as count from categorization where ".$colname." = ?";
				   //echo $sql;
		   if($stmt = mysqli_prepare($conn, $sql))
		   {
		   mysqli_stmt_bind_param($stmt, "s", $search);
		   $search = $serchvalue;
		   mysqli_stmt_execute($stmt);
		   }
		   mysqli_stmt_bind_result($stmt,$count);		   
		   if(mysqli_stmt_fetch($stmt))
		   {
			  return $count;
		   }
           else
		   {
			   return 0;
		   }
          
		   mysqli_close($conn);
			   }
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   if($row['level1']!= null && trim($row['level1'])!='')
			   {
				   
				   if (!in_array($row['level1'], $child))
				   {
				      array_push($parent,"");	   
				      array_push($child,$row['level1']);
				      array_push($value, getpatentcount("level1",$row['level1']));
					  //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level1'];
					  $rel['parent'] = 'Taxonomy';
					  array_push($node,$rel);
				   }
			   }
			   
			   if($row['level2']!= null && trim($row['level2'])!='')
			   {
				   
				   if (!in_array($row['level2'], $child))
				   {
				   array_push($parent,$row['level1']);
				   array_push($child,$row['level2']);
				   array_push($value, getpatentcount("level2",$row['level2']));
				    //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level2'];
					  $rel['parent'] = $row['level1'];
					  array_push($node,$rel);
				   }
			   }
			   
			   if($row['level3']!= null && trim($row['level3'])!='')
			   {
				   if (!in_array($row['level3'], $child))
				   {
				   array_push($parent,$row['level2']);
				   array_push($child,$row['level3']);
				   array_push($value, getpatentcount("level3",$row['level3']));
				   //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level3'];
					  $rel['parent'] = $row['level2'];
					  array_push($node,$rel);
				   }
			   }
			   
			   if($row['level4']!= null && trim($row['level4'])!='')
			   {
				   if (!in_array($row['level4'], $child))
				   {
				   array_push($parent,$row['level3']);
				   array_push($child,$row['level4']);
				   array_push($value, getpatentcount("level4",$row['level4']));
				   //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level4'];
					  $rel['parent'] = $row['level3'];
					  array_push($node,$rel);
				   }
			   }
			    
		   }
		   if ( $num> 0) 
               { 
		             /*----------------just for testing---------*/
					 $rel = Array();
					  $rel['name'] = 'Taxonomy';
					  $rel['parent'] = '';
					  array_push($node,$rel);
					 /*------------*/
		             $records["parent"] = $parent;
					 $records["child"] = $child;
					 $records["values"] = $value;
					 $records["nodes"] = $node;
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='6')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         		 
      /*------------------------*/				
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct family FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   if($row['family']!= null && trim($row['family'])!='')
			   {
				   $row['family'] =  preg_replace('/\[.*\]/', '', $row['family']);
				   
				   $familyarray = explode("\n", $row['family']);
				   $innerarray = Array();
				   for($s=0;$s<count($familyarray);$s++)
				   {
					   
					   array_push($innerarray,substr($familyarray[$s],0,2));
					   /*$splitwithinonepublication = preg_split("/[\s,]+/", $familyarray[$s]);
					   if(substr($splitwithinonepublication[1],0,1)=="B")
					   {
						   //patent number found
						   //consider next record as application so skip the next record.
						   continue;
					   }
					   else
					   {
						   array_push($records,substr($familyarray[$s],0,2));
					   }
					   /*for($m=0;$m<count($splitwithinonepublication);$m++)
					   {
						   array_push($records,$splitwithinonepublication[$m]); 
					   }
					   //array_push($records,substr($familyarray[$s],0,2)); */
				   }
				   
				    //$innerarray = array_unique($innerarray);
					
					foreach($innerarray as $val) 
					  {
                        array_push($records,$val); 
                      }
					
					
			   }
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             array_push($newfamilyarray,array_count_values($records));
	                 echo json_encode($newfamilyarray); 
					 //echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='7')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         
          /*------------------additional parameter--------------------*/
             $parameter = "r.epriorityno  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.epriorityno!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,left(r.epriorityno,2) as cc FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by left(r.epriorityno,2) order by count desc"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row);   
		  }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='8')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         /*------------------additional parameter--------------------*/
             $parameter = "r.epriorityno  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.epriorityno!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as z ,left(r.epriorityno,2) as cc, year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by left(r.epriorityno,2), year(r.pdate)"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);   
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='9')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
        
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 ='".$_POST['level']."'";
			 array_push($myCondition,$parameter);*/
             /*$parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);*/ 
             
           if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		   {
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 	
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		   {
			 $parameter = "level2 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		   {
			 $parameter = "level3 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		   {
			 $parameter = "level4 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql ="";
         if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.pdate) as year,level1,level2 FROM relevantpatents r left join  categorization c on r.pid=c.fk_pid ".$myCondition."   group by level1,level2,year(r.pdate)"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.pdate) as year,level2,level3 FROM relevantpatents r left join  categorization c on r.pid=c.fk_pid ".$myCondition."   group by level2,level3,year(r.pdate)"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.pdate) as year,level3,level4 FROM relevantpatents r left join  categorization c on r.pid=c.fk_pid ".$myCondition."   group by level3,level4,year(r.pdate)"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.pdate) as year,level4,level5 FROM relevantpatents r left join  categorization c on r.pid=c.fk_pid ".$myCondition."   group by level4,level5,year(r.pdate)"; 
		 }	
		 //$sql =  "SELECT count(distinct r.pubno) as count,year(r.pdate) as year,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno ".$myCondition."   group by level1,level2,level3,level4,level5,year(r.pdate)"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   /*if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray);
			   }*/
			     
				
               if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		       {
				   $infoarray['topcategory'] = $row['level1'];
				   if($row['level2']!='' && $row['level2']!=null)
				   {
				     $infoarray['indepthcategory'] = $row['level2'];
				   }
				   else
				   {
					   $infoarray['indepthcategory'] = $row['level1'];
				   }
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		       {
				   $infoarray['topcategory'] = $row['level2'];
				   if($row['level3']!='' && $row['level3']!=null)
				   {
				      $infoarray['indepthcategory'] = $row['level3'];
				   }
				   else
				   {
					   $infoarray['indepthcategory'] = $row['level2'];
				   }
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		       {
				   $infoarray['topcategory'] = $row['level3'];
				   if($row['level4']!='' && $row['level4']!=null)
				   {
				     $infoarray['indepthcategory'] = $row['level4'];
				   }
				   else
				   {
					   $infoarray['indepthcategory'] = $row['level3'];
				   }
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		       {
				   $infoarray['topcategory'] = $row['level4'];
				   if($row['level5']!='' && $row['level5']!=null)
				   {
				     $infoarray['indepthcategory'] = $row['level5'];
				   }
				   else
				   {
					    $infoarray['indepthcategory'] = $row['level4'];
				   }
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }				
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='10')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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
		 
		 if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
        
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); */	
           if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		   {
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 	
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		   {
			 $parameter = "level2 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		   {
			 $parameter = "level3 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		   {
			 $parameter = "level4 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	     
		 $sql ='';
         if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,level1,level2 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level1,level2 order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,level2,level3 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level2,level3 order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,level3,level4 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level3,level4 order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,level4,level5 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level4,level5 order by count desc"; 
		 }		 
		 //$sql =  "SELECT count(distinct r.pubno) as count,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno ".$myCondition."  group by level1,level2,level3,level4,level5 order by count desc"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   /*if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray);
			   }*/
			     
			  if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		       {
				   $infoarray['topcategory'] = $row['level1'];
				   if($row['level2']!='' && $row['level2']!=null)
				   {
				     $infoarray['indepthcategory'] = $row['level2'];
				   }
				   else
				   {
					   $infoarray['indepthcategory'] = $row['level1'];
				   }
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		       {
				   $infoarray['topcategory'] = $row['level2'];
				    if($row['level3']!='' && $row['level3']!=null)
				    {
				     $infoarray['indepthcategory'] = $row['level3'];
				    }
					else
					{
					$infoarray['indepthcategory'] = $row['level2'];
					}
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		       {
				   $infoarray['topcategory'] = $row['level3'];
				   if($row['level4']!='' && $row['level4']!=null)
				    {
				    $infoarray['indepthcategory'] = $row['level4'];
					}
					else
					{
					 $infoarray['indepthcategory'] = $row['level3'];	
					}
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		       {
				   $infoarray['topcategory'] = $row['level4'];
				   if($row['level5']!='' && $row['level5']!=null)
				    {
				     $infoarray['indepthcategory'] = $row['level5'];
					}
					else
					{
					  $infoarray['indepthcategory'] = $row['level4'];
					}
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }	
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='11')
	{
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
		 $infoarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         
		 /*------------------add parameter for typeofassignee--------------------*/
             $parameter = "typeofassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "typeofassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/		
         		 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count, r.typeofassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.typeofassignee order by count desc"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='12')
	{
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
		 $infoarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' && typeofassignee='".$_POST['typeofassignee']."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
        
             /*------------------add parameter for level--------------------*/
             $parameter = "r.typeofassignee ='".$_POST['typeofassignee']."'";
			 array_push($myCondition,$parameter); 			  
      /*------------------------*/		
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.parentassignee order by count desc"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='13')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 ='".$_POST['level']."'";
			 array_push($myCondition,$parameter); */
             /*$parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);*/ 			 
           if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		   {
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 	
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		   {
			 $parameter = "level2 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		   {
			 $parameter = "level3 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		   {
			 $parameter = "level4 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	     $sql ='';
         
		 if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		 {			 
		 $sql =   "SELECT count(distinct r.pubno) as count,parentassignee,level1,level2 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level1,level2,parentassignee order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		 {			 
		 $sql =   "SELECT count(distinct r.pubno) as count,parentassignee,level2,level3 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level2,level3,parentassignee order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		 {			 
		 $sql =   "SELECT count(distinct r.pubno) as count,parentassignee,level3,level4 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level3,level4,parentassignee order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,parentassignee,level4,level5 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level4,level5,parentassignee order by count desc"; 
		 }		
		 
		 //$sql =  "SELECT count(distinct r.pubno) as count,parentassignee,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno ".$myCondition."   group by level1,level2,level3,level4,level5,parentassignee order by count desc"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   /*if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray);
			   }*/
			     
			   if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		       {
				   $infoarray['topcategory'] = $row['level1'];
				   if($row['level2']!='' && $row['level2']!=null)
				   {
				   $infoarray['indepthcategory'] = $row['level2'];
				   }
				   else
				   {
					$infoarray['indepthcategory'] = $row['level1'];   
				   }
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		       {
				   $infoarray['topcategory'] = $row['level2'];
				   if($row['level3']!='' && $row['level3']!=null)
				   {
				   $infoarray['indepthcategory'] = $row['level3'];
				   }
				   else
				   {
					  $infoarray['indepthcategory'] = $row['level2']; 
				   }
				    $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		       {
				   $infoarray['topcategory'] = $row['level3'];
				   if($row['level4']!='' && $row['level4']!=null)
				   {
				     $infoarray['indepthcategory'] = $row['level4'];
				   }
				   else
				   {
					  $infoarray['indepthcategory'] = $row['level3']; 
				   }
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		       {
				   $infoarray['topcategory'] = $row['level4'];
				   if($row['level5']!='' && $row['level5']!=null)
				   {
				    $infoarray['indepthcategory'] = $row['level5'];
				   }
				   else
				   {
					  $infoarray['indepthcategory'] = $row['level4']; 
				   }
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
			   }	
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='14')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

       /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as z , r.parentassignee, year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.parentassignee, year(r.pdate) order by pdate asc"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);   
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='15')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
        
		if(isset($_POST['activeauthopt']))
		 {
			    if($_POST['activeauthopt']==2)
				{
					 $parameter = "r.activeauthority !=''";
			         array_push($myCondition,$parameter);
				}
				
		 }	
		 
		 
		/*select default based on scoringopt='1'*/
		   $selectscoreparameter ="r.externalcscore";
		   if(isset($_POST['scoringopt']) && trim($_POST['scoringopt'])!='')
		   {
			 $scoringopt = $_POST['scoringopt'];
			 switch ($scoringopt) 
			 {
             case "1":
                   $selectscoreparameter ="r.externalcscore";
                   break;
             case "2":
                    $selectscoreparameter ="r.internalcscore";
                   break;
             case "3":
                    $selectscoreparameter ="r.techscore";
                   break;
			 case "4":
                    $selectscoreparameter ="r.marketscore";
                   break;
			 case "5":
                    $selectscoreparameter ="r.impactscore";
                   break;	
             case "6":
                    $selectscoreparameter ="r.eciscore";
                   break;	
             case "7":
                    $selectscoreparameter ="r.patentassetindex";
                   break;				   				   
             default:
                   break;
               }
			 
			 
		 }
		/*----------------------*/
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 ='".$_POST['level']."'";
			 array_push($myCondition,$parameter); 	*/
             /*$parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); */
           if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		   {
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 	
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		   {
			 $parameter = "level2 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		   {
			 $parameter = "level3 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		   {
			 $parameter = "level4 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }			  			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	     
		 $sql ='';
         if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,year(r.pdate) as year,level1,level2 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level1,level2,year(r.pdate)"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		 {			 
		 $sql = "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,year(r.pdate) as year,level2,level3 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level2,level3,year(r.pdate)";
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,year(r.pdate) as year,level3,level4 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level3,level4,year(r.pdate)";
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,year(r.pdate) as year,level4,level5 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level4,level5,year(r.pdate)";
		 }		
		 //$sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,year(r.pdate) as year,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno ".$myCondition."   group by level1,level2,level3,level4,level5,year(r.pdate)"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   /*if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				    $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				    $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				    $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray);
			   }*/
			     
				
               if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		       {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		       {
				   $infoarray['topcategory'] = $row['level2'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		       {
				   $infoarray['topcategory'] = $row['level3'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		       {
				   $infoarray['topcategory'] = $row['level4'];
				   $infoarray['indepthcategory'] = $row['level5'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }				
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='16')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 $top10assignee = array();
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['activeauthopt']))
		 {
			    if($_POST['activeauthopt']==2)
				{
					 $parameter = "r.activeauthority !=''";
			         array_push($myCondition,$parameter);
				}
				
		 }	
		 
        /*select default based on scoringopt='1'*/
		   $selectscoreparameter ="r.externalcscore";
		   if(isset($_POST['scoringopt']) && trim($_POST['scoringopt'])!='')
		   {
			 $scoringopt = $_POST['scoringopt'];
			 switch ($scoringopt) 
			 {
             case "1":
                   $selectscoreparameter ="r.externalcscore";
                   break;
             case "2":
                    $selectscoreparameter ="r.internalcscore";
                   break;
             case "3":
                    $selectscoreparameter ="r.techscore";
                   break;
			 case "4":
                    $selectscoreparameter ="r.marketscore";
                   break;
			 case "5":
                    $selectscoreparameter ="r.impactscore";
                   break;	
             case "6":
                    $selectscoreparameter ="r.eciscore";
                   break;	
             case "7":
                    $selectscoreparameter ="r.patentassetindex";
                   break;				   				   
             default:
                   break;
               }
			 
			 
		 }
		/*----------------------*/
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 ='".$_POST['level']."'";
			 array_push($myCondition,$parameter); 	*/
             /*$parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);*/ 
             
           if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		   {
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 	
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		   {
			 $parameter = "level2 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		   {
			 $parameter = "level3 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		   {
			 $parameter = "level4 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }			 

             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	     
          $sql ='';
         
		 if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		 {			 
		 $sql = "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,parentassignee,level1,level2 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level1,level2,parentassignee order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,parentassignee,level2,level3 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid".$myCondition."   group by level2,level3,parentassignee order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,parentassignee,level3,level4 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level3,level4,parentassignee order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,parentassignee,level4,level5 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level4,level5,parentassignee order by count desc"; 
		 }		 
		 
		 //$sql =  "SELECT count(distinct r.pubno) as count,sum(".$selectscoreparameter.") as score,parentassignee,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno".$myCondition."   group by level1,level2,level3,level4,level5,parentassignee order by count desc"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   /*if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray);
			   }*/
			   
			   if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		       {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		       {
				   $infoarray['topcategory'] = $row['level2'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		       {
				   $infoarray['topcategory'] = $row['level3'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		       {
				   $infoarray['topcategory'] = $row['level4'];
				   $infoarray['indepthcategory'] = $row['level5'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
			   }
			     
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='17')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' && typeofassignee='".$_POST['typeofassignee']."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['activeauthopt']))
		 {
			    if($_POST['activeauthopt']==2)
				{
					 $parameter = "r.activeauthority !=''";
			         array_push($myCondition,$parameter);
				}
				
		 }	
		 
        /*select default based on scoringopt='1'*/
		   $selectscoreparameter ="r.externalcscore";
		   if(isset($_POST['scoringopt']) && trim($_POST['scoringopt'])!='')
		   {
			 $scoringopt = $_POST['scoringopt'];
			 switch ($scoringopt) 
			 {
             case "1":
                   $selectscoreparameter ="r.externalcscore";
                   break;
             case "2":
                    $selectscoreparameter ="r.internalcscore";
                   break;
             case "3":
                    $selectscoreparameter ="r.techscore";
                   break;
			 case "4":
                    $selectscoreparameter ="r.marketscore";
                   break;
			 case "5":
                    $selectscoreparameter ="r.impactscore";
                   break;	
             case "6":
                    $selectscoreparameter ="r.eciscore";
                   break;	
             case "7":
                    $selectscoreparameter ="r.patentassetindex";
                   break;				   				   
             default:
                   break;
               }
			 
			 
		 }
		/*----------------------*/
        /*------------------add parameter for level--------------------*/
             $parameter = "typeofassignee ='".$_POST['typeofassignee']."'";
			 array_push($myCondition,$parameter); 		
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,avg(".$selectscoreparameter.") as score,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."   group by r.parentassignee order by count desc"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   
				   
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
				  
			   
			   
			     
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='18')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

         /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/		 
		 
		 if(isset($_POST['activeauthopt']))
		 {
			    if($_POST['activeauthopt']==2)
				{
					 $parameter = "r.activeauthority !=''";
			         array_push($myCondition,$parameter);
				}
				
		 }	
		 
        /*select default based on scoringopt='1'*/
		   $selectscoreparameter ="r.externalcscore";
		   if(isset($_POST['scoringopt']) && trim($_POST['scoringopt'])!='')
		   {
			 $scoringopt = $_POST['scoringopt'];
			 switch ($scoringopt) 
			 {
             case "1":
                   $selectscoreparameter ="r.externalcscore";
                   break;
             case "2":
                    $selectscoreparameter ="r.internalcscore";
                   break;
             case "3":
                    $selectscoreparameter ="r.techscore";
                   break;
			 case "4":
                    $selectscoreparameter ="r.marketscore";
                   break;
			 case "5":
                    $selectscoreparameter ="r.impactscore";
                   break;	
             case "6":
                    $selectscoreparameter ="r.eciscore";
                   break;	
             case "7":
                    $selectscoreparameter ="r.patentassetindex";
                   break;				   				   
             default:
                   break;
               }
			 
			 
		 }
		/*----------------------*/
        
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,avg(".$selectscoreparameter.") as score,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by r.parentassignee order by count desc"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   
				   
				   $infoarray['parentassignee'] = $row['parentassignee'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['score'] = $row['score'];
				   array_push($records,$infoarray); 
				  
			   
			   
			     
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='19')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top comp filters
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		   if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

          /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/			 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,left(r.epriorityno,2) as cc, r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by left(r.epriorityno,2),r.parentassignee order by count desc"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row);   
		  }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='20')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per filter top com filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

         /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/			 
      /*------------------------*/				
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct family,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $currentparent = $row['parentassignee'];
			   if($row['family']!= null && trim($row['family'])!='')
			   {
				   $row['family'] =  preg_replace('/\[.*\]/', '', $row['family']);
				   
				   $familyarray = explode("\n", $row['family']);
				   $innerarray = Array();
				   for($s=0;$s<count($familyarray);$s++)
				   {
					   
					   array_push($innerarray,substr($familyarray[$s],0,2));
					   /*$splitwithinonepublication = preg_split("/[\s,]+/", $familyarray[$s]);
					   if(substr($splitwithinonepublication[1],0,1)=="B")
					   {
						   //patent number found
						   //consider next record as application so skip the next record.
						   continue;
					   }
					   else
					   {
						   array_push($records,substr($familyarray[$s],0,2));
					   }
					   /*for($m=0;$m<count($splitwithinonepublication);$m++)
					   {
						   array_push($records,$splitwithinonepublication[$m]); 
					   }
					   //array_push($records,substr($familyarray[$s],0,2)); */
				   }
				   
				    //$innerarray = array_unique($innerarray);
					
					foreach($innerarray as $val) 
					  {
						$refarray = Array();  
                        $refarray['cc'] = $val; 
						$refarray['value'] = 1; 
					    $refarray['assignee'] = $currentparent;
						array_push($records,$refarray);
                      }
					
					
			   }
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		          $result = array();
		             //array_push($newfamilyarray,array_count_values($records));
	                 //echo json_encode($newfamilyarray); 
					 foreach($records as $subarray){
                      $composite_key = $subarray['assignee'] . '_' . $subarray['cc'];
                        if(!isset($result[$composite_key])){
                            $result[$composite_key] = $subarray;  // first occurrence
                           }else{
                              $result[$composite_key]['value'] += $subarray['value'];  // not first occurrence
                               }
                              }
$result=array_values($result);  // change from assoc to indexed
//unset($result[0]);  // remove first element to start numeric keys at 1

					 echo json_encode($result); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='21')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

          if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
       /*------------------additional parameter--------------------*/
             $parameter = "r.legalstate  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.legalstate!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate from relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.legalstate"; 

		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='22')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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
		 
		 if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

       /*------------------add parameter for typeofassignee--------------------*/
             $parameter = "r.typeofassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.typeofassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate as legalstate, r.typeofassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.legalstate,r.typeofassignee order by count desc"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row);   
		  }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='23')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per selected top com. filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
       /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 

             $parameter = "r.legalstate  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.legalstate!=''";
			 array_push($myCondition,$parameter); 				 
       /*------------------------*/		 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate as legalstate, r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.legalstate,r.parentassignee order by count desc"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row);   
		  }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='24')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
      /*------------------------*/				
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct family,r.familymembers_legalstatus FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $currentrowlegalstate = $row['familymembers_legalstatus'];
			   if($row['family']!= null && trim($row['family'])!='')
			   {
				   $row['family'] =  preg_replace('/\[.*\]/', '', $row['family']);
				   
				   $familyarray = explode("\n", $row['family']);
				   $familylegalstatus = explode("\n", $row['familymembers_legalstatus']);
				   $innerarray = Array();
				   for($s=0;$s<count($familyarray);$s++)
				   {
					   
					   array_push($innerarray,substr($familyarray[$s],0,2));
					   /*$splitwithinonepublication = preg_split("/[\s,]+/", $familyarray[$s]);
					   if(substr($splitwithinonepublication[1],0,1)=="B")
					   {
						   //patent number found
						   //consider next record as application so skip the next record.
						   continue;
					   }
					   else
					   {
						   array_push($records,substr($familyarray[$s],0,2));
					   }
					   /*for($m=0;$m<count($splitwithinonepublication);$m++)
					   {
						   array_push($records,$splitwithinonepublication[$m]); 
					   }
					   //array_push($records,substr($familyarray[$s],0,2)); */
				   }
				   
				    //$innerarray = array_unique($innerarray);
					
					foreach($innerarray as $key => $val) 
					  {
						$refarray = Array();  
                        $refarray['cc'] = $val; 
						$refarray['value'] = 1; 
					    $refarray['legalstate'] = $familylegalstatus[$key];
						array_push($records,$refarray);
                      }
					
					
			   }
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		          $result = array();
		             //array_push($newfamilyarray,array_count_values($records));
	                 //echo json_encode($newfamilyarray); 
					 foreach($records as $subarray){
                      $composite_key = $subarray['legalstate'] . '_' . $subarray['cc'];
                        if(!isset($result[$composite_key])){
                            $result[$composite_key] = $subarray;  // first occurrence
                           }else{
                              $result[$composite_key]['value'] += $subarray['value'];  // not first occurrence
                               }
                              }
$result=array_values($result);  // change from assoc to indexed
//unset($result[0]);  // remove first element to start numeric keys at 1

					 echo json_encode($result); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
	}
	elseif($_POST['chart']=='25')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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


             /*-----other----*/
             $parameter = "r.legalstate  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.legalstate!=''";
			 array_push($myCondition,$parameter); 			 
        
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 ='".$_POST['level']."'";
			 array_push($myCondition,$parameter); */
             /*$parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);*/
           if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		   {
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 	
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		   {
			 $parameter = "level2 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		   {
			 $parameter = "level3 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   }
		   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		   {
			 $parameter = "level4 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter);
		   } 			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql ='';
         if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate,level1,level2 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level1,level2,r.legalstate order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate,level2,level3 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level2,level3,r.legalstate order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate,level3,level4 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level3,level4,r.legalstate order by count desc"; 
		 }
		 elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		 {			 
		 $sql =  "SELECT count(distinct r.pubno) as count,r.legalstate,level4,level5 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."  group by level4,level5,r.legalstate order by count desc"; 
		 }		  
		 
		 //$sql =  "SELECT count(distinct r.pubno) as count,r.legalstate,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno ".$myCondition."  group by level1,level2,level3,level4,level5,r.legalstate order by count desc"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   /*if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray);
			   }*/
			     
			  if(isset($_POST['hlevel']) && $_POST['hlevel']=='l1')
		       {
				   $infoarray['topcategory'] = $row['level1'];
				   if($row['level2']!='' && $row['level2']!=null)
				   {
				    $infoarray['indepthcategory'] = $row['level2'];
				   }
				   else
				   {
					 $infoarray['indepthcategory'] = $row['level1']; 
				   }
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l2')
		       {
				   $infoarray['topcategory'] = $row['level2'];
				   if($row['level3']!='' && $row['level3']!=null)
				   {
				     $infoarray['indepthcategory'] = $row['level3'];
				   }
				   else
				   {
					   $infoarray['indepthcategory'] = $row['level2'];
				   }
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l3')
		       {
				   $infoarray['topcategory'] = $row['level3'];
				   if($row['level4']!='' && $row['level4']!=null)
				   {
				      $infoarray['indepthcategory'] = $row['level4'];
				   }
				   else
				   {
					  $infoarray['indepthcategory'] = $row['level3']; 
				   }
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
			   }
			   elseif(isset($_POST['hlevel']) && $_POST['hlevel']=='l4')
		       {
				   $infoarray['topcategory'] = $row['level4'];
				   
				   if($row['level5']!='' && $row['level5']!=null)
				   {
				   $infoarray['indepthcategory'] = $row['level5'];
				   }
				   else
				   {
				   $infoarray['indepthcategory'] = $row['level4'];
				   }
				   $infoarray['count'] = $row['count'];
				   $infoarray['legalstate'] = $row['legalstate'];
				   array_push($records,$infoarray); 
			   }	
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='26')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct r.pubno ,r.familymembers_legalstatus from relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition; 

		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   
			   if($row['familymembers_legalstatus']!= null && trim($row['familymembers_legalstatus'])!='')
			   {
				   
				   $familylegalstatus = explode("\n", $row['familymembers_legalstatus']);
				   foreach($familylegalstatus as $key => $val) 
					  {
						$refarray = Array();   
						$refarray['count'] = 1; 
					    $refarray['legalstate'] = $val;
						array_push($records,$refarray);
                      }
			   
		       }
		   }
		   if ( $num> 0) 
               { 
		             $result = array();
		             
					 foreach($records as $subarray)
					 {
                      $composite_key = $subarray['legalstate'];
                        if(!isset($result[$composite_key]))
						   {
                            $result[$composite_key] = $subarray;  // first occurrence
                           }
						   else
						   {
                              $result[$composite_key]['count'] += $subarray['count'];  // not first occurrence
                           }
                      }
                           
				     $result=array_values($result);  // change from assoc to indexed
                      

					 echo json_encode($result); 
	                  
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='27')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top comp. filter selected
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

           /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/		 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct r.pubno,r.familymembers_legalstatus, r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $currentrowassignee = $row['parentassignee'];
			   if($row['familymembers_legalstatus']!= null && trim($row['familymembers_legalstatus'])!='')
			   {
				   
				   $familylegalstatus = explode("\n", $row['familymembers_legalstatus']);
				   
				  
				   
				    
					
					foreach($familylegalstatus as $key => $val) 
					  {
						$refarray = Array();  
                        $refarray['parentassignee'] = $currentrowassignee; 
						$refarray['count'] = 1; 
					    $refarray['legalstate'] = $val;
						array_push($records,$refarray);
                      }
			   }
				
			     
		   }
		   if ( $num> 0) 
               { 
		       
		             $result = array();
		             
					 foreach($records as $subarray)
					 {
                      $composite_key = $subarray['legalstate'] . '_' . $subarray['parentassignee'];
                        if(!isset($result[$composite_key]))
						   {
                            $result[$composite_key] = $subarray;  // first occurrence
                           }
						   else
						   {
                              $result[$composite_key]['count'] += $subarray['count'];  // not first occurrence
                           }
                      }
                       
					   $result=array_values($result);  // change from assoc to indexed
                       //unset($result[0]);  // remove first element to start numeric keys at 1

					 echo json_encode($result); 
					 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='28')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
      /*------------------------*/				
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct family,r.familymembers_legalstatus FROM relevantpatents r left join  categorization c on r.pid=c.fk_pid ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $currentrowlegalstate = $row['familymembers_legalstatus'];
			   if($row['family']!= null && trim($row['family'])!='')
			   {
				   $row['family'] =  preg_replace('/\[.*\]/', '', $row['family']);
				   
				   $familyarray = explode("\n", $row['family']);
				   $familylegalstatus = explode("\n", $row['familymembers_legalstatus']);
				   $innerarray = Array();
				   for($s=0;$s<count($familyarray);$s++)
				   {
					   
					   array_push($innerarray,substr($familyarray[$s],0,2));
					   
				   }
				   
				    //$innerarray = array_unique($innerarray);
					
					foreach($innerarray as $key => $val) 
					  {
						$refarray = Array();  
                        $refarray['cc'] = $val; 
						$refarray['value'] = 1; 
					    $refarray['legalstate'] = $familylegalstatus[$key];
						array_push($records,$refarray);
                      }
					
					
			   }
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		          $result = array();
		             
					 foreach($records as $subarray)
					 {
                      $composite_key = $subarray['legalstate'] . '_' . $subarray['cc'];
                        if(!isset($result[$composite_key]))
						   {
                            $result[$composite_key] = $subarray;  // first occurrence
                           }
						   else
						   {
                              $result[$composite_key]['value'] += $subarray['value'];  // not first occurrence
                            }
                       }
                       
					   $result=array_values($result);  // change from assoc to indexed
                        //unset($result[0]);  // remove first element to start numeric keys at 1

					 echo json_encode($result); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
	}
	elseif($_POST['chart']=='29')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.expirydate) as expiryyear FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY year(r.expirydate)"; 
		
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='30')
	{
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
		 $infoarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
        
        /*------------------add parameter for level--------------------*/
             /*$parameter = "level1 ='".$_POST['level']."'";
			 array_push($myCondition,$parameter);*/
             $parameter = "level1 =:toplevelfilter";
			 $bindparameter[':toplevelfilter'] = urldecode($_POST['level']);
			 array_push($myCondition,$parameter); 			 			 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,year(r.expirydate) as year,level1,level2,level3,level4,level5 FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition."   group by level1,level2,level3,level4,level5,year(r.expirydate)"; 
		  //SELECT count(distinct r.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.rid=c.rid && r.pubno=c.pubno  ".$myCondition." GROUP BY r.parentassignee order by count desc limit 15       
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   
			   if(($row['level2']== null) or (trim($row['level2'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level1'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level3']== null) or (trim($row['level3'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level2'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray); 
				  
			   }
			   elseif(($row['level4']== null) or (trim($row['level4'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level3'];
				   $infoarray['count'] = $row['count'];
				   $infoarray['year'] = $row['year'];
				   array_push($records,$infoarray); 
			   }
			   elseif(($row['level5']== null) or (trim($row['level5'])==''))
			   {
				   $infoarray['topcategory'] = $row['level1'];
				   $infoarray['indepthcategory'] = $row['level4'];
				   $infoarray['year'] = $row['year'];
				   $infoarray['count'] = $row['count'];
				   array_push($records,$infoarray);
			   }
			     
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='31')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

        /*------------------add parameter for removing blank from ipc--------------------*/
             $parameter = "ipc  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "ipc!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.ipc FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.ipc order by count desc limit 20"; 
		         
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $datasent = Array();
			   $datasent['count'] = $row['count'];
			   $datasent['ipc'] = $row['ipc'];
			   $datasent['ipc_definition'] = standard_ipcclass($row['ipc']);
			   array_push($records,$datasent); 
			   //array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records) ; 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='32')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top comp filters
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		   if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 //restricting top 20 classes
		 
			 
			 $top20ipc = array();
			 $sql =  "SELECT count(*) as count,r.ipc FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.ipc order by count desc,ipc limit 20"; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top20ipc,$row['ipc']);   
		      }
		   if ( $num> 0) 
               { 
		             $s=0;
				     $ipc = Array();
		            foreach ($top20ipc as $selectedOption)
		             {
			           $parameter ="ipc = :ipc".$s; 
			           array_push($ipc,$parameter);
					   $bindparameter[':ipc'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($ipc)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $ipc).")");
		            }
		           else
		           {
			        array_push($myCondition,$ipc);
		           } 
		             
					 
			         
	           }
               else
			   {
				   
			   }

          /*------------------add parameter for removing blank from parentassignee--------------------*/
              $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 	
			 
			 $parameter = "ipc  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "ipc!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/			 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count, ipc, r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by ipc,r.parentassignee order by count desc"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $datasent = Array();
			   $datasent['count'] = $row['count'];
			   $datasent['ipc'] = $row['ipc'];
			   $datasent['parentassignee'] = $row['parentassignee'];
			   $datasent['ipc_definition'] = standard_ipcclass($row['ipc']);
			   array_push($records,$datasent);
			   //array_push($records,$row);   
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='33')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

        /*------------------add parameter for removing blank from ipc--------------------*/
             $parameter = "cpc  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "cpc!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.cpc FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.cpc order by count desc limit 20"; 
		         
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $datasent = Array();
			   $datasent['count'] = $row['count'];
			   $datasent['cpc'] = $row['cpc'];
			   $datasent['cpc_definition'] = standard_cpcclass($row['cpc']);
			   array_push($records,$datasent);
			   //array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records) ; 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='34')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	    /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top comp filters
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		   if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 //restricting top 20 classes
		 
			 
			 $top20cpc = array();
			 $sql =  "SELECT count(*) as count,r.cpc FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.cpc order by count desc,cpc limit 20"; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top20cpc,$row['cpc']);   
		      }
		   if ( $num> 0) 
               { 
		             $s=0;
				     $cpc = Array();
		            foreach ($top20cpc as $selectedOption)
		             {
			           $parameter ="cpc = :cpc".$s; 
			           array_push($cpc,$parameter);
					   $bindparameter[':cpc'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($cpc)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $cpc).")");
		            }
		           else
		           {
			        array_push($myCondition,$cpc);
		           } 
		             
					 
			         
	           }
               else
			   {
				   
			   }
		 
		 
		 

          /*------------------add parameter for removing blank from parentassignee--------------------*/
              $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 	
			 
			 $parameter = "cpc  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "cpc!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/			 
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count, cpc, r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid ".$myCondition." group by cpc,r.parentassignee order by count desc"; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			    $datasent = Array();
			   $datasent['count'] = $row['count'];
			   $datasent['cpc'] = $row['cpc'];
			   $datasent['parentassignee'] = $row['parentassignee'];
			   $datasent['cpc_definition'] = standard_cpcclass($row['cpc']);
			   array_push($records,$datasent);
			   //array_push($records,$row);   
		  }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='35')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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

       /*------------------add parameter for removing blank from typeofassignee--------------------*/
             $parameter = "typeofassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "typeofassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as z , r.typeofassignee, year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.typeofassignee, year(r.pdate) order by pdate asc"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);   
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='36')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         /*------------------additional parameter--------------------*/
             $parameter = "r.headquarter  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.headquarter!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   /*------------------add parameter for level--------------------*/
             $parameter = "r.typeofassignee ='".$_POST['typeofassignee']."'";
			 array_push($myCondition,$parameter); 			  
      /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as z ,headquarter, year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.headquarter, year(r.pdate)"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);   
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='37')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         /*------------------additional parameter--------------------*/
             $parameter = "r.headquarter  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.headquarter!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   /*------------------add parameter for level--------------------*/
             $parameter = "r.typeofassignee ='".$_POST['typeofassignee']."'";
			 array_push($myCondition,$parameter); 			  
      /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.parentassignee) as count ,headquarter  FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.headquarter"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);   
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='38')
	{
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
		 $parent = Array();
		 $child = Array();
		 $value = Array();
		 $node = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
      
     /*------------------additional parameter--------------------*/
             $parameter = "typeofassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "typeofassignee!=''";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/		  
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct typeofassignee,parentassignee FROM relevantpatents ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		  
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   if($row['level1']!= null && trim($row['level1'])!='')
			   {
				   
				   if (!in_array($row['level1'], $child))
				   {
				      array_push($parent,"");	   
				      array_push($child,$row['level1']);
				      //array_push($value, getpatentcount("level1",$row['level1']));
					  //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level1'];
					  $rel['parent'] = 'Taxonomy';
					  array_push($node,$rel);
				   }
			   }
			   
			   if($row['level2']!= null && trim($row['level2'])!='')
			   {
				   
				   if (!in_array($row['level2'], $child))
				   {
				   array_push($parent,$row['level1']);
				   array_push($child,$row['level2']);
				   //array_push($value, getpatentcount("level2",$row['level2']));
				    //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level2'];
					  $rel['parent'] = $row['level1'];
					  array_push($node,$rel);
				   }
			   }
			   
			   if($row['level3']!= null && trim($row['level3'])!='')
			   {
				   if (!in_array($row['level3'], $child))
				   {
				   array_push($parent,$row['level2']);
				   array_push($child,$row['level3']);
				   //array_push($value, getpatentcount("level3",$row['level3']));
				   //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level3'];
					  $rel['parent'] = $row['level2'];
					  array_push($node,$rel);
				   }
			   }
			   
			   if($row['level4']!= null && trim($row['level4'])!='')
			   {
				   if (!in_array($row['level4'], $child))
				   {
				   array_push($parent,$row['level3']);
				   array_push($child,$row['level4']);
				   //array_push($value, getpatentcount("level4",$row['level4']));
				   //new way for tree
					  $rel = Array();
					  $rel['name'] = $row['level4'];
					  $rel['parent'] = $row['level3'];
					  array_push($node,$rel);
				   }
			   }
			    
		   }
		   if ( $num> 0) 
               { 
		             /*----------------just for testing---------*/
					 $rel = Array();
					  $rel['name'] = 'Taxonomy';
					  $rel['parent'] = '';
					  array_push($node,$rel);
					 /*------------*/
		             $records["parent"] = $parent;
					 $records["child"] = $child;
					 $records["values"] = $value;
					 $records["nodes"] = $node;
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='39')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 else
		 {
			 // if priority filter is not checked set priority years as acc. to pdateopt
			 
			 if($_POST['pdateopt']=='5')
			 {
				 
				   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 4;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				   
			 }
			 elseif($_POST['pdateopt']=='7')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 6;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='10')
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 9;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
			 }
			 elseif($_POST['pdateopt']=='20') 
			 {
				  $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $currentdate = date("Y-m-d");
				   $currentyear = date("Y");
				   $pastyear = date("Y") - 19;
				   $bindparameter[':pstart'] = $pastyear."-01-01";
				   $bindparameter[':pend'] =  $currentyear."-12-31";
				  
			 }
			 elseif($_POST['pdateopt']=='precovid')
			 {
				  $parameter = "pdate < :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2020-01-01";
				   
			 }
			 elseif($_POST['pdateopt']=='postcovid')
			 {
				  $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = "2019-12-31";
				   
			 }
			 else
			 {
				 
			 }
		 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' && typeofassignee='".$_POST['typeofassignee']."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
         /*------------------additional parameter--------------------*/
             $parameter = "r.parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "r.parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/	
	   /*------------------add parameter for level--------------------*/
             $parameter = "r.typeofassignee ='".$_POST['typeofassignee']."'";
			 array_push($myCondition,$parameter); 			  
      /*------------------------*/	
	   
      /*------------------------*/			
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as z ,parentassignee, year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." group by r.parentassignee, year(r.pdate)"; 
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   $num++;
			   array_push($records,$row);   
					
					
			 
			   
			   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		       
		             
					 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='40')
	{
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
		 $parent = Array();
		 $child = Array();
		 $value = Array();
		 $node = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
      
     /*------------------additional parameter--------------------*/
             $parameter = "typeofassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "typeofassignee!=''";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/		  
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
		 $sql ='';		 
	     if($_POST['emergingplayer']=='postcovid')
		 {
		 $sql =  "SELECT parentassignee,typeofassignee,min(pdate) as mindate,headquarter,count(*) as count FROM relevantpatents ".$myCondition." group by parentassignee having mindate >'2019-12-31' order by count desc"; 
		 }
		 elseif($_POST['emergingplayer']=='precovid')
		 {
		  $sql =  "SELECT parentassignee,typeofassignee,max(pdate) as maxdate,headquarter,count(*) as count FROM relevantpatents ".$myCondition." group by parentassignee having maxdate <'2020-01-01' order by count desc"; 
		 }
		 elseif($_POST['emergingplayer']=='prendpostcovid')
		 {
			$sql =  "SELECT parentassignee,typeofassignee,max(pdate) as maxdate,min(pdate) as mindate,headquarter,count(*) as count FROM relevantpatents ".$myCondition." group by parentassignee having mindate <'2020-01-01' &&  maxdate >'2019-12-31' order by count desc";  
		 }
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		  
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			       if (!in_array($row['typeofassignee'], $child))
				   {
					   array_push($parent,'Type of Assignee');
			           array_push($child,$row['typeofassignee']);
					   $rel = Array();
					   $rel['name'] = $row['typeofassignee'];
					   $rel['parent'] = 'Type of Assignee';
					   array_push($node,$rel);
				   }
			       
				   if (!in_array($row['parentassignee'], $child))
				   {
					   array_push($parent,$row['typeofassignee']);
				       array_push($child,$row['parentassignee']);
					   $rel = Array();
					   if($row['headquarter']!='' && $row['headquarter']!=null)
					   {
					   $rel['name'] = $row['parentassignee'].' ('.$row['headquarter'].')';
					   }
					   else
					   {
						$rel['name'] = $row['parentassignee'];   
					   }
					   $rel['parent'] = $row['typeofassignee'];
					   array_push($node,$rel);
				   }
			       
				  
					  
					  
					  
				   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		             /*----------------just for testing---------*/
					 $rel = Array();
					  $rel['name'] = 'Type of Assignee';
					  $rel['parent'] = '';
					  array_push($node,$rel);
					 /*------------*/
		             $records["parent"] = $parent;
					 $records["child"] = $child;
					 $records["values"] = $value;
					 $records["nodes"] = $node;
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='41')
	{
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
		 $parent = Array();
		 $child = Array();
		 $value = Array();
		 $node = Array();
		 
		 /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
      
     /*------------------additional parameter--------------------*/
             $parameter = "typeofassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "typeofassignee!=''";
			 array_push($myCondition,$parameter); 	

             $parameter = "headquarter  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "headquarter!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/		  
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
		 $sql ='';		 
	     if($_POST['emergingplayer']=='postcovid')
		 {
		 $sql =  "SELECT typeofassignee,min(pdate) as mindate,headquarter,count(*) as count FROM relevantpatents ".$myCondition." group by headquarter,typeofassignee having mindate >'2019-12-31' order by count desc"; 
		 }
		 elseif($_POST['emergingplayer']=='precovid')
		 {
		  $sql =  "SELECT typeofassignee,max(pdate) as maxdate,headquarter,count(*) as count FROM relevantpatents ".$myCondition." group by headquarter,typeofassignee having maxdate <'2020-01-01' order by count desc"; 
		 }
		 elseif($_POST['emergingplayer']=='prendpostcovid')
		 {
			$sql =  "SELECT typeofassignee,max(pdate) as maxdate,min(pdate) as mindate,headquarter,count(*) as count FROM relevantpatents ".$myCondition." group by headquarter,typeofassignee having mindate <'2020-01-01' &&  maxdate >'2019-12-31' order by count desc";  
		 }
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		  
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			       if (!in_array($row['typeofassignee'], $child))
				   {
					   array_push($parent,'Type of Assignee');
			           array_push($child,$row['typeofassignee']);
					   $rel = Array();
					   $rel['name'] = $row['typeofassignee'];
					   $rel['parent'] = 'Type of Assignee';
					   array_push($node,$rel);
				   }
			       
				  
					   array_push($parent,$row['typeofassignee']);
				       array_push($child,$row['headquarter']);
					   $rel = Array();
					   $rel['name'] = $row['headquarter'];   
					   $rel['parent'] = $row['typeofassignee'];
					   array_push($node,$rel);
				   
			       
				  
					  
					  
					  
				   
			   
			    
		   }
		   if ( $num> 0) 
               { 
		             /*----------------just for testing---------*/
					 $rel = Array();
					  $rel['name'] = 'Type of Assignee';
					  $rel['parent'] = '';
					  array_push($node,$rel);
					 /*------------*/
		             $records["parent"] = $parent;
					 $records["child"] = $child;
					 $records["values"] = $value;
					 $records["nodes"] = $node;
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='42')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "citationtype is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "citationtype!=''";
			 array_push($myCondition,$parameter); 	

             $parameter = "citationtype ='".$_POST['citationtype']."'";
			 array_push($myCondition,$parameter); 				 
       /*------------------------*/				  
		 
        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "citingowner  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "citingowner!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT count(distinct pm.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by count desc"; 
		 $sql =   "SELECT citingowner,count(*) as count FROM patentmining ".$myCondition." group by citingowner order by count desc limit 20";   
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='43')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		 
			 // first get top 30 citing companies
			 
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,citingowner FROM patentmining  where rid='".$_POST["reportid"]."' GROUP BY citingowner order by count desc limit 30"; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['citingowner']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $citingowner = Array();
		            foreach ($top10assignee as $selectedOption)
		             {
			           $parameter ="citingowner = :citingowner".$s; 
			           array_push($citingowner,$parameter);
					   $bindparameter[':citingowner'.$s] = $selectedOption;
					   $s++;
		             }
		            if(count($citingowner)>0)
		            {
			        array_push($myCondition,"(".implode(" OR ", $citingowner).")");
		            }
		           else
		           {
			        array_push($myCondition,$citingowner);
		           } 
		             
					 
			         
	           }
               else
			   {
				   
			   }
			 
		 
		 
		 /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "citationtype is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "citationtype!=''";
			 array_push($myCondition,$parameter); 	
		 
       /*------------------------*/				  
		 
        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "citingowner  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "citingowner!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT count(distinct pm.pubno) as count,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by count desc"; 
		 $sql =   "SELECT citingowner,count(*) as count,citationtype FROM patentmining ".$myCondition." group by citingowner,citationtype order by count desc";   
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='44')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,sum(score) as score from (SELECT distinct r.pubno,r.parentassignee,r.patentassetindex as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee order by score desc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='45')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 	
             
             $parameter = "r.marketscore>0";
			 array_push($myCondition,$parameter); 				 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,avg(score) as score from (SELECT distinct r.pubno,r.parentassignee,r.marketscore as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee order by score desc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='46')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 

             $parameter = "r.eciscore>0";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,avg(score) as score from (SELECT distinct r.pubno,r.parentassignee,r.eciscore as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee order by score desc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='47')
	{
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
		 $familyarray = Array();
		 $newfamilyarray = Array();
		 
	  /*-----------check filter-------------*/
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 //$parameter = "r.rid='".$_POST["reportid"]."'";
			 //array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
		             {
			           $parameter ="assignee = :assignee".$s; 
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 $queryfilterpart = "";
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $queryfilterpart = "cc = '".$_POST["criteriacc"]."'";
			 array_push($myCondition,$queryfilterpart);
			 
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $queryfilterpart = "cc = '".$_POST["criteriacc"]."'";
			 array_push($myCondition,$queryfilterpart);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "assignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }
		 
		  $querypart =   "rid='".$_POST["reportid"]."'";

         /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "assignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "assignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/			 
      /*------------------------*/				
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
		$sql =  "select cc, assignee, value from(SELECT count(*) as value, 'CN' as cc, parentassignee as assignee FROM relevantpatents WHERE ".$querypart." and (activeauthority like '%CN%') group by parentassignee
UNION
SELECT count(*) as value , 'JP' as cc, parentassignee as assignee FROM `relevantpatents` WHERE ".$querypart." and (activeauthority like '%JP%') group by parentassignee
UNION
SELECT count(*) as value , 'US' as cc, parentassignee as assignee FROM `relevantpatents` WHERE ".$querypart." and (activeauthority like '%US%')
group by parentassignee
UNION
SELECT count(*) as value , 'DE' as cc, parentassignee as assignee FROM `relevantpatents` WHERE ".$querypart." and (activeauthority like '%DE%')
GROUP by parentassignee
UNION
SELECT count(*) as value, 'FR' as cc, parentassignee as assignee FROM `relevantpatents` WHERE ".$querypart." and (activeauthority like '%FR%')
group by parentassignee
UNION
SELECT count(*) as value, 'KR' as cc, parentassignee as assignee FROM `relevantpatents` WHERE ".$querypart." and (activeauthority like '%KR%')
group by parentassignee
UNION
SELECT count(*) as value, 'GB' as cc, parentassignee as assignee FROM `relevantpatents` WHERE ".$querypart." and (activeauthority like '%GB%')
group by parentassignee) as test ".$myCondition;		 
	      
		 //$sql =  "SELECT distinct family,r.parentassignee FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
	
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   $refarray = Array();  
               $refarray['cc'] = $row['cc']; 
			   $refarray['value'] = $row['value']; 
			   $refarray['assignee'] = $row['assignee'];
			   array_push($records,$refarray);
              
			    
		   }
		   if ( $num> 0) 
               { 
		           
		           echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
			   
			   
			   
	}
	elseif($_POST['chart']=='48')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.parentassignee as assignee,year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee,year(r.pdate) order by pdate asc"; 
		         
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='49')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 

             $parameter = "r.externalcscore>0";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,avg(score) as score from (SELECT distinct r.pubno,r.parentassignee,r.externalcscore as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee order by score desc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='50')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }
		 

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT count(distinct r.pubno) as count,r.parentassignee,year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee,year(r.pdate) order by pdate asc"; 
		         
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='51')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,sum(score) as score,pdate from (SELECT distinct r.pubno,r.parentassignee,r.patentassetindex as score,year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee,pdate order by pdate asc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='52')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 	
             
             $parameter = "r.marketscore>0";
			 array_push($myCondition,$parameter);			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,avg(score) as score,pdate from (SELECT distinct r.pubno,r.parentassignee,r.marketscore as score,year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee,pdate order by pdate asc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='53')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 	
             
             $parameter = "r.externalcscore>0";
			 array_push($myCondition,$parameter);			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,avg(score) as score,pdate from (SELECT distinct r.pubno,r.parentassignee,r.externalcscore as score,year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee,pdate order by pdate asc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='54')
	{
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
		if(isset($_POST['reportid']) && trim($_POST['reportid'])!='')
		 {
			 
			 $parameter = "r.rid='".$_POST["reportid"]."'";
			 array_push($myCondition,$parameter);
		 }
		 else
		 {
			 exit;
			 
		 }
		 
		    /*----------------Joining Condition--------------------*/
		    
			 //$parameter = "r.rid=c.rid && r.pubno=c.pubno";
			 //array_push($myCondition,$parameter);
		    /*--------------------------------------------------------*/
		
		if(isset($_POST['pubdate']) && trim($_POST['pubdate'])!='' && trim($_POST['pubdate'])=='pubdate')
		 {
			 $operator = $_POST['pubdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pubdate = :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "after":
                   $parameter = "pubdate > :pubstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
                   break;
             case "before":
                   $parameter = "pubdate < :pubstart";
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pubdate > :pubstart && pubdate < :pubend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pubstart'] = $_POST["pubstart"];
				   $bindparameter[':pubend'] = $_POST["pubend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['pdate']) && trim($_POST['pdate'])!='' && trim($_POST['pdate'])=='pdate')
		 {
			 $operator = $_POST['pdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "pdate = :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "after":
                   $parameter = "pdate > :pstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
                   break;
             case "before":
                   $parameter = "pdate < :pstart";
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "pdate > :pstart && pdate < :pend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':pstart'] = $_POST["pstart"];
				   $bindparameter[':pend'] = $_POST["pend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['appdate']) && trim($_POST['appdate'])!='' && trim($_POST['appdate'])=='appdate')
		 {
			 $operator = $_POST['appdate_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "appdate = :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "after":
                   $parameter = "appdate > :appstart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
                   break;
             case "before":
                   $parameter = "appdate < :appstart";
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "appdate > :appstart && appdate < :append";
				   array_push($myCondition,$parameter);
				   $bindparameter[':appstart'] = $_POST["appstart"];
				   $bindparameter[':append'] = $_POST["append"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		
		 if(isset($_POST['updationdate']) && trim($_POST['updationdate'])!='' && trim($_POST['updationdate'])=='updationdate')
		 {
			 $operator = $_POST['updation_comparisonoperator'];
			 switch ($operator) 
			 {
             case "is":
                   $parameter = "updateddate = :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "after":
                   $parameter = "updateddate > :updateddatestart";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
                   break;
             case "before":
                   $parameter = "updateddate < :updateddatestart";
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   array_push($myCondition,$parameter);
                   break;
			case "between":
                   $parameter = "updateddate > :updateddatestart && updateddate < :updateddateend";
				   array_push($myCondition,$parameter);
				   $bindparameter[':updateddatestart'] = $_POST["updationstart"];
				   $bindparameter[':updateddateend'] = $_POST["updationend"];
                   break;
             default:
                   break;
               }
			 
			 
		 }
		 
		 if(isset($_POST['relevancycheck']))
		 {
			     $s=0;
				 $relevancy = Array();
		            foreach ($_POST['relevancy'] as $selectedOption)
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
		  
		 if(isset($_POST['assigneecheck']))
		 {
			     $s=0;
				 $assignee = Array();
		            foreach ($_POST['assignee'] as $selectedOption)
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
		 else
		 {
			 // if assignee filter is not checked retrieve top assignes as per top-com-filter
			 if(isset($_POST['topvalue']) && $_POST['topvalue']!='all')
			 {
			 $top10assignee = array();
			 $sql =  "SELECT count(*) as count,r.parentassignee FROM relevantpatents r where rid='".$_POST["reportid"]."' GROUP BY r.parentassignee order by count desc,parentassignee limit ".$_POST['topvalue']; 
		     $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
             $sth->execute();
		     $num = 0;
              while($row = $sth->fetch(PDO::FETCH_ASSOC))
		      {
			   
			   $num++;
			   array_push($top10assignee,$row['parentassignee']);   
		      }
		       if ( $num> 0) 
               { 
		             $s=0;
				     $assignee = Array();
		            foreach ($top10assignee as $selectedOption)
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
               else
			   {
				   
			   }
			 }
		 
		 }
		 
		 if(isset($_POST['level1check']))
		 {
			     $s=0;
				 $level1 = Array();
		            foreach ($_POST['level1'] as $selectedOption)
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
		 
		 if(isset($_POST['level2check']))
		 {
			     $s=0;
				 $level2 = Array();
		            foreach ($_POST['level2'] as $selectedOption)
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
		 
		 if(isset($_POST['level3check']))
		 {
			     $s=0;
				 $level3 = Array();
		            foreach ($_POST['level3'] as $selectedOption)
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
		 
		 if(isset($_POST['level4check']))
		 {
			     $s=0;
				 $level4 = Array();
		            foreach ($_POST['level4'] as $selectedOption)
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

         if(isset($_POST['taggingcheck']))
		 {
			     $s=0;
				 $tagging = Array();
		            foreach ($_POST['tagging'] as $selectedOption)
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
		 
		 if(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='activeauthority' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 
			 $parameter = "r.activeauthority like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
		 }
		 elseif(isset($_POST['corporatecriteria']) && trim($_POST['corporatecriteria'])=='family' && isset($_POST['criteriacc']) && trim($_POST['criteriacc'])!='')
		 {
			 $parameter = "r.family like '%".$_POST["criteriacc"]."%'";
			 array_push($myCondition,$parameter);
			 
		 }
		 
		 if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        /*------------------add parameter for removing blank from parentassignee--------------------*/
             $parameter = "parentassignee  is not null";
			 array_push($myCondition,$parameter); 	

             $parameter = "parentassignee!=''";
			 array_push($myCondition,$parameter); 	
             
             $parameter = "r.eciscore>0";
			 array_push($myCondition,$parameter);			 
       /*------------------------*/				 
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 //$sql =  "SELECT distinct r.parentassignee,sum(r.patentassetindex) as score FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition." GROUP BY r.parentassignee order by score desc"; 
		 $sql = "select parentassignee,avg(score) as score,pdate from (SELECT distinct r.pubno,r.parentassignee,r.eciscore as score,year(r.pdate) as pdate FROM relevantpatents r left join categorization c on r.pid=c.fk_pid  ".$myCondition."  ORDER BY score desc) as test group by parentassignee,pdate order by pdate asc";       
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	elseif($_POST['chart']=='55')
	{
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
		 
		 
		 /*if(isset($_POST['citeriaassignee']) && trim($_POST['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_POST["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }

        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }*/
	      
		 $sql = "SELECT year(r.creationtime) as year,r.type,r.reporttag,count(*) as count FROM reports r,reportallocation allo where r.rid=allo.rid && allo.email='".$_SESSION["clientemail"]."' group by  year(creationtime),type,reporttag order by count desc;";       
		 
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   if($row['reporttag']=='')
			   {
				   $row['reporttag'] ='Other';
			   }
			   array_push($records,$row); 
		   }
		   if ( $num> 0) 
               { 
	                 echo json_encode($records); 
			         
	           }
               else
			   {
				   echo 0;
			   }
	}
	
}	
}
}
else
{
    header('HTTP/1.0 401 Unauthorized');
    echo 'This is an error';
    exit;
	//header('Location: index.php');
	
}
?>