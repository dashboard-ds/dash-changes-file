<?php
session_start();
include_once('function/function.php');
date_default_timezone_set('Asia/Kolkata');
if(isset( $_SESSION["clientemail"]))
{
	if(!isset($_POST["action"]))
	{
		echo "Acccess Denied";
		exit;
	}
include 'dbconnection.php';
include('mailnotifications.php');
$data= array();
$num =0;
if (!$conn) 
{
    echo 0;
}
else
{
   if($_POST["action"]=='deletecat')
   {
	    if(isset($_POST['cid']) && trim($_POST['cid'])!='' && isset($_POST['reportid']) && trim($_POST['reportid'])!='') 
	    {
		  
          
		   if(checkreportrights($_SESSION["clientemail"],trim($_POST['reportid']))!=1)
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		   }
		   
		   mysqli_select_db($conn,$dbname);
		   
		   $sql ="Delete from categorization where cid=?";
		   if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "s",$cid);
				$cid = trim($_POST["cid"]);
				
			 }
		   if (mysqli_stmt_execute($stmt))
			 {
				 $row = array();
			     $row['cid']= trim($_POST["cid"]);
			     array_push($data,$row);
			     echo json_encode($data);
			 }
			 else
			 {
			     $row = array();
			     $row['cid']= '';
			     array_push($data,$row);
			     echo json_encode($data);
			 }
			 mysqli_close($conn);
		}
		   
   }
   elseif($_POST["action"]=='addcatrow')
	{
		
		if(isset($_POST['pubno']) && trim($_POST['pubno'])!='' && isset($_POST['reportid']) && trim($_POST['reportid'])!='' && isset($_POST['connected_pid']) && trim($_POST['connected_pid'])!='') 
	    {
			
		   if(checkreportrights($_SESSION["clientemail"],trim($_POST['reportid']))!=1)
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		   }
		   
		   
			 mysqli_select_db($conn,$dbname);
			 
			 $sql ="insert into categorization(rid,pubno,level1,level2,level3,level4,level5,fk_pid)values(?,?,?,?,?,?,?,?)";
		    
			 if($stmt = mysqli_prepare($conn, $sql))
			{
		  
     		  mysqli_stmt_bind_param($stmt, "sssssssi", $rid,$pubno,$level1,$level2,$level3,$level4,$level5,$fk_pid);
		   
			 
		   $rid = trim($_POST['reportid']);	 
		   
		   $pubno = trim($_POST['pubno']);
		   
		   if(isset($_POST['level1']))
		   {
		   $level1 = trim($_POST['level1']);
		   }
		   else
		   {
			   $level1 = '';
		   }
		   
		   if(isset($_POST['level2']))
		   {
		   $level2 = trim($_POST['level2']);
		   }
		   else
		   {
			 $level2 = '';  
		   }
		   
		   if(isset($_POST['level3']))
		   {
		   $level3 = trim($_POST['level3']);
		   }
		   else
		   {
			$level3 = '';   
		   }
		    if(isset($_POST['level4']))
		   {
		   $level4 = trim($_POST['level4']);
		   }
		   else
		   {
			   $level4 ='';
		   }
		   
		   if(isset($_POST['level5']))
		   {
		   $level5 = trim($_POST['level5']);
		   }
		   else
		   {
			$level5 = '';   
		   }
		  
		   $fk_pid = trim($_POST['connected_pid']);
		   
		   }
		   
		   if (mysqli_stmt_execute($stmt))
		   {
		       $row = array();
			   $row['cid']= mysqli_insert_id($conn);
			   array_push($data,$row);
			   echo json_encode($data);
			    
		   }
		   else
		   {
			   //echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['cid']= '';
			   array_push($data,$row);
			   echo json_encode($data);
		   }
        mysqli_close($conn);
    }
	
  }
  elseif($_POST["action"]=='sharedlist')
   {
	   if(checksharingrights($_SESSION["clientemail"],$_POST['reportid'])!=1)
		      {
			    $row = array();
			    $row['accessright'] = 0;
			    array_push($data,$row);
                echo json_encode($data); 
			    exit;
		      }
		   
		   mysqli_select_db($conn,$dbname);
		   
			$sql ="select allocation_id,rid,email,rright,allocatedby,allocatetime from reportallocation where rid =?";
			 if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "s", $rid);
				$rid = trim($_POST["reportid"]);
			 }
			 if (mysqli_stmt_execute($stmt))
			 {
				mysqli_stmt_bind_result($stmt,$allocation_id,$rid,$email,$rright,$allocatedby,$allocatetime);
				 
				 $row = array();
				 $num =0;
				  while (mysqli_stmt_fetch($stmt)) 
				   {
					   $num =1;
                      $row['allocation_id'] = $allocation_id;
					  $row['rid'] = $rid;
					  $row['email'] = $email;
					  $row['right'] = $rright;
					  $row['allocatedby'] = $allocatedby;
					  $row['allocatetime'] = $allocatetime;
					  
					  array_push($data,$row);
                   }
				 if ( $num> 0) 
                 {
			       echo json_encode($data);
			     }
		         else
		         {
					 
					 echo "0";
			     }
			 }
			 
			 mysqli_close($conn);
		   
   }
   elseif($_POST["action"]=='allocateUser')
   {
	     
        
		//----------------------Before allocating user, fetch its account information (if it doesnot exist then we need to create one and also need to send crentials along with notification to the user.)------------------------------
		   
		   $password ='';
		   $error=0;
		   $clientalid='';
		  if(trim($_POST['sname'])!=null && trim($_POST['semail'])!=null && trim($_POST['role'])!=null && $_POST['reportid']!=null)
		   {
			   //check right only owner can allocate/share report with others. 
	          if(checksharingrights($_SESSION["clientemail"],$_POST['reportid'])!=1)
		      {
			    $row = array();
			    $row['accessright'] = 0;
			    array_push($data,$row);
                echo json_encode($data); 
			    exit;
		      }
		   
		   
		   mysqli_select_db($conn,$dbname);
		   date_default_timezone_set('Asia/Kolkata');
		   $sql ="SELECT clientid FROM client where email=?";		
			
			 if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "s", $email);
				$email= trim($_POST['semail']);
			 }
			 if (mysqli_stmt_execute($stmt))
			 {
				 mysqli_stmt_bind_result($stmt,$clientid);
				 
				 $row = array();
				 $num =0;
				  if(mysqli_stmt_fetch($stmt)) 
				   {
					  $num =1;
					  
                      
					  
                   }
				
		          if ( $num> 0) 
                  {
			          $clientalid = (int)$clientid;
				      
			      }
		          else
		          {
			         //-----------need to create client-------------
								$sql ="insert into client(name,email,cpassword,createdby,creationtime,allowsignin,usertype)values(?,?,?,?,?,1,'client')";
			                   
							   if($stmt = mysqli_prepare($conn, $sql))
			                    {
		                         mysqli_stmt_bind_param($stmt, "sssss", $name,$email,$cpassword,$createdby,$creationtime);
				                 $name  = trim($_POST['sname']);
		                         $email = trim($_POST['semail']); 
								 $password= substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 12);
								 $cpassword = md5($password);
								 $createdby = $_SESSION['clientemail'];
								 $creationtime = date("Y-m-d H:i:s");
				               }
				 
				 
				               if (mysqli_stmt_execute($stmt))
		                           {
		                                $clientalid = mysqli_insert_id($conn);
										accountcreationmail($_POST['semail'],$_POST['sname'],$_POST['sharedreportname'],$password,$createdby);
			                       }
		                           else
		                           {
									//echo mysqli_stmt_error($stmt);
			                        $error = 1;
		                           }
		          }
			 }
			 
		mysqli_close($conn);		 
		if($error == 0) 
		{
         		
            $notify_report_sharing = 1;
			if(isset($_POST['dontnotify']) && trim($_POST['dontnotify'])=='1')
			{
				$notify_report_sharing = 0;
			}
		    include 'dbconnection.php';
            mysqli_select_db($conn,$dbname);
		   			
			$sql ="insert into reportallocation(rid,clientid,email,rright,allocatedby,allocatetime)values(?,?,?,?,?,?)";
			                   
							   if($stmt = mysqli_prepare($conn, $sql))
			                    {
		                         mysqli_stmt_bind_param($stmt, "ssssss", $rid,$clientid,$email,$rright,$allocatedby,$allocatetime);
				                 $rid = trim($_POST['reportid']);
								 $clientid = $clientalid;
		                         $email = trim($_POST['semail']); 
								 $rright = trim($_POST['role']); 
								 $allocatedby = $_SESSION['clientemail'];
								 $allocatetime = date("Y-m-d H:i:s");
				               }
				 
				 
				               if (mysqli_stmt_execute($stmt))
		                           {
									    if($notify_report_sharing==1)
										{
		                                reportsharingmail($_POST['semail'],$_POST['sname'],$_POST['sharedreportname'],$allocatedby);
										}
								   }
		                           else
		                           {
									//echo mysqli_stmt_error($stmt);
			                        $error = 1;
		                           }
                
				  
		   
		}
		  
		      if ($error==0)
		         {	 
		          $row = array();
			      $row['rid']= trim($_POST['reportid']);
			      array_push($data,$row);
                  echo json_encode($data);
				  
			     }
		         else
		         {
			      //echo mysqli_stmt_error($stmt);
			      $row = array();
			      $row['rid']= '';
			      array_push($data,$row);
			      echo json_encode($data);
		         }
		  
               mysqli_close($conn);	
		   }			   
   }
   elseif($_POST["action"]=='deallocateuser')
   {
      //1. only that person can deallocate user, one who is owner of the report or admin of the report.
      	  
      if(checksharingrights($_SESSION["clientemail"],$_POST['reportid'])!=1)
		      {
			    $row = array();
			    $row['accessright'] = 0;
			    array_push($data,$row);
                echo json_encode($data); 
			    exit;
		      }	  
		   
		   mysqli_select_db($conn,$dbname);
		   
		   $sql ="Delete from reportallocation where rid =? && allocation_id=?";
		   if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "ss", $rid,$allocation_id);
				$rid = trim($_POST["reportid"]);
				$allocation_id = trim($_POST["userid"]);
			 }
		   if (mysqli_stmt_execute($stmt))
			 {
				 
				 $row = array();
			     $row['rid']= trim($_POST["reportid"]);
				 $row['rowsdeleted']= mysqli_affected_rows($conn);
			     array_push($data,$row);
			     echo json_encode($data);
			 }
			 else
			 {
			   //echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['rid']= '';
			   array_push($data,$row);
			   echo json_encode($data);
			 }
			 mysqli_close($conn);
		   
   }
   elseif($_POST["action"]=='addtagging')
	{
		
		if(isset($_POST['pubno']) && trim($_POST['pubno'])!='' && isset($_POST['reportid']) && trim($_POST['reportid'])!='' && isset($_POST['taggedvalue']) && trim($_POST['taggedvalue'])!='') 
	    {
			
		   if(checkreportrights($_SESSION["clientemail"],trim($_POST['reportid']))!=1)
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		   }
		   
		   
			 mysqli_select_db($conn,$dbname);
			 
			 $sql ="update relevantpatents set tagging=? where rid=? && pubno=?";
		    
			 if($stmt = mysqli_prepare($conn, $sql))
			{
		  
     		  mysqli_stmt_bind_param($stmt, "sss", $tagging,$rid,$pubno);
		   
			 
		   $rid = trim($_POST['reportid']);	 
		   
		   $pubno = trim($_POST['pubno']);
		   
		   if(trim($_POST['taggedvalue'])=='Other')
		   {
		     $tagging = trim($_POST['definetag']);
		   }
		   else
		   {
			   $tagging = trim($_POST['taggedvalue']);
		   }
		   
		   
		  
		   
		   
		   }
		   
		   if (mysqli_stmt_execute($stmt))
		   {
		       $row = array();
			   $row['success']= 1;
			   $row['tagging']= $tagging;
			   array_push($data,$row);
			   echo json_encode($data);
			    
		   }
		   else
		   {
			   //echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['success']= '';
			   array_push($data,$row);
			   echo json_encode($data);
		   }
        mysqli_close($conn);
    }
	
  }
