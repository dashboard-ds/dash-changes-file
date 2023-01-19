<div class="top-option-bar">
	             <div class='top-option-bar-option'>
				     <a title="Home" class="" href="clienthome.php"> <img src="images/hometab.svg" height="35px"></a>
				  </div>
				 <?php
                  if($_SESSION["usertype"]=='admin')
		           {
			      ?>
				 <div class="tab-seperater"></div>
				 <div class='top-option-bar-option'><a title="Reports" class="" href="allfiles.php"> <img src="images/reportstab.svg" height="35px"></a></div>
				 <div class="tab-seperater"></div>
				 <div class='top-option-bar-option'><a title="Users" class="" href="userlist.php"> <img src="images/usertab.svg" height="35px"></a></div>
				 <?php
		           }?>
		         <?php
                 if($_SESSION["usertype"]=='user')
		           {
			       ?>
				   <div class="tab-seperater"></div>
			       <div class='top-option-bar-option'><a title="Reports" class="" href="allfiles.php"> <img src="images/reportstab.svg" height="35px"></a></a></div>			
			       <?php
		          }?>
				  <div class="tab-seperater"></div>
				 <div class="top-option-bar-option"><a title="Logout" class="" href="clientlogout.php"> <img src="images/logouttab.svg" height="35px"></a></div>
</div>