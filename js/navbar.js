$(document).ready(function () {
	
localStorage.setItem('test','true');
window.addEventListener('storage', () => {
	        if(localStorage.getItem("test")=="False")
			{
				window.location="logout.php";
			}
		});
		
              //----Ask permission for desktop notification----
                 if (Notification.permission !== 'denied' || Notification.permission === "default") 
                 {
                  Notification.requestPermission(function (permission) 
                  {
                      
                  });
                 }

				 
$("#myTopnav .icon").on("click",function(){
		   if($("#myTopnav").hasClass("responsive"))
		   {
			   $("#myTopnav").removeClass("responsive");
		   }
		   else
		   {
			   $("#myTopnav").addClass("responsive");
		   }
	  });	
	  
	$("#myTopnav .logout").on("click",function(e){
		e.preventDefault();
		localStorage.setItem("test","False");
		window.location='logout.php';
		
	});  
	
	$(".profile-info .logout").on("click",function(e){
		e.preventDefault();
		localStorage.setItem("test","False");
		window.location='logout.php';
		
	});  
	  
	//Notifications
    $('body').on('click','#bell-notify',function(){
			
					
			$('.notify-outer').toggle();
			if($(this).hasClass('notify-area-not-expanded'))
			{
				$(this).removeClass('notify-area-not-expanded');
				
				var bell = $('#bell-notify').offset();
			    var notifytop  = bell.top + $('#bell-notify').height();
			    var bellleft = bell.left;
			    var bellright = $(document).outerWidth(true)-bellleft;
			    var bellwidth = $('#bell-notify').outerWidth(true)/2;
			    $('.notify-outer').css({'top':notifytop,'right':0});
			    $('.notify-arrowup').css({'right':bellright-bellwidth-5,'top':-5});
			    
			    $('.notify-content-tbl').html('Loading...');
			    fetchnotificationrecords(); 
                $('#notify-counter').empty();	
			}
			else
			{
				$(this).addClass('notify-area-not-expanded');
			}
		});
		
		//------for profile-----------
		$('body').on('click','#top-logo-profile-icon',function(){
			
			$('.notify-outer').hide();		
			$('.profile-info').toggle();
			if($(this).hasClass('profile-not-expanded'))
			{
				$(this).removeClass('profile-not-expanded');
				
				var bell = $('#top-logo-profile-icon').offset();
			    var notifytop  = bell.top + $('#top-logo-profile-icon').height();
			    var bellleft = bell.left;
			    var bellright = $(document).outerWidth(true)-bellleft;
			    var bellwidth = $('#top-logo-profile-icon').outerWidth(true)/2;
			    $('.profile-info').css({'top':notifytop,'right':0});
			    $('.profile-arrowup').css({'right':bellright-bellwidth-5,'top':-5});
			    
			    
			}
			else
			{
				$(this).addClass('profile-not-expanded');
			}
		});
		  
         function fetchnotificationrecords()
		 {
			           $.ajax({
                                type: 'POST',
                                url: 'request.php',
								data:'action=alerts&notifiedcounter='+$('#notify-counter').text(),
								dataType: "json",
                                success: function(data)
			                     {
									 
									 if(data==0)
									 {
										 $('.notify-content-tbl').empty();
									    $('.notify-content-tbl').append("<tr><td>Currently, No Notification Available</td></tr>");
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
											var hrefurl='';   
											if(data[i].module=='vendor')
											{
												hrefurl = "viewexpense.php?id="+data[i].url;
											}
										    if(data[i].module=='myproject')
											{
												hrefurl = "viewmyproject.php?projectid="+data[i].url;
											}
											if(data[i].module=='notice')
											{
												hrefurl = "noticeboard.php";
											}
											if(data[i].module=='evaluation')
											{
												hrefurl = "viewevaluation.php?evaluationid="+data[i].url;
											}
											if(data[i].module=='viewevaluation')
											{
												hrefurl = "viewmyevaluation.php?evaluationid="+data[i].url;
											}
											if(data[i].module=='project')
											{
												hrefurl = "viewproject.php?projectid="+data[i].url;  
											}
											if(data[i].module=='evaluation_ack')
											{
												hrefurl = "adminviewevaluation.php?evaluationid="+data[i].url;
											}
											if(data[i].module=='policies')
											{
												hrefurl = "viewpolicies.php";
											}
											if(data[i].module=='leavereq')
											{
												hrefurl = "viewleaverequest.php?lid="+data[i].url;
											}
											if(data[i].module=='tasks')
											{
												hrefurl = "alltasks.php";
											}
											if(data[i].module=='newidea')
											{
												hrefurl = "view_shared_idea.php?articleid="+data[i].url;
											}
											if(data[i].module=='incentive')
											{
												hrefurl = "incentive.php";
											}
											if(data[i].module=='clientmail')
											{
												hrefurl = "clientmail.php?cid="+data[i].url;
											}
                                            content = content+"<div class='msg-row-outer'><div><span class='notify-name'>"+data[i].sname+"</span></div><div><span class='notify-date'>"+data[i].creationdate+"</span><span class='notify-time'>"+data[i].ctime+"</span></div><div class='msg-row-subject'><a href='"+hrefurl+"' target='_blank'>"+data[i].msg+"</a></div>";
											
											
											
											content=content+"</div>";
                                            
									      }
									  $('.notify-content-tbl').empty();
									  $('.notify-content-tbl').append(content);
									 }
									 
								 }
					   });
		 }

		 notifiycounter();
		 function notifiycounter()
		 {
			$.ajax({
                                type: 'POST',
                                url: 'request.php',
								data:'action=counteralerts',
								dataType: "json",
                                success: function(data)
			                     {
									 
									 if(data[0].counter==0)
									 {
										 
									 }
									 else 
									 {
										 if(data[0].accessright==0)
									     {
										 alert('You are not authorised to use this Module.');
										 window.location ="home.php";
										 return false;
									     }
									 
									   
									    $('#notify-counter').text(data[0].counter);
										desktopnotify();
							
									 }
									 
								 }
					   }); 
		 }
		 		 setInterval(function(){notifiycounter()}, 600000);	//request made after ten minutes
		//advancetax notification
        fetchadvancedTaxnotification();		 
		function fetchadvancedTaxnotification()
		 {
			 var currentdate = new Date();
		     currentdate.setHours(0,0,0,0);
             var year = currentdate.getFullYear();
		     var month = currentdate.getMonth();
		     var date = currentdate.getDate();
			 var financialyear="";
			 
			 if((month+1)<4)
			 {
				 financialyear = year-1;
			 }
			 else
			 {
				 financialyear = year;
			 }
             //var startdate=financialyear+'-04-01';
			 var quatermonthstart = month+1-3+1;
			 if(quatermonthstart<10)
			 {
				 quatermonthstart='0'+quatermonthstart;
			 }
			 var quaterstart = year+'-'+quatermonthstart+'-01';
			 var endmonth = month+1;
			 if(endmonth<10)
			 {
				 endmonth ='0'+endmonth;
			 }
			 if(date<10)
			 {
				 date ='0'+date;
			 }
             var endadte = year+'-'+endmonth+'-'+date;			 
			 if((month+1)%3 ==0)
		     {
			    $.ajax({
                                type: 'POST',
                                url: 'request.php',
								data:'action=advancedTaxAlert&start='+quaterstart+'&end='+endadte,
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
										    return false;
									     }
									 
									     var content ="";
										 $('body').prepend("<div id='advancedTaxAlert' style='position:fixed;bottom:5px;left:0px;border-left:5px solid blue;background-color:#ddd;padding:10px;cursor:pointer;z-index:500;'><a href='insight.php?start="+quaterstart+"&end="+endadte+"'>Advanced Tax Due Date:15/"+endmonth+"/"+year+"</a></div>");
							             setTimeout(function() { 
                                           $('#advancedTaxAlert').fadeOut('slow'); 
                                              }, 15000); 
    
    
    
									 
									 }
									 
								 }
					   }); 
		     }
			           
		 }	


// the following function to show notification on desktop

function desktopnotify(){
    if ('Notification' in window) {
         Notification.requestPermission(function(permission)
         {
             if (permission === 'granted') 
              {
                   // create notification
                   var note = new Notification('iCuerious Mangement Console', {
                    icon: 'images/notificationimg.png',
                    body: 'You have some new notification'
                     });
              }
         });
    }
   
    
}

//including script at top
var script = document.createElement('script'); 
script.src =  "js/initial.js"; 
document.head.appendChild(script);


		 
});	  