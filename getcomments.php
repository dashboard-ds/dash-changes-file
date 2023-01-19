<?php
session_start();
include_once('function/function.php');
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
if($_POST["action"]=='viewchartcomment')
   {
	   
		   mysqli_select_db($conn,$dbname);
		   if($_SESSION['usertype'] =='client')
		   {
		   $sql ="select c.comment,c.datetime,c.commentby_email,c.commentby_name,c.cid,c.pid from comment c, reportallocation allo where c.rid =? && c.chartid=? && c.rid=allo.rid && allo.email='".$_SESSION["clientemail"]."' order by datetime desc";
		   }
		   else if($_SESSION['usertype'] =='user')
		   {
			 $sql ="select c.comment,c.datetime,c.commentby_email,c.commentby_name,c.cid,c.pid from comment c, reports r where c.rid =? && c.chartid=? && c.rid=r.rid && r.uploadedby='".$_SESSION["clientemail"]."' order by datetime desc";
			 
		   }
		   elseif($_SESSION['usertype'] =='admin')
		   {
			  $sql ="select c.comment,c.datetime,c.commentby_email,c.commentby_name,c.cid,c.pid from comment c, reports r where c.rid =? && c.chartid=? && c.rid=r.rid order by datetime desc";
		   }
			
			 if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "ss", $reportid,$chartid);
				$reportid = trim($_POST["reportid"]);
				$chartid = $_POST['chartid'];
			 }
			 if (mysqli_stmt_execute($stmt))
			 {
				mysqli_stmt_bind_result($stmt,$comment,$datetime,$commentby_email,$commentby_name,$cid,$pid);
				 
				 $row = array();
				 $num =0;
				  while (mysqli_stmt_fetch($stmt)) 
				   {
					   $num =1;
					  $row['cid'] = $cid;
					  $row['pid'] = $pid;
                      $row['comment'] = $comment;
					  $row['datetime'] = $datetime;
					  
					  $row['commentby'] = $commentby_email;
					  $row['creationdate'] = date('d F,Y', strtotime($datetime));
					  $row['ctime']= date('h:i a', strtotime($datetime));
					  
					  if($commentby_email==$_SESSION['clientemail'])
					  {
						  $row['flag'] = 'myself';
					  }
					  else
					  {
						   $row['flag'] = 'other';
					  }
					  
					  
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
   elseif($_POST["action"]=='deletechartcomment')
   {   
		   mysqli_select_db($conn,$dbname);
		   
		   $sql ="Delete from projectcomment where pdn =? && cid=? && commentby=?";
		   if($stmt = mysqli_prepare($conn, $sql))
			 {
				mysqli_stmt_bind_param($stmt, "sss", $pdn,$cid,$commentby);
				$pdn = trim($_POST["projectid"]);
				$cid = trim($_POST["cid"]);
				$commentby = trim($_SESSION['email']);
			 }
		   if (mysqli_stmt_execute($stmt))
			 {
				 $row = array();
			     $row['pdn']= trim($_POST["projectid"]);
			     array_push($data,$row);
			     echo json_encode($data);
			 }
			 else
			 {
			   //echo mysqli_stmt_error($stmt);
			   $row = array();
			   $row['pdn']= '';
			   array_push($data,$row);
			   echo json_encode($data);
			 }
			 mysqli_close($conn);
   }
   elseif($_POST["action"]=='addcomment')
   {
	   
	     if(trim($_POST['comment'])!=null && trim($_POST['reportid'])!=null && trim($_POST['chartid'])!=null)
		 {
			   mysqli_select_db($conn,$dbname);
			    $sql ="insert into comment(rid,chartid,comment,datetime,commentby_email,commentby_name)values(?,?,?,?,?,?)";
			     if($stmt = mysqli_prepare($conn, $sql))
			     {
		           mysqli_stmt_bind_param($stmt, "ssssss", $rid,$chartid,$comment,$datetime,$commentby_email,$commentby_name);
				   $rid = trim($_POST['reportid']);
				   $chartid = trim($_POST['chartid']);
		           $comment =  trim($_POST['comment']);
                   $datetime = date("Y-m-d H:i:s");
                   //$commentby = $_SESSION["user"];
                   $commentby_name = $_SESSION["clientname"];	
                   $commentby_email = $_SESSION["clientemail"];				   
				 }
				 
				 
				 if (mysqli_stmt_execute($stmt))
		         {
		             $row = array();
			         $row['rid']= trim($_POST['reportid']);
					 $row['chartid']= trim($_POST['chartid']);
			         array_push($data,$row);
                     echo json_encode($data);
			     }
		         else
		         {
			       echo mysqli_stmt_error($stmt);
			       $row = array();
			       $row['rid']= '';
			       array_push($data,$row);
			       echo json_encode($data);
		         }
				 mysqli_close($conn);
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