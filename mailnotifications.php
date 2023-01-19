<?php 
function accountcreationmail($useremail,$username,$reportname,$password,$sentby)
{
	

if(trim($useremail)!=null)
{
    
	$content ="<p>Dear ".$username.",<br/><br/>A user with email id ".$sentby." has just shared a dashboard report with you. <br/><br/> Since this is the first time a report has been shared with you, here is the information you need to access it</p><p>Login Page: www.icuerious.com/dash <br/></br/>Username: ".$useremail." <br/><br/>Password: ".$password."</p><p>Best,<br/>iCuerious</p>";
              
$curl = curl_init();

$data  = array();

$data['bounce_address'] = "info@bounce.icuerious.com";

$data['track_clicks']= true;
$data['track_opens']= true;

$from = array();
$from['name'] = 'iCuerious';
$from['address'] = 'info@icuerious.com';
$data['from'] = $from;


if(trim($useremail)!=null)
{
                  $to = array();
                  $ccinner = Array();
				  $ccinner['address'] = trim($useremail);
				  array_push($to,array("email_address"=>$ccinner));
				  $data['to'] = $to;
}

$data['subject'] = 'New Dash Report Account Information -iCuerious';

$data['htmlbody'] = $content;

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.zeptomail.com/v1.1/email",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>  json_encode($data),
        CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "authorization: Zoho-enczapikey wSsVR612/kT3XKt5mzalL+9rywxcA1LzEBl+ilf16XT7FvzEp8dtlEabBAPzFfQfQG44QDFB8eoomksDhzsP2tR/nw0ACCiF9mqRe1U4J3x17qnvhDzKX2pekRKILokOwg1vnmBkEswl+g==",
        "cache-control: no-cache",
        "content-type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) 
{
	echo "cURL Error #:" . $err;
	//echo 0;
	
}
else
{
	//echo $response;
	$arr = json_decode($response, true);
    if(isset($arr["data"]))
	{
		//echo 1;

	}
	else
	{
		//echo 0;
	}

    
}
			  
			  
}
}

function reportsharingmail($useremail,$username,$reportname,$sentby)
{
	

if(trim($useremail)!=null)
{
    
	$content ="<p>Dear ".$username.",<br/><br/>A user with email id ".$sentby." has shared a dashboard report -".$reportname." with you.To open the report, please visit www.icuerious.com/dash</p><p>Best,<br/>iCuerious</p>";
              
$curl = curl_init();

$data  = array();

$data['bounce_address'] = "info@bounce.icuerious.com";

$data['track_clicks']= true;
$data['track_opens']= true;

$from = array();
$from['name'] = 'iCuerious';
$from['address'] = 'info@icuerious.com';
$data['from'] = $from;


if(trim($useremail)!=null)
{
                  $to = array();
                  $ccinner = Array();
				  $ccinner['address'] = trim($useremail);
				  array_push($to,array("email_address"=>$ccinner));
				  $data['to'] = $to;
}

$data['subject'] = $sentby." has shared a dashboard report - ".$reportname." with you";

$data['htmlbody'] = $content;

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.zeptomail.com/v1.1/email",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>  json_encode($data),
        CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "authorization: Zoho-enczapikey wSsVR612/kT3XKt5mzalL+9rywxcA1LzEBl+ilf16XT7FvzEp8dtlEabBAPzFfQfQG44QDFB8eoomksDhzsP2tR/nw0ACCiF9mqRe1U4J3x17qnvhDzKX2pekRKILokOwg1vnmBkEswl+g==",
        "cache-control: no-cache",
        "content-type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) 
{
	echo "cURL Error #:" . $err;
	//echo 0;
	
}
else
{
	//echo $response;
	$arr = json_decode($response, true);
    if(isset($arr["data"]))
	{
		//echo 1;

	}
	else
	{
		//echo 0;
	}

    
}
			  
			  
}
}

?>