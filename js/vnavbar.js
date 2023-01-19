$(document).ready(function () {
	
localStorage.setItem('test','true');
window.addEventListener('storage', () => {
	        if(localStorage.getItem("test")=="False")
			{
				window.location="vendorlogout.php";
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
		window.location='vendorlogout.php';
		
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
		
		  
         function fetchnotificationrecords()
		 {
			           $.ajax({
                                type: 'POST',
                                url: 'vendorrequest.php',
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
										 window.location ="vendorhome.php";
										 return false;
									     }
									 
									     var content ="";
							            for (i = 0; i < data.length; i++) 
								           {
											
											var hrefurl='';   
											
										    if(data[i].module=='vendorproject')
											{
												hrefurl = "vendor_viewmyproject.php?projectid="+data[i].url;
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
                                url: 'vendorrequest.php',
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
										 window.location ="vendorhome.php";
										 return false;
									     }
									 
									   
									  $('#notify-counter').text(data[0].counter);
							
									 }
									 
								 }
					   }); 
		 }
		 		 setInterval(function(){notifiycounter()}, 120000);	
				 
				 
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
});	  				 
				 	  