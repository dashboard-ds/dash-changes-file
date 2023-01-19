<?php 
session_start();
$data= array();
include('function/function.php');
if (isset( $_SESSION["clientemail"]))
{
	if(isset($_GET['reportid']) && $_GET['reportid']!='')
	{
		
	
    if(checkreportrights($_SESSION["clientemail"],$_GET['reportid'])!=1)
		   {
			$row = array();
			$row['accessright'] = 0;
			array_push($data,$row);
            echo json_encode($data); 
			exit;
		   }	  
    include_once('xlsxwriter.class.php');
    include 'dbconnection.php';
    mysqli_select_db($conn,$dbname);
    $filename = "Scoring.xlsx";
    header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');  
    
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
			 exit;
			 
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
		 
		 if(isset($_GET['citeriaassignee']) && trim($_GET['citeriaassignee'])!='')
		 {
			 
			 $parameter = "r.parentassignee = '".$_GET["citeriaassignee"]."'";
			 array_push($myCondition,$parameter);
		 }
		 

        if(count($myCondition) > 0)
				 {
					$myCondition = " WHERE (".implode(" AND ", $myCondition).")"; 
				 }
    	
    $query="SELECT pubno,pubtitle,pdate,parentassignee,abstract,family,legalstate,externalcscore,internalcscore,techscore,marketscore,impactscore,eciscore,patentassetindex,activeauthority FROM relevantpatents r ".$myCondition;
    $result = mysqli_query($conn,$query); 
    //$rows = mysqli_fetch_assoc($result); 
    $header = array(
      'Publication Number'=>'string',
      'Title'=>'string',
	  'Priority Date'=>'date',
	  'Parent Assignee'=>'string',
	  'Abstract'=>'string',
	  'Family Members'=>'string',
	  'Family Legal Status'=>'string',
	  'External Technology Relevance'=>'integer',
	  'Internal Technology Relevance'=>'integer',
	  'Technology Relevance'=>'integer',
	  'Market Coverage'=>'integer',
	  'Competitive Impact'=>'integer',
	  'External Competitive Impact'=>'integer',
	  'Patent Asset Index'=>'integer',
	  'Active Authority'=>'string'
	  
    );
    $writer = new XLSXWriter();
    $writer->writeSheetHeader('Scores', $header);
    $array = array();
    while ($row=mysqli_fetch_row($result))
    {
        for ($i=0; $i<mysqli_num_fields($result); $i++ )
        {
        $array[$i] = strip_tags(utf8_encode($row[$i]));
        //$array[$i] = strip_tag($row[$i],"<p> <b> <br> <a> <img>");
        }
        $writer->writeSheetRow('Scores', $array);
    };
	
	  

    $writer->writeToStdOut();
    exit(0);
}
}
else
{
	header('Location: index.php');
	
}
?>