elseif($_POST['action']=='custometags')
{
	
	if(isset($_POST['reportid']))
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
		 $records = Array("Problematic","May be Problematic","Need Opinion of Legal Team","Need a validity check","Need Design Around","Check Prosecution History for updated Claims","Need Monitoring");
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
					  
      /*------------------------*/		
        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
	      
		 $sql =  "SELECT distinct tagging from relevantpatents ".$myCondition; 
		 //echo $sql;
		 //var_dump($bindparameter);
		 //exit;
		 $sth = $connec->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
         $sth->execute($bindparameter);
		  $num = 0;
		   while($row = $sth->fetch(PDO::FETCH_ASSOC))
		   {
			   
			   $num++;
			   array_push($records,$row['tagging']); 
		   }
		    array_push($records,"Other");
	       echo json_encode(array_values(array_unique($records))); 
			         
	           				   
	}
}
elseif($_POST["action"]=='newreport')
  {
	  $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin' && $typeofuser!='user')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
		  
		if(isset($_POST['rtitle']) && isset($_POST['rtype']) && isset($_POST['referenceno']) && isset($_POST['pono']) && isset($_POST['rrelevancy']) && isset($_POST['rscoring']) && isset($_POST['rfamilymemberslegalstatus']) && isset($_POST['rcategorization']) && isset($_POST['typeofassignee'])) 
	    {
			 mysqli_select_db($conn,$dbname);
			 $sql ="insert into reports(title,type,preparedby,uploadedby,creationtime,referenceno,pono,report_relevancy,report_scoring,report_categorization,report_typeofassignee,report_familymembers_legalstatus,report_ipccpc,report_headquarter,reporttag,pdf)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		     
			 if($stmt = mysqli_prepare($conn, $sql))
			{
		   mysqli_stmt_bind_param($stmt, "sssssssiiiiiiisi", $title,$type,$preparedby,$uploadedby,$creationtime,$referenceno,$pono,$relevancy,$scoring,$categorization,$typeofassignee,$familymembers_legalstatus,$ipccpc,$headquarter,$tag,$pdf);
		  
		  
		   $title = trim($_POST['rtitle']);
		   
		   $type = trim($_POST['rtype']);
		   
		   $preparedby = $_SESSION['clientemail'];
		   
		   $uploadedby = $_SESSION['clientemail'];
		   
		   $creationtime = date("Y-m-d H:i:s");
		  
		   $referenceno = trim($_POST['referenceno']);
		   
		   $pono = trim($_POST['pono']);
		   
		   $relevancy = trim($_POST['rrelevancy']);
		   
		   $scoring = trim($_POST['rscoring']);
		   
		   $categorization = trim($_POST['rcategorization']);
		   
		   $typeofassignee = trim($_POST['typeofassignee']);
		   
		   $familymembers_legalstatus = trim($_POST['rfamilymemberslegalstatus']);
		   
		   $ipccpc = trim($_POST['ipccpc']);
		   
		   $headquarter = trim($_POST['headquarter']);
		   
		   $pdf = trim($_POST['pdf']);
		   
		   $tag = "";
		   if(isset($_POST['tagging']) && trim($_POST['tagging'])==4)
		   {
			   $tag = trim($_POST['definetag']);
		   }
		   elseif(isset($_POST['tagging']) && trim($_POST['tagging'])!=4)
		   {
			   $tag = trim($_POST['tagging']);
		   }
		   else
		   {
			    $tag = "";
		   }
		   
		   }
		   
		   if (mysqli_stmt_execute($stmt))
		   {
		       $row = array();
			   $row['id']= mysqli_insert_id($conn);
			   array_push($data,$row);
			   echo json_encode($data);
			    
		   }
		   else
		   {
			   echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['id']= '';
			   array_push($data,$row);
			   echo json_encode($data);
		   }
       mysqli_close($conn);
    }	
		
	
  }
