<?php

function getUserIpAddr()
  {
	$staticip="103.66.211.17";
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
	{
        $ip = $_SERVER['HTTP_CLIENT_IP']; //ip from share internet
    }
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; //ip pass from proxy
    }
	else
	{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
	if($ip==$staticip)
	{
		return True;
	}
	else
	{
		return False;
	}	
    
 }
 
function checkreportrights($userid,$reportid)
 {
	 /* client existence && check whether he has right to access this report or not*/
	$typeofuser = checktypeofuser($userid);
	if($typeofuser=='')
	{
		return 0;
		exit;
	}
	               include 'dbconnection.php';
	 
                    if(!$conn) 
		            {
                    die("Connection failed: " . mysqli_connect_error());
                    }
                    mysqli_select_db($conn,$dbname);
					if($typeofuser=='client') 
					{
                    $sql = "SELECT * FROM client c join reportallocation r on c.email=r.email where c.email='".trim($userid)."' && c.allowsignin='1' && r.rid='".$reportid."'";
                    }
					elseif($typeofuser=='user') 
					{
					  $sql = "SELECT * FROM client c join reports r on c.email=r.uploadedby where c.email='".trim($userid)."' && c.allowsignin='1' && r.rid='".$reportid."' && r.uploadedby='".trim($userid)."'";	 	
					}
					elseif($typeofuser=='admin') 
					{
						$sql = "SELECT * FROM client c,reports r where c.email='".trim($userid)."' && r.rid='".$reportid."'";	
					}
                    
                    $result = mysqli_query($conn, $sql);
                    $num = mysqli_num_rows($result);
                   if($num> 0) 
				   {
					   
					   mysqli_close($conn);
					   return 1;
				   }
                   else
				   {
					   mysqli_close($conn);
					   return 0;
				   }				   
 }
 
 function checksharingrights($userid,$reportid)
 {
	$typeofuser = checktypeofuser($userid);
	if($typeofuser=='')
	{
		return 0;
		exit;
	}
	
	               include 'dbconnection.php';
	 
                    if(!$conn) 
		            {
                    die("Connection failed: " . mysqli_connect_error());
                    }
                    mysqli_select_db($conn,$dbname);
					if($typeofuser=='client') //client with role owner for a particular file  + existence of file can share/remove
					{
                    $sql = "SELECT * FROM client c join reportallocation r on c.email=r.email where c.email='".trim($userid)."' && c.allowsignin='1' && r.rid='".$reportid."' && r.rright='Owner'";
                    }
					elseif($typeofuser=='user') //user has no right to share/remove files with other
					{
					   	return 0;
						exit;
					}
					elseif($typeofuser=='admin') //file exist that admin want to share/remove
					{
						$sql = "SELECT * FROM client c,reports r where c.email='".trim($userid)."' && r.rid='".$reportid."'";	
					}
					$result = mysqli_query($conn, $sql);
                    $num = mysqli_num_rows($result);
                   if($num> 0) 
				   {
					   
					   mysqli_close($conn);
					   return 1;
				   }
                   else
				   {
					   mysqli_close($conn);
					   return 0;
				   }				   
 }
 
 function checktypeofuser($userid)
 {
	 /* client existence && check what type of user he is */
	               include 'dbconnection.php';
	 
                    if(!$conn) 
		            {
                    die("Connection failed: " . mysqli_connect_error());
                    }
                    mysqli_select_db($conn,$dbname);
					
                    $sql = "SELECT * FROM client where email='".trim($userid)."'";
                    
					$result = mysqli_query($conn, $sql);
                    $num = mysqli_num_rows($result);
                   if($num> 0) 
				   {
					   $row=mysqli_fetch_array($result);
					   mysqli_close($conn);
					   return $row['usertype'];
				   }
                   else
				   {
					   mysqli_close($conn);
					   return 0;
				   }				   
 }
 
function uploaddatarights($userid,$reportid)
{
	$typeofuser = checktypeofuser($userid);
	if($typeofuser=='')
	{
		return 0;
		exit;
	}
	/* client existence && check whether he has right to upload data in file*/
	               include 'dbconnection.php';
	 
                    if(!$conn) 
		            {
                    die("Connection failed: " . mysqli_connect_error());
                    }
                    mysqli_select_db($conn,$dbname);
					if($typeofuser=='client')
					{
                    //$sql = "SELECT c.name FROM client c join reportallocation r on c.email=r.email where c.email='".trim($userid)."' && c.allowsignin='1' && r.rid='".$reportid."'";
                        return 0;
						exit;
					}
					elseif($typeofuser=='user')
					{
					$sql = "SELECT c.name FROM client c join reports r on c.email=r.uploadedby where c.email='".trim($userid)."' && c.allowsignin='1' && r.rid='".$reportid."' && r.uploadedby='".trim($userid)."'";	
					}
					elseif($typeofuser=='admin')
					{
					$sql = "SELECT * FROM client c,reports r where c.email='".trim($userid)."' && r.rid='".$reportid."'";		
					}
					$result = mysqli_query($conn, $sql);
                    $num = mysqli_num_rows($result);
                   if($num> 0) 
				   {
					   
					   mysqli_close($conn);
					   return 1;
				   }
                   else
				   {
					   mysqli_close($conn);
					   return 0;
				   }	
}
 
 ?>