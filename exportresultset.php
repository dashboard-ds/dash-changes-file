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
    $filename = "Resultset_DashBoard_iCuerious.xlsx";
    header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');   
    $query="SELECT pubno,pubtitle,relevancy,pdate,appdate,pubdate,parentassignee,typeofassignee,inventor,abstract,claims,family,tagging,epriorityno,legalstate,relevantpatent_legalstatus,familymembers_legalstatus FROM relevantpatents where rid=".$_GET['reportid'];
    $result = mysqli_query($conn,$query); 
    //$rows = mysqli_fetch_assoc($result); 
    $header = array(
      'Publication Number'=>'string',
      'Title'=>'string',
      'Relevancy'=>'string',
	  'Priority Date'=>'date',
	  'Application Date'=>'date',
	  'Publication Date'=>'date',
	  'Parent Assignee'=>'string',
	  'Type of Assignee'=>'string',
	  'Inventor'=>'string',
	  'Abstract'=>'string',
	  'Claims'=>'string',
	  'Family Members'=>'string',
	  'Tagging'=>'string',
	  'Priority Number'=>'string',
	  'Family Legal Status'=>'string',
	  'Publication Legal Status'=>'string',
	  'Family Members Legal Status'=>'string'
	  
    );
    $writer = new XLSXWriter();
    $writer->writeSheetHeader('Relevant Patents', $header);
    $array = array();
    while ($row=mysqli_fetch_row($result))
    {
        for ($i=0; $i<mysqli_num_fields($result); $i++ )
        {
        $array[$i] = strip_tags(utf8_encode($row[$i]));
        //$array[$i] = strip_tag($row[$i],"<p> <b> <br> <a> <img>");
        }
        $writer->writeSheetRow('Relevant Patents', $array);
    };
	
	$query="SELECT pubno,level1,level2,level3,level4,level5 FROM categorization where rid=".$_GET['reportid'];
    $result = mysqli_query($conn,$query); 
    //$rows = mysqli_fetch_assoc($result); 
    $header = array(
      'Publication Number'=>'string',
      'Level(I)'=>'string',
      'Level(II)'=>'string',
	  'Level(III)'=>'string',
	  'Level(IV)'=>'string'
    );
    //$writer = new XLSXWriter();
    $writer->writeSheetHeader('Categorization', $header);
    $array = array();
    while ($row=mysqli_fetch_row($result))
    {
        for ($i=0; $i<mysqli_num_fields($result); $i++ )
        {
        $array[$i] = $row[$i];
        //$array[$i] = strip_tag($row[$i],"<p> <b> <br> <a> <img>");
        }
        $writer->writeSheetRow('Categorization', $array);
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