elseif($_POST["action"]=='uploadrelevantdata')
   {
	   
	   if(uploaddatarights($_SESSION['clientemail'],$_POST['reportid'])!='1')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
		  
		  
	   if($_POST['typeofdata']=='Relevant Data')
	   {
	   $rowerror = array();
	   mysqli_select_db($conn,$dbname);
	       $filepath =array();
		   date_default_timezone_set("UTC"); 
		   
			$error=0; 
			
				
                $externalfile= "File".time()."_";
				$tmpFilePath = $_FILES['files']['tmp_name'];
				if($tmpFilePath != "")
				{
					$filename = $_FILES['files']["name"];
                    $filetype = $_FILES['files']["type"];
                     $filesize = $_FILES['files']["size"];
					 $temext = explode('.',$filename);
					 $file_ext = strtolower(end($temext));
					 $extensions= array("csv");
                     // Verify file size - 20MB maximum
                     $maxsize = 40 * 1024 * 1024;
					 
					if(in_array($file_ext,$extensions)=== false)
					{
                        $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='Please upload CSV files only';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
						
                    }
                     if($filesize > $maxsize) 
		             {
			           //die("Error: File size is larger than the allowed limit.");
					    $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='File size is larger than the allowed limit(40MB)';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
		             }
                     // Check whether file exists before uploading it
                    if(file_exists("externalfiles/" . $_FILES['files']["name"]))
					  {
                       //echo $_FILES["photo"]['files'][$i] . " is already exists.";
					   $error = 1;
					   $row = array();
			            $row['success']= '';
						$row['error'] ='File already exist with specified name.';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
                      } 
			         else
			          {
                         move_uploaded_file($_FILES['files']["tmp_name"], "externalfiles/" . $externalfile.$_FILES['files']["name"]);
				         $filepath[0]= $externalfile.$_FILES['files']["name"];
                         //echo "Your file was uploaded successfully.";
                      } 
				}
				
				
			
		  $file = fopen('externalfiles/'.$filepath[0], "r");
		  // Skip the first line
            fgetcsv($file);
          $rowcount = 1;
		  $reportid = $_POST["reportid"];
		  mysqli_begin_transaction($conn);
		  $sql ="insert into relevantpatents (rid,pubno,pubtitle,relevancy,pdate,appdate,pubdate,parentassignee,assignee,typeofassignee,inventor,abstract,claims,family,hexcolor,tagging,epriorityno,externalcscore,internalcscore,techscore,marketscore,impactscore,eciscore,patentassetindex,activeauthority,updateddate,legalstate,relevantpatent_legalstatus,familymembers_legalstatus,expirydate,ipc,cpc,headquarter,relevantclaims) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
          $flagrollback =0;
		  while (($getData = fgetcsv($file, ",")) !== FALSE)
           {
			   
			    $rowcount = $rowcount + 1;
               
				   
			                   $stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
							   if($stmt = mysqli_prepare($conn, $sql) )
			                    {
		                         mysqli_stmt_bind_param($stmt, "sssssssssssssssssdddddddssssssssss", $rid,$pubno,$pubtitle,$relevancy,$pdate,$appdate,$pubdate,$parentassignee,$assignee,$typeofassignee,$inventor,$abstract,$claims,$family,$hexcolor,$tagging,$epriorityno,$externalcscore,$internalcscore,$techscore,$marketscore,$impactscore,$eciscore,$patentassetindex,$activeauthority,$updateddate,$legalstate,$relevantpatent_legalstatus,$familymembers_legalstatus,$expectedexpiry,$ipc,$cpc,$headquarter,$relevatclaims);
				                 $rid  = trim($_POST['reportid']);
		                         $pubno = trim($getData[0]); 
								 $pubtitle= trim($getData[1]); 
								 $relevancy = trim($getData[2]); 
								 $pdate = trim($getData[3]); 
								 $appdate = trim($getData[4]); 
								 $pubdate = trim($getData[5]); 
								 $parentassignee= trim($getData[6]); 
								 $assignee = trim($getData[7]); 
								 $typeofassignee = trim($getData[8]); 
								 $inventor = trim($getData[9]); 
								 $abstract = trim($getData[10]); 
								 $claims = trim($getData[11]); 
								 $family = trim($getData[12]); 
								 $hexcolor = trim($getData[13]) ;
								 $tagging = trim($getData[14]);
								 $epriorityno = trim($getData[15]);
								 $externalcscore = trim($getData[16]);
								 $internalcscore = trim($getData[17]);
								 $techscore = trim($getData[18]);
								 $marketscore = trim($getData[19]);
								 $impactscore = trim($getData[20]);
								 $eciscore = trim($getData[21]);
								 $patentassetindex = trim($getData[22]);
								 $activeauthority = trim($getData[23]);
								 $updateddate = trim($getData[24]);
								 $legalstate = trim($getData[25]);
								 $relevantpatent_legalstatus = trim($getData[26]);
								 $familymembers_legalstatus = trim($getData[27]);
								 if(trim($getData[28])!='')
								 {
								 $expectedexpiry = trim($getData[28]);
								 }
								 else
								 {
									 $expectedexpiry = null;
									 
								 }
								 
								 if(trim($getData[29])!='')
								 {
								 $ipc = trim($getData[29]);
								 }
								 else
								 {
									 $ipc = null;
									 
								 }
								 
								 if(trim($getData[30])!='')
								 {
								 $cpc = trim($getData[30]);
								 }
								 else
								 {
									 $cpc = null;
									 
								 }
								 
								 if(trim($getData[31])!='')
								 {
								 $headquarter = trim($getData[31]);
								 }
								 else
								 {
									 $headquarter = null;
									 
								 }
								 
								 if(trim($getData[32])!='')
								 {
								 $relevatclaims = trim($getData[32]);
								 }
								 else
								 {
									 $relevatclaims = null;
									 
								 }
				               }
				 
				 
				               if(mysqli_stmt_execute($stmt))
		                           {
		                              $successarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = 'Done';
									  array_push($rowerror, $errorarray);
										
			                       }
		                           else
		                           {
									  $flagrollback = 1;
									  $errorarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = mysqli_stmt_error($stmt);
									  array_push($rowerror, $errorarray);
			                        
		                           }
        
           }
           
		   if($flagrollback == 1)
		   {
			  mysqli_rollback($conn);
		   }
		   else
		   {
			   mysqli_commit($conn);
		   }
           fclose($file);  
		   
		                $row = array();
			            $row['success']= '';
						$row['error'] ='';
						$row['inserterror'] = $rowerror;
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
		   
	   }
elseif($_POST['typeofdata']=='Categorization')
{
	$rowerror = array();
	   mysqli_select_db($conn,$dbname);
	       $filepath =array();
		   date_default_timezone_set("UTC"); 
		   
			$error=0; 
			
				
                $externalfile= "File".time()."_";
				$tmpFilePath = $_FILES['files']['tmp_name'];
				if($tmpFilePath != "")
				{
					$filename = $_FILES['files']["name"];
                    $filetype = $_FILES['files']["type"];
                     $filesize = $_FILES['files']["size"];
					 $temext = explode('.',$filename);
					 $file_ext = strtolower(end($temext));
					 $extensions= array("csv");
                     // Verify file size - 20MB maximum
                     $maxsize = 40 * 1024 * 1024;
					 
					if(in_array($file_ext,$extensions)=== false)
					{
                        $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='Please upload CSV files only';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
						
                    }
                     if($filesize > $maxsize) 
		             {
			           //die("Error: File size is larger than the allowed limit.");
					    $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='File size is larger than the allowed limit(40MB)';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
		             }
                     // Check whether file exists before uploading it
                    if(file_exists("externalfiles/" . $_FILES['files']["name"]))
					  {
                       //echo $_FILES["photo"]['files'][$i] . " is already exists.";
					   $error = 1;
					   $row = array();
			            $row['success']= '';
						$row['error'] ='File already exist with specified name.';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
                      } 
			         else
			          {
                         move_uploaded_file($_FILES['files']["tmp_name"], "externalfiles/" . $externalfile.$_FILES['files']["name"]);
				         $filepath[0]= $externalfile.$_FILES['files']["name"];
                         //echo "Your file was uploaded successfully.";
                      } 
				}
				
				
			
		  $file = fopen('externalfiles/'.$filepath[0], "r");
		  // Skip the first line
            fgetcsv($file);
          $rowcount = 1;
		  $reportid = $_POST["reportid"];
		  mysqli_begin_transaction($conn);
		  $sql ="insert into categorization (rid,pubno,level1,level2,level3,level4,level5) values(?,?,?,?,?,?,?)";
          $flagrollback =0;
		  while (($getData = fgetcsv($file, ",")) !== FALSE)
           {
			   
			    $rowcount = $rowcount + 1;
                include 'dbconnection.php';
                mysqli_select_db($conn,$dbname);
				 $sql ="SELECT pid FROM relevantpatents where pubno=? && rid=?";		
			
			     if($stmt = mysqli_prepare($conn, $sql))
			     {
				  mysqli_stmt_bind_param($stmt, "ss", $pubno,$rid);
				  $pubno= trim($getData[0]);
				  $rid = $reportid;
			     }
			    if (mysqli_stmt_execute($stmt))
			    {
					mysqli_stmt_bind_result($stmt,$pid);
				 
				 $row = array();
				 $num =0;
				 $retrievedpid;
				  if(mysqli_stmt_fetch($stmt)) 
				   {
					  $num =1;
					  
					  $retrievedpid = $pid;
                      
					  
                   }
				
		          if ( $num> 0) 
                  {
					     include 'dbconnection.php';
                          mysqli_select_db($conn,$dbname);
						  $sql ="insert into categorization (rid,pubno,level1,level2,level3,level4,level5,fk_pid) values(?,?,?,?,?,?,?,?)";
						  $stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
							   if($stmt = mysqli_prepare($conn, $sql) )
			                    {
		                         mysqli_stmt_bind_param($stmt, "sssssssi", $rid,$pubno,$level1,$level2,$level3,$level4,$level5,$fk_pid);
				                 $rid  = trim($_POST['reportid']);
		                         $pubno = trim($getData[0]); 
								 $level1= trim($getData[1]); 
								 $level2 = trim($getData[2]); 
								 $level3 = trim($getData[3]); 
								 $level4 = trim($getData[4]); 
								 $level5 = trim($getData[5]); 
								 $fk_pid = $retrievedpid;
				               }
				 
				 
				               if(mysqli_stmt_execute($stmt))
		                           {
		                              $successarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = 'Done';
									  array_push($rowerror, $errorarray);
										
			                       }
		                           else
		                           {
									  $flagrollback = 1;
									  $errorarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = mysqli_stmt_error($stmt);
									  array_push($rowerror, $errorarray);
			                        
		                           }
				  }
				  else
				  {
					  //not matched pubno in relevant patent table.
					                 $flagrollback = 1;
									  $errorarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = 'Matching Publication number not found.';
									  array_push($rowerror, $errorarray);

				  }
				}
        
           }
           
		   if($flagrollback == 1)
		   {
			  mysqli_rollback($conn);
		   }
		   else
		   {
			   mysqli_commit($conn);
		   }
           fclose($file);  
		   
		                $row = array();
			            $row['success']= '';
						$row['error'] ='';
						$row['inserterror'] = $rowerror;
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
}
elseif($_POST['typeofdata']=='Patent Citation')
	   {
	   $rowerror = array();
	   mysqli_select_db($conn,$dbname);
	       $filepath =array();
		   date_default_timezone_set("UTC"); 
		   
			$error=0; 
			
				
                $externalfile= "File".time()."_";
				$tmpFilePath = $_FILES['files']['tmp_name'];
				if($tmpFilePath != "")
				{
					$filename = $_FILES['files']["name"];
                    $filetype = $_FILES['files']["type"];
                     $filesize = $_FILES['files']["size"];
					 $temext = explode('.',$filename);
					 $file_ext = strtolower(end($temext));
					 $extensions= array("csv");
                     // Verify file size - 30MB maximum
                     $maxsize = 40 * 1024 * 1024;
					 
					if(in_array($file_ext,$extensions)=== false)
					{
                        $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='Please upload CSV files only';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
						
                    }
                     if($filesize > $maxsize) 
		             {
			           //die("Error: File size is larger than the allowed limit.");
					    $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='File size is larger than the allowed limit(40MB)';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
		             }
                     // Check whether file exists before uploading it
                    if(file_exists("externalfiles/" . $_FILES['files']["name"]))
					  {
                       //echo $_FILES["photo"]['files'][$i] . " is already exists.";
					   $error = 1;
					   $row = array();
			            $row['success']= '';
						$row['error'] ='File already exist with specified name.';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
                      } 
			         else
			          {
                         move_uploaded_file($_FILES['files']["tmp_name"], "externalfiles/" . $externalfile.$_FILES['files']["name"]);
				         $filepath[0]= $externalfile.$_FILES['files']["name"];
                         //echo "Your file was uploaded successfully.";
                      } 
				}
				
				
			
		  $file = fopen('externalfiles/'.$filepath[0], "r");
		  // Skip the first line
            fgetcsv($file);
          $rowcount = 1;
		  $reportid = $_POST["reportid"];
		  mysqli_begin_transaction($conn);
		  $sql ="insert into patentmining (rid,srno,pubno,parentassignee,trscore,etrscore,itrscore,mcscore,ciscore,eciscore,citationtype,citingpatent,citingowner,citingowner2,citingpatent2,divisiontype,updateddate) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
          $flagrollback =0;
		  while (($getData = fgetcsv($file, ",")) !== FALSE)
           {
			   
			    $rowcount = $rowcount + 1;
               
				   
			                   $stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
							   if($stmt = mysqli_prepare($conn, $sql) )
			                    {
		                         mysqli_stmt_bind_param($stmt, "sissddddddsssssss", $rid,$srno,$pubno,$parentassignee,$trscore,$etrscore,$itrscore,$mcscore,$ciscore,$eciscore,$citationtype,$citingpatent,$citingowner,$citingowner2,$citingpatent2,$divisiontype,$updateddate);
				                 $rid  = trim($_POST['reportid']);
								 $srno = trim($getData[0]); 
		                         $pubno = trim($getData[1]); 
								 $parentassignee= trim($getData[2]); 
								 $trscore = trim($getData[3]);
								 $etrscore = trim($getData[4]);
								 $itrscore = trim($getData[5]);
								 $mcscore = trim($getData[6]);
								 $ciscore = trim($getData[7]);
								 $eciscore = trim($getData[8]);
								 $citationtype = trim($getData[9]);
								 $citingpatent = trim($getData[10]);
								 $citingowner = trim($getData[11]);
								 $citingowner2 = trim($getData[12]);
								 $citingpatent2 = trim($getData[13]);
								 $divisiontype = trim($getData[14]);
								 $updateddate = trim($getData[15]);
								 
				               }
				 
				 
				               if(mysqli_stmt_execute($stmt))
		                           {
		                              $successarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = 'Done';
									  array_push($rowerror, $errorarray);
										
			                       }
		                           else
		                           {
									  $flagrollback = 1;
									  $errorarray = array();
									  $errorarray['rowno'] = $rowcount;
									  $errorarray['msg'] = mysqli_stmt_error($stmt);
									  array_push($rowerror, $errorarray);
			                        
		                           }
        
           }
           
		   if($flagrollback == 1)
		   {
			  mysqli_rollback($conn);
		   }
		   else
		   {
			   mysqli_commit($conn);
		   }
           fclose($file);  
		   
		                $row = array();
			            $row['success']= '';
						$row['error'] ='';
						$row['inserterror'] = $rowerror;
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
		   
	   }
       elseif($_POST['typeofdata']=='PDF')
	   {
	   $rowerror = array();
	   mysqli_select_db($conn,$dbname);
	       $filepath =array();
		   date_default_timezone_set("UTC"); 
		   
			$error=0; 
			
				
                $externalfile= "File".time()."_";
				$tmpFilePath = $_FILES['files']['tmp_name'];
				if($tmpFilePath != "")
				{
					$filename = $_FILES['files']["name"];
                    $filetype = $_FILES['files']["type"];
                     $filesize = $_FILES['files']["size"];
					 $temext = explode('.',$filename);
					 $file_ext = strtolower(end($temext));
					 $extensions= array("pdf");
                     // Verify file size - 40MB maximum
                     $maxsize = 40 * 1024 * 1024;
					 
					if(in_array($file_ext,$extensions)=== false)
					{
                        $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='Please upload PDF file only';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
						
                    }
                     if($filesize > $maxsize) 
		             {
			           //die("Error: File size is larger than the allowed limit.");
					    $error = 1;
						$row = array();
			            $row['success']= '';
						$row['error'] ='File size is larger than the allowed limit(40MB)';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
						exit;
		             }
                     // Check whether file exists before uploading it
                    if(file_exists("reports/" . $_FILES['files']["name"]))
					  {
                       //echo $_FILES["photo"]['files'][$i] . " is already exists.";
					   $error = 1;
					   $row = array();
			            $row['success']= '';
						$row['error'] ='File already exist with specified name.';
						$row['inserterror'] ='';
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
                      } 
			         else
			          {
                         move_uploaded_file($_FILES['files']["tmp_name"], "reports/" . $externalfile.$_FILES['files']["name"]);
				         $filepath[0]= $externalfile.$_FILES['files']["name"];
                         //echo "Your file was uploaded successfully.";
                      } 
				}
				
		  mysqli_begin_transaction($conn);
		  $sql ="insert into reports_pdf (rid,attachment,creationtime,displayname) values(?,?,?,?)";
          $flagrollback =0;
		 
          $stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
		  
		                if($stmt = mysqli_prepare($conn, $sql) )
			                    {
		                         mysqli_stmt_bind_param($stmt, "ssss", $rid,$attachment,$creationtime,$displayname);
				                 $rid  = trim($_POST['reportid']);
		                         $attachment = $filepath[0]; 
								 $creationtime = date("Y-m-d H:i:s");
								 if(isset($_POST['pdfdisplayname']) && trim($_POST['pdfdisplayname'])!='')
								 {
									 $displayname = trim($_POST['pdfdisplayname']);
								 }
								 else
								 {
									 $displayname = $filepath[0];
								 }
								 
				                }
				 
				 
				               if(mysqli_stmt_execute($stmt))
		                           {
		                              $successarray = array();
									  $errorarray['rowno'] = 'Successfully uploaded.';
									  $errorarray['msg'] = 'Done';
									  array_push($rowerror, $errorarray);
										
			                       }
		                           else
		                           {
									  $flagrollback = 1;
									  $errorarray = array();
									  $errorarray['rowno'] = "Upload Fail";
									  $errorarray['msg'] = mysqli_stmt_error($stmt);
									  array_push($rowerror, $errorarray);
			                        
		                           }
        
           
           
		   if($flagrollback == 1)
		   {
			  mysqli_rollback($conn);
		   }
		   else
		   {
			   mysqli_commit($conn);
		   }
           
		   
		                $row = array();
			            $row['success']= '';
						$row['error'] ='';
						$row['inserterror'] = $rowerror;
			            array_push($data,$row);
			            echo json_encode($data);
					   exit;
		   
	   }	   
		
   }
