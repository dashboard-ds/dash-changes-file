<?php
session_start();
include('function/function.php');
if (isset( $_SESSION["clientemail"]))
{

$reportid = $_GET["reportid"];
$chartid = $_GET["chartid"];
	 
 
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<!--- Multiple select---->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
	<title>View Comment</title>
	<style>
	
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
				width:50px;
				height:50px;
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
			
			
			
			
	</style>
	
  </head>
  
  <body>

                     				
   <div class='container'>
	   
	   <input type='hidden' id='reportid' value='<?php echo $reportid;?>'/>
	   <input type='hidden' id='chartid' value='<?php echo $chartid;?>'/>
        
		
	    <section>
		        <h3>Comments</h3>
				<div class='projectprogress'>
				
				 <div style='text-align:right;'><a class='button button1' data-toggle="modal" data-target="#addCommentForm" id='cmntbtn_intialconversation'>Add +</a></div>
			         		<!--<table class='table table-bordered'>
							      <thead><th width='80%'>Comment</th><th>By</th></thead>
								  <tbody id='commentbody'><td colspan='2'>Currently, No comments Available</td></tbody>
							</table>-->
                    <div id='commentbody'></div>							
			   </div>
		</section>


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

  
    <!-- jQuery-->
    <script src="js/jquery.min.js"></script>
	 
	 <!-- bootstrap-->
    <script src="js/bootstrap.min.js"></script>
	
	 <!---------form validation---->
	 <script src='js/jquery.validate.js'></script>
	
	 <!----------bootstrap multiple----->
	 <script src="js/bootstrap-select.js"></script>
	 <!---ckeditor--->
	 <!--<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>-->
	 <!---user selection-->
     <script src="js/multiselect.min.js"></script>
	 <!---summernote-->
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js" integrity="sha256-oOIhv6MPxuIfln8IN7mwct6nrUhs7G1zvImKQxwkL08=" crossorigin="anonymous"></script>
	 <!--intial-->
	 <script src='js/initial.js'></script>
	 <!--- Google Charts------>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script>
	$(document).ready(function () {
		//CKEDITOR.replace( 'ccomment' );
		
		
		
		$('input,textarea,select').each(function(){
			 if($(this).prop('required'))
			 {
				 $(this).css({'border-left':'1px solid red'});
			 }
			 
		 });
		 
		 $('input,textarea,select').each(function(){
			 if($(this).data('read')==1)
			 {
				$(this).prop('readonly',true);
			 }
			 
		 });
		 
		 
	     
		 
		 
		 
		 

         

         $("#updatecmtbtn").click(function (e) {
                      e.preventDefault();
                     
					var reportid = $('#reportid').val();
					var chartid = $('#chartid').val();
                    var comment = $('#comment').val();
					var reverton = $('#reverton').val();
					
					comment = encodeURIComponent(comment);
		           $("#updatecmtbtn").prop("disabled", true);

                     $.ajax({
                           type: "POST",
                           url: "getcomments.php",
                           data: "comment="+comment+"&reverton="+reverton+"&action=addcomment&reportid="+$('#reportid').val()+"&chartid="+chartid,
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
									  alert('Done');
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
	
	     fetchcommentrecords($('#reportid').val(),$('#chartid').val());       
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
                                            content = content+"<div><div>"+data[i].ctime+" "+data[i].creationdate+"</div><div class='"+data[i].flag+"'><div class='profile-outer'><img data-name='"+data[i].commentby+"'  class='profile profile-circle'/></div><div class='msg-outer'><div class='msg-createdby'>"+data[i].commentby+"</div>"+data[i].comment.replace(/\n/g, "<br />")+"</div></div></div>";
											}
									  $('#commentbody').empty();
									  $('#commentbody').append(content);
									  $('.profile').initial();
									 }
									 
								 }
					   });
		 }
        $('#commentbody').on('click','.commentdelbtn',function(){
			 var result = confirm("Are you sure you wana delete it?");
                 if (result) {
    

			 $.ajax({
                                type: 'POST',
                                url: 'getcomment.php',
								data:'action=deleteprojectcomment&reportid='+$('#reportid').val()+'&cid='+$(this).data('cid'),
								dataType: "json",
                                success: function(data)
			                     {
									 if(data[0].pdn!='')
									 {
									  alert('Deleted');
									  fetchcommentrecords(data[0].pdn); 
	
									 }
									 else
									 {
							           alert('Something Went Wrong');
									 }
									 
							       
								 }
					   });
				 }
		 });
		 
		$("body").on('click','.replybtn',function(){
			 var reverton = $(this).data('reverton');
			 $('#reverton').val(reverton);
			 $('#remail').val($(this).data('remail'));
			 $('#rprivate').val($(this).data('rprivate'));
			 
		 });
		 
		 $("body").on('click','#cmntbtn_intialconversation',function(){
			 
			 $('#reverton').val('');
			 $('#remail').val('');
			 $('#rprivate').val('');
			 
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
