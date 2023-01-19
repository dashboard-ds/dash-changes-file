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
    $filename = "PatentMining_iCuerious.xlsx";
    header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');   
    $query="SELECT srno,pubno,parentassignee,trscore,etrscore,itrscore,mcscore,ciscore,eciscore,citationtype,citingpatent,citingowner,citingowner2,citingpatent2,updateddate from patentmining where rid=".$_GET['reportid'];
    $result = mysqli_query($conn,$query); 
    //$rows = mysqli_fetch_assoc($result); 
    $header = array(
      'Sr. No.'=>'integer',
      'Publication Number'=>'string',
      'Current Owner'=>'string',
	  'Technology Relevance'=>'integer',
	  'External Technology Relevance'=>'integer',
	  'Internal Technology Relevance'=>'integer',
	  'Market Coverage'=>'integer',
	  'Competitive Impact'=>'integer',
	  'External Competitive Impact'=>'integer',
	  'Citation Type'=>'string',
	  'Citing patent'=>'string',
	  'Citing Owner'=>'string',
	  'Further citing owners'=>'string',
	  'Further citing patents'=>'string',
	  'Updation Date'=>'date'
	  
    );
    $writer = new XLSXWriter();
    $writer->writeSheetHeader('Patent Mining', $header);
    $array = array();
    while ($row=mysqli_fetch_row($result))
    {
        for ($i=0; $i<mysqli_num_fields($result); $i++ )
        {
        $array[$i] = utf8_encode($row[$i]);
        }
        $writer->writeSheetRow('Patent Mining', $array);
    };
	
	

    //$writer->writeSheet($array,'Sheet1', $header);//or write the whole sheet in 1 call    

    $writer->writeToStdOut();
    //$writer->writeToFile('example.xlsx');
    //echo $writer->writeToString();
    exit(0);
}
}
else
{
	header('Location: index.php');
	
}
?>