elseif($_POST["action"]=='adduser')
{
	  $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
		  
	mysqli_select_db($conn,$dbname);
	if(trim($_POST['rname'])!=null && trim($_POST['email'])!=null && trim($_POST['usertype'])!=null && $_POST['password']!=null)
		   {
			   //check right only admin . 
	          /*if(checksharingrights($_SESSION["clientemail"],$_POST['reportid'])!=1)
		      {
			    $row = array();
			    $row['accessright'] = 0;
			    array_push($data,$row);
                echo json_encode($data); 
			    exit;
		      }*/
		   
		   
		  
								$sql ="insert into client(name,email,cpassword,createdby,creationtime,usertype,allowsignin)values(?,?,?,?,?,?,1)";
			                   
							   if($stmt = mysqli_prepare($conn, $sql))
			                    {
		                         mysqli_stmt_bind_param($stmt, "ssssss", $name,$email,$cpassword,$createdby,$creationtime,$usertype);
				                 $name  = trim($_POST['rname']);
		                         $email = trim($_POST['email']); 
								 $cpassword = md5($_POST['password']);
								 $createdby = $_SESSION['clientemail'];
								 $creationtime = date("Y-m-d H:i:s");
								 $usertype = $_POST['usertype'];
				               }
				 
				 
				               if (mysqli_stmt_execute($stmt))
		                           {
		                                $clientalid = mysqli_insert_id($conn);
										$row = array();
			                            $row['cid'] = $clientalid;
			                            array_push($data,$row);
                                        echo json_encode($data); 
			                            exit;
			                       }
		                           else
		                           {
									   
										$row = array();
			                            $row['cid'] = 0;
			                            array_push($data,$row);
                                        echo json_encode($data); 
			                            exit;
		                           }
		          
			 
			 
		mysqli_close($conn);
}	
}
elseif($_POST["action"]=='userinfo')
  {
	  
	  $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
	   
		  
	        mysqli_select_db($conn,$dbname);
			$sql ="select clientid,name,email,usertype,createdby,creationtime from client where clientid =?";
			 if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "s", $clientid);
				$clientid = trim($_POST["userid"]);
			 }
			 if (mysqli_stmt_execute($stmt))
			 {
				mysqli_stmt_bind_result($stmt,$clientid,$name,$email,$usertype,$createdby,$creationtime);
				 
				 $row = array();
				 $num =0;
				  while (mysqli_stmt_fetch($stmt)) 
				   {
					   $num =1;
                      $row['clientid'] = $clientid;
					  $row['name'] = $name;
					  $row['email'] = $email;
					  $row['usertype'] = $usertype;
					  $row['createdby'] = $createdby;
					  $row['creationtime'] = $creationtime;
					  array_push($data,$row);
                   }
				 if ( $num> 0) 
                 {
			       echo json_encode($data);
			     }
		         else
		         {
			     }
			 }
			 else
			 {
				 echo mysqli_stmt_error($stmt);
			 }
			 
			 
			 mysqli_close($conn);
  }
  elseif($_POST["action"]=='updateuser')
  {
	  $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
		  
		if(trim($_POST['rname'])!=null && trim($_POST['usertype'])!=null  && trim($_POST['email'])!=null) 
	    {
			 mysqli_select_db($conn,$dbname);
			 
			 if(isset($_POST['passwordchng']))
			 {
				 $sql ="update client set name=?,email=?,cpassword=?,usertype=? where clientid=?";
			 }
			 else
			 {
				$sql ="update client set name=?,email=?,usertype=? where clientid=?"; 
			 }
			 
		     
			 if($stmt = mysqli_prepare($conn, $sql))
			{
			
			    if(isset($_POST['passwordchng']))
			    {
				 mysqli_stmt_bind_param($stmt, "sssss", $name,$email,$password,$usertype,$userid);
			    }				
		        else
				{
					mysqli_stmt_bind_param($stmt, "ssss", $name,$email,$usertype,$userid);
				}
		  
		  
		   $name = trim($_POST['rname']);
		   
		   $email = trim($_POST['email']);
		   
		   if(isset($_POST['passwordchng']))
		   {
			   $password = md5($_POST['password']);
		   }
		   
		  
		   $usertype = trim($_POST['usertype']);
		   
		   
		   
		   $userid = $_POST['userid'];
		   
		   
		  
		   }
		   //mysqli_stmt_execute($stmt);
		   //echo mysqli_stmt_error($stmt);
		   if (mysqli_stmt_execute($stmt))
		   {
		       $row = array();
			   $row['id']= $_POST['userid'];
			   array_push($data,$row);
			   echo json_encode($data);
			    
		   }
		   else
		   {
			   //echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['id']= '';
			   array_push($data,$row);
			   echo json_encode($data);
		   }
       mysqli_close($conn);
    }
	
  }
  elseif($_POST['action']=='deleteuseraccount')
   {
	   $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
       
	  if(isset($_POST['userid']) && $_POST['userid']!='')
	  {
		  mysqli_select_db($conn,$dbname);
		   
		   $sql ="Delete from client where clientid =?";
		   if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "s", $userid);
				$userid = trim($_POST["userid"]);
			 }
		   if (mysqli_stmt_execute($stmt))
			 {
				 echo 1;
			 }
			 else
			 {
			   echo 0;
			 }
			 
	  }
	  else
	  {
		  echo 0;
	  }
	  mysqli_close($conn);
   }
elseif($_POST['action']=='deletereport')
   {
	   $typeofuser = checktypeofuser($_SESSION["clientemail"]);
	  if($typeofuser!='admin')
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		  }
       
	  if(isset($_POST['reportid']) && $_POST['reportid']!='')
	  {
		  mysqli_select_db($conn,$dbname);
		   
		   $sql ="Delete from reports where rid =?";
		   if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "s", $rid);
				$rid = trim($_POST["reportid"]);
			 }
		   if (mysqli_stmt_execute($stmt))
			 {
				 echo 1;
			 }
			 else
			 {
			   echo 0;
			 }
			 
	  }
	  else
	  {
		  echo 0;
	  }
	  mysqli_close($conn);
   }
   elseif($_POST["action"]=='updateclaims')
	{
		
		if(isset($_POST['updatedclaim']) && trim($_POST['updatedclaim'])!='' && isset($_POST['reportid']) && trim($_POST['reportid'])!='' && isset($_POST['pid']) && trim($_POST['pid'])!='') 
	    {
			
		   if(checkreportrights($_SESSION["clientemail"],trim($_POST['reportid']))!=1)
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		   }
		   
		   
			 mysqli_select_db($conn,$dbname);
			 
			 $sql ="update relevantpatents set claims=? where rid=? && pid=?";
		    
			 if($stmt = mysqli_prepare($conn, $sql))
			{
		  
     		  mysqli_stmt_bind_param($stmt, "sss", $claims,$rid,$pid);
		   
			 
		   $rid = trim($_POST['reportid']);	 
		   
		   $pid = trim($_POST['pid']);
		   
		   
		   $claims = trim($_POST['updatedclaim']);
		   
		   
		   
		  
		   
		   
		   }
		   
		   if (mysqli_stmt_execute($stmt))
		   {
		       $row = array();
			   $row['success']= 1;
			   array_push($data,$row);
			   echo json_encode($data);
			    
		   }
		   else
		   {
			   //echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['success']= '';
			   array_push($data,$row);
			   echo json_encode($data);
		   }
        mysqli_close($conn);
    }
	
  }
elseif($_POST["action"]=='allocatemultipleusers')
   {
	     
        
		//----------------------Before allocating user, fetch its account information (if it doesnot exist then we need to create one and also need to send crentials along with notification to the user.)------------------------------
		   
		   $password ='';
		   $error=0;
		   $clientalid='';
		   $userid = array();
		   
		  if(isset($_POST['emails']) && trim($_POST['emails'])!='' && isset($_POST['rid']) && trim($_POST['rid'])!='')
		   {
			   //check right only admin can do this thing. 
	          
		       //$userid = explode(' ',trim($_POST['emails']));
			    $userid  = preg_split('/[\s]+/', trim($_POST['emails']));
				
			   for($i=0;$i<count($userid);$i++)
			   {
				   $emailid = $userid[$i];
				   
				   include 'dbconnection.php';
				   mysqli_select_db($conn,$dbname);
		           date_default_timezone_set('Asia/Kolkata');
		   
		          $sql ="SELECT clientid FROM client where email=?";		
			
			       if($stmt = mysqli_prepare($conn, $sql))
			       {
				     mysqli_stmt_bind_param($stmt, "s", $email);
				     $email= trim($emailid);
			       }
			       
				   if (mysqli_stmt_execute($stmt))
			       {
				      mysqli_stmt_bind_result($stmt,$clientid);
				 
				         $row = array();
				         $num =0;
				         if(mysqli_stmt_fetch($stmt)) 
				         {
					       $num =1;
					     }
				
		                if ( $num> 0) 
                        {
			                $clientalid = (int)$clientid;
				        }
		                else
		                {
			                  //-----------need to create client-------------
							  continue; //skipping that email id which is not already existing as client
		                }
			        }
					
						 
		           if($error == 0 && isset($clientalid)) 
		           {
         		        include 'dbconnection.php';
                        mysqli_select_db($conn,$dbname);
		   			
			                   $sql ="insert into reportallocation(rid,clientid,email,rright,allocatedby,allocatetime)values(?,?,?,?,?,?)";
			                   
							   if($stmt = mysqli_prepare($conn, $sql))
			                    {
		                         mysqli_stmt_bind_param($stmt, "ssssss", $rid,$clientid,$email,$rright,$allocatedby,$allocatetime);
				                 $rid = trim($_POST['rid']);
								 $clientid = $clientalid;
		                         $email = trim($emailid); 
								 $rright = 'Editor'; 
								 $allocatedby = $_SESSION['clientemail'];
								 $allocatetime = date("Y-m-d H:i:s");
				               }
				 
				 
				               if (mysqli_stmt_execute($stmt))
		                           {
									    
								   }
		                           else
		                           {
									//echo mysqli_stmt_error($stmt);
			                        $error = 1;
									break;
		                           }
                
				  
		   
		              }
					
					
			   }
		       
			   if ($error==0)
		         {	 
		          $row = array();
			      $row['rid']= trim($_POST['rid']);
			      array_push($data,$row);
                  echo json_encode($data);
				  
			     }
		         else
		         {
			      //echo mysqli_stmt_error($stmt);
			      $row = array();
			      $row['rid']= '';
			      array_push($data,$row);
			      echo json_encode($data);
		         }
		  
               mysqli_close($conn);	
		  
			 
		
		  
		      
		   }			   
   }  
}
}
else
{
	header('Location: index.php');
	
}
?>