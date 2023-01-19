<?php
session_start();
include 'dbconnection.php';
include 'function/function.php';
if (isset( $_SESSION["clientemail"]))
{
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link href="css/flag-icon.css" rel="stylesheet">
	<!-- Oue select dropdown -->
    <link rel="stylesheet" href="css/chosen.css">
	<link href="css/sumoselect.css" rel="stylesheet"/>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
	<script src="https://use.fontawesome.com/9f102ed215.js"></script>
	<link href="css/tipso.css" rel="stylesheet">
	<link rel="icon" href="images/bar-chart.png" type="image/x-icon">
	<link rel="stylesheet" href="css/pretty-checkbox.css"/>
    <title>DashReports</title>
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-38089819-1', 'auto');
  ga('send', 'pageview');

</script>
  </head>
  <style>
@font-face 
{
  font-family: 'Bauziet';
  src: url('css/font/Bauziet-Norm-Regular.otf');  
}
@font-face 
{
  font-family: 'Telegraf';
  src: url('css/font/Telegraf-Regular.otf');  
}
@font-face 
{
  font-family: 'Telegraf-UltraLight';
  src: url('css/font/Telegraf-UltraLight.otf');  
}
@font-face 
{
  font-family: 'Telegraf';
  src: url('css/font/Telegraf-Bold.otf');
  font-weight: bold;
}
.new-header-child svg
{
	height:70px;
}
.wl-pr-text
{
  color : #1E150B;
  text-decoration : underline;
  font-family : 'Bauziet';
  font-size : 18px;
  color : #1E150B;
}
html,body
   {
	padding:0px;
	margin:0px;
	width:100%;
	height:100%;
	color: #777777;
    font-family: 'Bauziet','Telegraf','Bahnschrift', sans-serif;
	background-color:#F6EEE1;
    font-size:12px;                                             
	}
.wrapper
{
  display: flex;
  flex-direction: column;
  flex-wrap:nowrap;
  justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  
}	
.header
{
 width:100%;
 height:auto !important;
}
.bottom
{
  width:100%;
  height:auto;
  background-color:#fff;
  color: #C8BEAA;
  padding:5px;
}
.bottom-inner
{
	display:flex;
	flex-direction:row;
	flex-grow:1;
	flex-basis:0;
}
.bottom a
{
	text-decoration:none;
	color : #615A53;
}
.bottom a:hover, .bottom a:clicked, .bottom a:visited
{
	text-decoration:none;
	color : #615A53;
}
.bottom ul li 
{
	padding-left:5px;
	padding-right:5px;
}
#middle-container-outer-wrapper
{
display: flex;
flex-direction: row;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
width:100%;
flex-grow:1;
flex-basis:0;
border-top:1px solid #ddd;
border-bottom:1px solid #ddd;

}
#middle-container-left-wrapper
{
display: flex;
flex-direction: row;
flex-wrap: wrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
width:100%;
flex-grow:1;
flex-basis:0;
border-right: 1px solid #ddd;
border-left: 1px solid #ddd;
overflow:auto;
}
#middle-container-right-wrapper-projectlist
{
display: flex;
flex-direction: column;
flex-wrap: wrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
//flex-basis:150px;
max-width:250px;
border-right: 1px solid #ddd;
border-left: 1px solid #ddd;
overflow:auto;
}
#project-list-container a
{
	text-decoration:none;
	color:#1E150B;
	margin-top:5px;
}
#project-list-container a.activereport
{
	color:#ef3f23;
	
}
.filter-right-side-view-wrapper
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
width:200px;
display:none;
padding:5px;
}
.filter-right-side-view
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
//flex-grow: 1; 
width:100%;
height:100%;
overflow: auto;
flex-basis: 0;
opacity:0.9;
}
.all-filter-options-outer
{
flex-grow: 1;
overflow: auto;
flex-basis: 0;	
}
.all-filter-apply-outer
{
height:auto;
align-items:center;
text-align:center;
padding-top: 5px;	
}
.middle-container {
  display: flex;
  flex-direction: column;
  flex-wrap: nowrap;
  justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  flex-grow:1;
    flex-basis:0;

display:none;
}
.mc-view-publication-inner
{
	display:flex;
	flex-direction: row;
	flex-grow:1;
	
}
.mc-left {
  
  flex-grow: 1;
   
  padding:10px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
  flex-basis:0;
}

.mc-middle {
 
  flex-grow: 1;
  border-right:1px solid #ddd;
  border-left:1px solid #ddd;
  padding:10px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
 flex-basis:0;
 
}

.mc-right {
 
  flex-grow: 1;
  
  padding:10px;
 display:flex;
flex-direction:column;
flex-wrap:nowrap;
 flex-basis:0;
  
}

.logo-header
{
 padding:5px;
}
.project-title
{
padding-bottom:5px;
padding-left:5px;
font-family : 'Bauziet';
font-size : 32px;
color : #1E150B;
}
.mc-left-pubno #pubno
{
  height:auto;
  font-weight:bold;
  font-size:18px;
}
.mc-left-abstract
{
  flex-grow:1;
  overflow-y:auto;
  flex-basis:0;

}
.mc-left-biblio
{
flex-grow:1;
overflow-y:auto;
flex-basis:0;
border-bottom: 2px dotted #ddd;
}

.mc-middle-nav
{
height:auto;
}
.mc-middle-relevant-claim
{
 flex-grow:1;
  display:none;
  overflow:auto;
   flex-basis:0;
   white-space: pre-line;
}
.mc-middle-all-claim
{
  flex-grow:1;
  overflow:auto;
   flex-basis:0;
  
}
#claims
{
	white-space: pre-line;
}
.mc-right-cat
{
flex-grow:1;
overflow:auto;
flex-basis:0;
}
.mc-right-tagging
{
flex-grow:1;
overflow-y:auto;
flex-basis:0;
}
/* Style the buttons */
		   #filterContainer
		   {
		  
		   }
        .filterbtn {
                  border: none;
                 outline: none;
                 padding: 12px 16px;
                 background-color: #f1f1f1;
               cursor: pointer;
			   margin:2px;
                  }

/* Add a light #ddd background on mouse-over */
.filterbtn:hover {
  background-color: #ddd;
}

/* Add a dark background to the active button */
.filterbtn.active {
  background-color: #666;
  color: white;
}
div.sticky {
  position: -webkit-sticky; /* Safari */
  position: sticky;
  top: 0;
  font-size:18px;
  background-color:#F6EEE1;
  padding:5px 5px;
  color:#EF3C18;
  font-family: 'Telegraf';
  
}
::-webkit-scrollbar-track 
	{
    background: #F6EEE1;
    }
	::-webkit-scrollbar {
    width: 13px;
    height: 13px;
	
}

::-webkit-scrollbar-thumb {
    border: 3px solid #F6EEE1;
    border-radius: 10px;
    cursor: pointer;
    background-color: rgba(0,0,0,.2);
}
.cat-box
{
	 padding:5px 8px;
		  
		  background-color:white;
		  color:blue;
		  border:1px solid blue;
		  margin:2px;
		  cursor:pointer;
		  font-size:8px;
		  display:inline-block;
		  color: white;
         font-size: 11px;
         padding: 4px 11px;
         border-radius: 0px 17.5px  0px 17.5px;
		 border-radius: 0px 17.5px  17.5px 0px;
		 border-radius:17.5px;
         background-color: #fff;
         text-transform: capitalize;
         border: 1px solid #d3d3d3;
         margin-top: 6px;
         display: inline-block;
}
.l1
{
 //color:#607d8b;
}
.l2
{
	color:orange;
	color:black;
}
.l3
{
	//color:#f44336;
}
.l4
{
	color:blue;
	color:black;
}
.cat-separator:nth-of-type(odd) 
{
	color:#e4e1e3;
	padding:5px;
}
.cat-separator:nth-of-type(even) 
{
	
	padding:5px;
}

.middle-container-consolidate-view
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
height:100%;
width:100%;
flex-grow:1;
flex-basis:0;
overflow:auto;
padding:10px;
}
.mc-cv-list-view
{
	flex-grow:1;
   overflow:auto;
   flex-basis:0;
}
.mc-cv-list-view table
{
	border-collapse: collapse;
}
.mc-cv-list-view table th,.srno
{
	
	color:#C8BEAA;
	font-weight:normal;
}
.mc-cv-list-view table a:hover
{
	
	text-decoration:none;
}
.mc-cv-list-view table td
{
	
	border-bottom:0.5px solid black;
}
.mc-cv-page-navigation
{
	width:100%;
    height:auto;
}
.mc-pubview-navigation
{
	width:100%;
    height:auto;
}
.visibility-true
{
	display:flex;
}
table
{
	border-collapse: collapse;
}
thead{
	background-color:#EF3C18;
	background-color:#F6EEE1;
	//color: #ffffff;
	text-align: left;
}
thead tr {
    background-color: #EF3C18;
	background-color:#F6EEE1;
    //color: #ffffff;
    text-align: left;
}
table thead{
	position: sticky;
    top: 0;
    z-index: 1000;
}

table th,
table td {
    padding: 3px 4px;
	//width:200px;
	font-size:11px;
}
#categorisation table tbody tr {
    border-bottom: 1px dotted #ddd;
}
#categorisation table tbody td {
    border:none;
}

.headcol {
  position: sticky;
  left:0px;
 
  
}
td.headcol
{
	background-color:#F6EEE1;
}
th.headcol
{
	background-color:#f44336;
	background-color:#F6EEE1;
}

.middle-container-filter-view
{
	 display: flex;
     flex-direction: column;
     flex-wrap: nowrap;
    justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  flex-grow:1;
    flex-basis:0;

overflow:auto;
display:none;
}

#bcktolst,#showcomments
{
	display:none;
}
.pagination_new,.pagination_pubview
{
	
	 list-style-type: none;
  margin: 0;
  padding: 0;
  display:inline-block;
}
.pagination_new li,.pagination_pubview li
{
	display:inline-block;
	margin-left:5px;
	cursor:pointer;
	text-decoration:none;
}
.viewpub
{
	cursor:pointer;
	color:#EF3C18;
	font-weight:bold;
}
.viewpub a
{
	text-decoration:none;
}
.middle-container-insight-view
{
	 display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  justify-content: normal;
  align-items: normal;
  align-content: normal;
  height:100%;
  width:100%;
  flex-grow:1;
    flex-basis:0;

display:none;
}
.mc-iv-left-outer {
  
  width: 250px;
  padding:4px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
 font-size:10px;
}
.mc-iv-left
{
	flex-grow:1;
   overflow:auto;
   flex-basis:0;
}
.mc-iv-right {
 
  flex-grow: 1;
  border-right:1px solid #ddd;
  border-left:1px solid #ddd;
  //padding:4px;
  display:flex;
flex-direction:column;
flex-wrap:nowrap;
 flex-basis:0;
 background-color:#F6EEE1;
}
.mc-iv-right-navigation
{
	height:auto;
	padding:5px;
	/*background-color: #ecdddd;
	background-color:#e3ddd4;
	background-color:#f7e4c6;*/
	border-bottom: 1px solid #ddd;
}
.mc-iv-right-tester-outer
{
	flex-grow: 1;
}
body .getinsight
{
	cursor:pointer;
	text-transform: uppercase;
}
.insight-group
{
	color:#f44336;
	text-transform: uppercase;
	font-weight:bold;
	margin:5px 0px;
}
.actionbtn
{
	margin:5px;
	color:#f44336;
	background-color: #77777773;
	background-color:#C8BEAA;
    border: none;
	border-radius: 25px;
    color: white;
    padding: 5px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 10px;
    margin: 4px 2px;
    transition-duration: 0.4s;
    cursor: pointer;
	text-transform:uppercase;
	font-family : 'Telegraf';
  font-size : 10px;
  line-height : 14px;
  text-transform : uppercase;
  color : #FFFFFF;
  color : rgb(255, 255, 255);
}
.node circle {
	  fill: #fff;
	  stroke: #f44336;
	  stroke-width: 1px;
	}

	.node text { font: 8px Bauziet; }

	.link {
	  fill: none;
	  stroke: #e7e6e6fa;
	  stroke-width: 1px;
	}
	.insight-main-group
{
	color:#f44336;
	text-transform: uppercase;
	font-weight:bold;
	margin:5px 0px;
	font-size:18px;
}
#filtersform select,#filtersform input[type='date']
{
	border:0px;
	width:170px;
	font-size: .8rem;
    height: 2.5em;
	border: 1px solid #A4A4A4;
    min-height: 14px;
    background-color: #fff;
    border-radius: 2px;
    margin: 0;
	
}
#filtersform label
{
	color:#EF3C18;
	//font-size : 14px;
    color : #1E150B;
}
.filter-label-opt
{
	
}
.mc-iv-left ul{
	list-style-type: none;
	padding-left:10px;
}
.signout
{
	text-decoration:none;
	/*float:right;*/
	color:#777777;
	color:white;
	cursor:pointer;
}
.signout:hover
{
	color:white;
}
.signout:active
{
	color:white;
}
#insightdescription
{
	font-size:8px;
	margin-left:5px;
	color:#757575;
	text-transform: uppercase;
}
.comment-right-side-view-wrapper
{
display: flex;
flex-direction: column;
flex-wrap: nowrap;
justify-content: normal;
align-items: normal;
align-content: normal;
width:250px;
display:none;
padding:5px;
border-right: 1px solid #ddd;
    border-left: 1px solid #ddd;
}
.comment-right-side-view
{
width:100%;
height:100%;
overflow: auto;
//flex-basis: 0;
font-size:10px;
}
#cmntbtn_intialconversation
{
	cursor:pointer;
	color:#ef3f23;
}
.myself{
                background-color:#F6EEE1;
                border-radius:10px;
                padding:5px 20px 5px 20px;
                min-width: 200px;
                //display:inline-block;
                //float:right;
                //clear:both;
				position:relative;
                
            }
            .other
            {
                background-color: #F6EEE1;
                 border-radius:10px;
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
				width:10px;
				height:10px;
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
				//color:#286090;
			}
			.msg-createdby
			{
				font-weight:bold;
			}
			.lnkbtn
			{
				display:inline-block;
				//border: 2px solid #cccccc;
                padding: 5px 5px;
                text-decoration: none;
                border-radius: 8px;
				margin-right:5px;
				background-color:#C8BEAA;
			}
			.mc-left-family
			{
				   
                   //flex-grow:1;
				   overflow-y: auto;
				   //flex-basis:0px;
				   height:80px;
				   max-height:120px;
                  border-bottom: 2px dotted #ddd;
			}
			#pubtagging
			{
			color: #ef3f23;
			font-weight:bold;
			}
			#exportbtn
			{
				text-decoration:none;
				color:white;
			}
			#insight-suboption div
			{
				display:inline-block;
				margin:5px;
				
			}
			#switchchart,#top-comp-opt,#priority-year-opt
			{
				border:none;
				background-color:#F6EEE1;
			}
			#layoutsettings
			{
				float:right;
			}
			.info-header
			{
				
				background-color:#1E150B;
				display:flex;
				flex-direction: row;
                flex-wrap:nowrap;
                justify-content: normal;
                align-items: normal;
                align-content: normal;
				height:auto;
                width:100%;
			}
			.info-header-child
		    {
				flex-grow:1;
				color:#fff;
				padding:5px;
				font-size:8px;
			}
			.new-header
			{
				display:flex;
				flex-direction: row;
                flex-wrap:nowrap;
                justify-content: center;
                align-items: center;
                align-content: normal;
				height:auto;
                width:100%;
			}
			.new-header-child
		    {
				flex-grow:1;
				padding:5px;
			}
			.project-title
			{
				font-size:32px;
				display:inline-block;
			}
			.top-option-bar-outer
			{
				padding:0px;
				background-color:#ef3f23;
				height:100%;
			}
			.top-option-bar
			{
				display:flex;
				flex-direction:row;
				width:100%;
				height:100%;
				background-color:#ef3f23;
				justify-content: center;
                align-items: center;
				
			}
			.top-option-bar-option
			{
				flex-grow:1;
				text-align: center;
				color:white;
				font-size:25px;
			}
			.logo-text
			{
				font-size:20px;
			}
			#middle-container a
			{
				text-decoration:none;
			    color: #777777;
			}
			#open-max-window
			{
				float:right;
				margin-right:10px;
				cursor:pointer;
			}
			#processing-div
			{
				padding:5px;
			}
			.full-screen 
{
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background: rgba(0,0,0,0.6);
  Z-index:1001;
}

.flex-container-center {
  display: flex;
  flex-direction: row;
  justify-content: right;
  height:100%;
  width:100%;
    align-items: flex-start;
}

.hidden-outer
{
	display:none;
}
.layout-modal
{
	display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    justify-content: normal;
    align-items: normal;
    align-content: normal;
	background-color:#F6EEE1;
	overflow-y:auto;
	height:80%;
	padding:20px;
	margin-top:auto;
	width:300px;
    border-radius:20px;
	
}
#layout-insight-header
{
  
  font-size : 20px;
  color : #EF3C18;
  color : rgb(239, 60, 24);
}
#closelayoutbtn
{
  font-size : 20px;
  color : #EF3C18;
  color : rgb(239, 60, 24);
  float:right;
  cursor:pointer;
}
.colors-pallets
{
	display:flex;
	flex-direction:column;
}
.colors-pallets div{
	display:flex;
	flex-direction:row;
	//padding:5px;
}
.colors-pallets div span{
	padding:2px;
}
.colors-pallets div span div:first-child{
	border-top-left-radius:5px;
	border-bottom-left-radius:5px;
}
.colors-pallets div span div:last-child{
	border-top-right-radius:5px;
	border-bottom-right-radius:5px;
}
.mc-iv-left .l2
{
	padding-left:10px;
	color:#777777;
}
.mc-iv-left .l2::before
{
	content:'\00bb';
	//content:'\2219';
}
.mc-iv-left .l3
{
	padding-left:15px;
	color:#777777;
}
.mc-iv-left .l3::before
{
	content:'\00bb\00bb';
}
.mc-iv-left .l4
{
	padding-left:20px;
	color:#777777;
}
#tester{
	overflow:auto;
}
#insightshowdatabtn,#insightshowdatabtn:hover,#insightshowdatabtn:active
{
	color:#ef3f23;
	text-decoration:none;
	
}
#pagination
{
	flex-grow:1;
	text-align:right;
}
#pagination_pubview_outer
{
	flex-grow:1;
	display:none;
	text-align:right;
}
body .tab-seperater
{
	border-left: 1px solid #c8beaa;
    height: 100%;
}
.filter-right-side-view-wrapper-popup
{
	display:none;
	position:absolute;
	bottom:0px;
	background-color: #C8BEAA;
	width:250px;
	z-index:10000;
	padding:20px;
	border-radius:20px;
	opacity:0.95;
}
.notify-arrowup,.notify-arrowup-comment
{
	width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 5px solid #C8BEAA;
    position: absolute;
}
.filter-right-side-view-wrapper-popup ::-webkit-scrollbar-track 
	{
    background: #C8BEAA;
	
    }
	.filter-right-side-view-wrapper-popup ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
	
}

.filter-right-side-view-wrapper-popup ::-webkit-scrollbar-thumb {
    border: 3px solid #C8BEAA; 
	
    border-radius: 10px;
    cursor: pointer;
    background-color: rgba(0,0,0,.2);
}
.comment-right-side-view-wrapper-popup
{
	display:none;
	position:absolute;
	bottom:0px;
	background-color: #C8BEAA;
	width:300px;
	z-index:10000;
	padding:20px;
	border-radius:20px;
	opacity:0.95;
}

.comment-right-side-view-wrapper-popup ::-webkit-scrollbar-track 
	{
    background: #C8BEAA;
	
    }
	.comment-right-side-view-wrapper-popup ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
	
}

.comment-right-side-view-wrapper-popup ::-webkit-scrollbar-thumb {
    border: 3px solid #C8BEAA; 
	
    border-radius: 10px;
    cursor: pointer;
    background-color: rgba(0,0,0,.2);
}
.activetab
{
	background-color:#8f8c87;
}
.comment-right-side-view h5
			{
				font-size:24px;
				font-family: 'Telegraf-Medium';
				color:#1E150B;
			}
.mc-iv-left
{
	color:#1e150b;
}
.insight-card
{
	//flex-grow:1;
	//flex-basis:0;
	//flex: 1 0 calc(33.33% - 10px);
	width:33.33%;
	height:100%;
}
.insight-card-inner
{
	//margin:5px;
	height:calc(100% - 10px);
	width:calc(100% - 10px);
	//background-color:red;
}
.insight-card-wd-100
{
	//flex-grow:1;
	//flex-basis:0;
	//flex: 1 0 calc(33.33% - 10px);
	width:100%;
	height:100%;
}
.anchordiv
{
	border-bottom: 2px dotted #ddd;
	padding:5px;
}
#middle-container-right-wrapper-projectlist .sticky
{
	font-weight: bold;
    text-decoration: underline;
}
.filter-card-wd-100
{
	width:100%;
	height:auto;
	padding:10px;
	position: sticky;
    top: 0;
    background-color: #F6EEE1;
    padding: 10px 5px;
    z-index: 2000;
    border-bottom: 1px solid #ddd;
}
a.gotoexternal
{
	text-decoration:none;
	float:right;
	color: #EF3C18;
    font-family: 'Telegraf';
	cursor:pointer;
}
.site-header
{
	font-size: 18px;
    background-color: #F6EEE1;
    padding: 5px 5px;
    color: #EF3C18;
    font-family: 'Telegraf';
	font-weight:bold;
}
div#customiziedlogo 
{
    color: #ef3f23;
    font-size: 32px;
    font-family: 'Bauziet';
}
div#customiziedlogo .logo-dash
{
	font-family: 'Telegraf-UltraLight', Telegraf;
    font-weight: 100;
}
.new-header-child a
{
	text-decoration:none;
}
.new-header-child a:hover
{
	text-decoration:none;
}
	</style>
  <body>
    <div class='wrapper'>
	<div class='info-header'>
	     <div class='info-header-child'>+91-(988)-873-2426 (Ind)</div>
		 <div class='info-header-child'>+1-(339)-237-3075 (USA)</div>
		 <div class='info-header-child'>info@icuerious.com</div>
	</div>
	<div class='new-header'>
	     <div class='new-header-child'><a href='clienthome.php'>
		 <?php 
			  if(isset($_SESSION["firm"]) && $_SESSION["firm"]!='')
			  {
				  echo "<div id='customiziedlogo'>".$_SESSION["firm"]." <span class='logo-dash'>dashboard</span></div>";
			  }
			  else
			  {
				  ?>
		 <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 664.94 105.93" style="enable-background:new 0 0 664.94 105.93;" xml:space="preserve">
<style type="text/css">

	.st0{fill:#EF3C18;}
	.st1{fill:#EF3D17;}
	.st2{font-family:'Telegraf-UltraLight';}
	.st3{font-size:49.3698px;}
	.st4{fill:#0A0A0A;}
</style>
<g>
	<g>
		<rect x="134.78" y="51.45" class="st0" width="4.8" height="22.41"/>
	</g>
	<g>
		<path class="st0" d="M207.83,65.73c0.69-1.61,1.04-3.35,1.04-5.17V39.98h-4.8v20.58c0,1.16-0.22,2.27-0.67,3.3
			c-0.44,1.02-1.05,1.93-1.83,2.7c-0.77,0.77-1.68,1.39-2.7,1.83c-2.07,0.89-4.54,0.89-6.61,0c-1.02-0.44-1.93-1.05-2.7-1.83
			c-0.77-0.77-1.39-1.68-1.83-2.7c-0.44-1.03-0.67-2.14-0.67-3.3V39.98h-4.76l-0.04,20.58c0,1.82,0.35,3.56,1.04,5.17
			c0.69,1.62,1.65,3.05,2.85,4.25c1.2,1.2,2.63,2.16,4.25,2.85c3.24,1.38,7.1,1.38,10.35,0c1.62-0.69,3.05-1.65,4.25-2.85
			C206.18,68.77,207.14,67.34,207.83,65.73z"/>
	</g>
	<g>
		<polygon class="st0" points="237.33,44.78 237.33,39.98 214.36,39.98 214.36,73.86 237.33,73.86 237.33,69.05 219.16,69.05 
			219.16,59.32 233.7,59.32 233.7,54.52 219.16,54.52 219.16,44.78 		"/>
	</g>
	<g>
		<rect x="277.96" y="39.98" class="st0" width="4.8" height="33.88"/>
	</g>
	<g>
		<path class="st0" d="M330.03,69.97c1.2,1.2,2.63,2.16,4.25,2.85c3.24,1.38,7.11,1.38,10.35,0c1.62-0.69,3.05-1.65,4.25-2.85
			c1.2-1.2,2.16-2.63,2.85-4.25c0.69-1.61,1.04-3.35,1.04-5.17V39.98h-4.8v20.58c0,1.16-0.23,2.27-0.67,3.3
			c-0.44,1.02-1.05,1.93-1.83,2.7c-0.77,0.77-1.68,1.39-2.7,1.83c-2.07,0.89-4.54,0.89-6.61,0c-1.02-0.44-1.92-1.05-2.7-1.83
			c-0.78-0.78-1.39-1.68-1.83-2.7c-0.44-1.03-0.67-2.14-0.67-3.3V39.98h-4.76l-0.04,20.58c0,1.82,0.35,3.56,1.04,5.17
			C327.87,67.34,328.83,68.77,330.03,69.97z"/>
	</g>
	<g>
		<path class="st0" d="M174.1,53.07l4.54-1.63c-0.13-0.38-0.27-0.75-0.43-1.12c-0.89-2.06-2.11-3.87-3.62-5.39
			c-1.52-1.52-3.33-2.74-5.39-3.62c-2.05-0.88-4.27-1.33-6.6-1.33c-2.33,0-4.55,0.45-6.6,1.33c-2.06,0.89-3.87,2.11-5.39,3.62
			c-1.52,1.52-2.74,3.33-3.63,5.39c-0.88,2.05-1.33,4.27-1.33,6.6c0,2.33,0.45,4.55,1.33,6.6c0.89,2.06,2.11,3.87,3.63,5.39
			c1.52,1.52,3.33,2.74,5.39,3.63c2.05,0.88,4.27,1.33,6.6,1.33c2.33,0,4.55-0.45,6.6-1.33c2.06-0.89,3.87-2.11,5.39-3.63
			c1.38-1.38,2.52-3.02,3.39-4.86l-4.56-1.64c-0.59,1.15-1.35,2.19-2.25,3.09c-1.09,1.09-2.39,1.97-3.86,2.6
			c-2.98,1.29-6.51,1.29-9.46,0c-1.45-0.63-2.74-1.51-3.83-2.6c-1.09-1.09-1.96-2.38-2.6-3.83c-0.64-1.47-0.97-3.06-0.97-4.74
			c0-1.65,0.32-3.23,0.96-4.71c0.64-1.47,1.51-2.77,2.6-3.86c1.09-1.09,2.38-1.96,3.83-2.6c2.95-1.28,6.48-1.29,9.46,0
			c1.47,0.64,2.77,1.51,3.86,2.6c1.09,1.09,1.96,2.39,2.6,3.86C173.89,52.48,174,52.77,174.1,53.07z"/>
	</g>
	<g>
		<circle class="st0" cx="137.19" cy="42.18" r="3.68"/>
	</g>
	<g>
		<path class="st0" d="M316.93,44.91c-1.52-1.52-3.33-2.74-5.39-3.63c-2.05-0.88-4.27-1.33-6.61-1.33c-2.34,0-4.56,0.45-6.61,1.33
			c-2.06,0.89-3.88,2.11-5.39,3.63c-1.52,1.52-2.74,3.34-3.63,5.39c-0.88,2.05-1.33,4.27-1.33,6.61c0,2.34,0.45,4.56,1.33,6.61
			c0.89,2.06,2.11,3.87,3.63,5.39c1.52,1.52,3.34,2.74,5.4,3.63c2.05,0.88,4.27,1.33,6.61,1.33c2.33,0,4.56-0.45,6.61-1.33
			c2.06-0.89,3.87-2.11,5.39-3.63c1.52-1.52,2.74-3.34,3.63-5.39c0.88-2.05,1.33-4.27,1.33-6.61c0-2.34-0.45-4.56-1.33-6.61
			C319.67,48.25,318.45,46.43,316.93,44.91z M316.11,61.66c-0.63,1.45-1.51,2.74-2.6,3.84c-1.09,1.09-2.39,1.97-3.86,2.6
			c-1.48,0.64-3.07,0.96-4.72,0.96c-1.69,0-3.28-0.32-4.75-0.97c-1.46-0.64-2.75-1.51-3.84-2.6c-1.09-1.09-1.97-2.38-2.6-3.84
			c-0.64-1.47-0.97-3.07-0.97-4.75c0-1.65,0.32-3.24,0.97-4.72c0.64-1.47,1.51-2.77,2.6-3.86c1.09-1.09,2.38-1.96,3.84-2.6
			c2.95-1.28,6.49-1.28,9.47,0c1.47,0.64,2.77,1.51,3.86,2.6c1.09,1.09,1.97,2.39,2.6,3.86c0.64,1.49,0.97,3.07,0.97,4.72
			C317.08,58.6,316.75,60.19,316.11,61.66z"/>
	</g>
	<g>
		<path class="st0" d="M262.38,57.56v-1.07c4.44-0.1,7.34-3.49,7.34-6.84l0,0c0-1.31-0.26-2.56-0.76-3.74
			c-0.51-1.18-1.21-2.22-2.08-3.09c-0.87-0.87-1.91-1.57-3.09-2.08c-1.17-0.51-2.43-0.76-3.74-0.76H244.4v33.88h4.8V59.78h9.82
			c1.33,0,2.49,0.48,3.44,1.43c0.95,0.95,1.43,2.11,1.43,3.44v0v9.22h4.8v-9.22v0c0-1.14-0.21-2.23-0.59-3.26
			C267.29,59.37,264.98,57.69,262.38,57.56z M249.21,54.52v-9.73h10.84c1.33,0,2.49,0.48,3.44,1.43c0.95,0.95,1.43,2.11,1.43,3.44
			c0,1.33-0.48,2.49-1.43,3.44c-0.95,0.95-2.11,1.43-3.44,1.43H249.21z"/>
	</g>
	<g>
		<g>
			<path class="st0" d="M360.79,62.81c1.35,4.15,4.2,6.91,9.06,6.91c4.43,0,6.91-2.05,6.91-5.23c0-2.8-1.82-4.3-5.93-5.18
				l-4.34-0.98c-5.46-1.17-8.54-3.83-8.54-9.1c0-5.37,4.53-9.29,11.39-9.29c6.86,0,10.69,4.11,11.81,7.7l-4.39,2.01
				c-1.35-3.69-3.97-5.55-7.84-5.55c-3.97,0-6.3,2.05-6.3,4.9c0,2.61,1.59,3.97,5.18,4.76l4.48,0.98c5.79,1.26,9.38,4.43,9.38,9.34
				c0,5.79-4.34,9.85-12.09,9.85c-7.1,0-11.76-3.78-13.26-9.06L360.79,62.81z"/>
		</g>
	</g>
</g>
<g>
	<circle class="st1" cx="42.84" cy="33.6" r="12.23"/>
</g>
<g>
	<circle class="st1" cx="69.59" cy="61.37" r="12.23"/>
</g>
<rect x="31.2" y="48.9" class="st1" width="22.63" height="24.68"/>
<rect x="85.72" y="22.15" class="st1" width="22.63" height="51.43"/>
<text transform="matrix(1 0 0 1 395.6519 75.1571)" class="st0 st2 st3">dashboard</text>
<g>
	<g>
		<path class="st0" d="M111.49,22.25h6.49v1.17h-2.59v6.16h-1.36v-6.16h-2.54V22.25z"/>
		<path class="st0" d="M119.04,29.58v-7.33h2.24l1.39,3.83c0.21,0.59,0.27,1.24,0.28,2.21h0.52c0.01-0.96,0.09-1.62,0.3-2.21
			l1.38-3.83h2.18v7.33h-1.36v-3.29c0-0.97,0.22-1.99,0.65-3.04l-0.51-0.21l-2.36,6.54h-1.14l-2.36-6.54l-0.51,0.21
			c0.43,1.04,0.65,2.04,0.65,3.04v3.29H119.04z"/>
	</g>
</g>
</svg>
<?php
			  } 
			  ?>
		 </a></div>
		 <div class='new-header-child'><span class='project-title'>Project Statistics</span></div>
		 <div class='new-header-child'><span class='wl-pr-text'>Welcome <?php echo $_SESSION["clientname"];?></span></div>
		 <div class='new-header-child top-option-bar-outer'>
	             <?php include "clienttopnavbar.php"?>	     
		 </div>
	</div>
 <!-- <div class='header'><button  class='actionbtn'  style='float:right;'><a target='_blank' id='exportbtn' href='exportresultset.php?reportid=<?php echo$_GET["reportid"]?>' title='Export Resultset In Excel Sheet'>Export</a></button><button title='Show result set' class='actionbtn' id='bcktolst' style='float:right;'>Result set</button><button title='show/hide filters' class='actionbtn filter-area-not-expanded' id='showfilters' style='float:right;'>Filters</button><button title='show/hide comments' class='actionbtn comment-area-not-expanded' id='showcomments' style='float:right;'>Comments</button><button title='show insights' class='actionbtn' id='showinsights' style='float:right;'>Insights</button></div>-->
  <div id='middle-container-outer-wrapper'>
  <div id='middle-container-left-wrapper'>
  <div class='insight-card'><div class='insight-card-inner' id='chart-1'></div></div>
 
    
  </div> 
   
  </div>
  <div class='bottom'>
     <div class='bottom-inner'>
      <div class='bottom-content'>© iCuerious 2012-2022. All Rights Reserved
	      <a style='float:right;display:none;' id="google_translate_element"></a>
	  </div>
      
	  
	</div>
	</div>
  </div>
  

<div class="full-screen hidden-outer flex-container-center">
  <div class='layout-modal'>
  <div style='margin-bottom:10px;'>
    <span id='layout-insight-header'>Layout Settings</span>  <span id='closelayoutbtn'>X</span>
  </div>
  <div>
				  
                              <input type='checkbox' name='xaxislabel' id='xaxislabel'  />	<label for='xaxislabel'>Show complete X axis tick labels</label>
							  <br/><label>X axis tick label angle</label> <select name='xaxislabelangle' id='xaxislabelangle'><option>Auto</option><option>0</option><option>45</option><option>90</option><option>-45</option><option>-90</option></select>
                              <br/><label>X axis tick font size</label> <select name='xaxislabelfont' id='xaxislabelfont'><option>6</option><option>8</option><option selected>10</option><option>12</option><option>14</option><option>16</option><option>18</option><option>20</option></select>
							  <br/><input type='checkbox' name='yaxislabel' id='yaxislabel'  checked />	<label for='yaxislabel'>Show complete Y axis tick labels</label>
							  <br/><label>Y axis tick label angle</label> <select name='yaxislabelangle' id='yaxislabelangle'><option>Auto</option><option>45</option><option>90</option><option>-45</option><option>-90</option></select>
                              <br/><label>Y axis tick font size</label> <select name='yaxislabelfont' id='yaxislabelfont'><option>6</option><option>8</option><option selected>10</option><option>12</option><option>14</option><option>16</option><option>18</option><option>20</option></select>
					          <br/><label>Max Bubble size</label><select name='bubbleresizer' id='bubbleresizer'><option>10</option><option>20</option><option>30</option><option>40</option><option selected>50</option></select>
					  
                     
				     
			                  
				              
					    <div class='colors-pallets'>
						  <div><label>Select Color Palette</label></div> 
					      <div><span><input type='radio' name='palette' class='palette' value='p1'/></span><span id='p1'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p2'/></span><span id='p2'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p3'/></span><span id='p3'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p4'/></span><span id='p4'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p5'/></span><span id='p5'></span></div>
						  <div><span><input type='radio' name='palette' class='palette' value='p6'/></span><span id='p6'></span></div>
						  <div><span><input type='radio' name='palette' class='palette'  checked value='p7'/></span><span id='p7'></span></div>
					 
					   </div>
				  </div>
</div>		   
			 
</div>  

 
  <!-- jQuery-->
    <script src="js/jquery.min.js"></script>
	 <!-- bootstrap-->
    <script src="js/bootstrap.min.js"></script>
	 <!-- select dropdown this page -->
		<script src="js/chosen.jquery.js" type="text/javascript"></script>
		<script src="js/jquery.sumoselect.js"></script>
	<!---------form validation---->
	 <script src='js/jquery.validate.js'></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js"></script>
	  <script src="js/tipso.js"></script>
	  <script src='js/initial.js'></script>
      <script type="text/javascript">
      function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en',layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL}, 'google_translate_element');
      }
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
 <script language="javascript" src="js/CSVExport.js"></script>   
	<script>
	  $(document).ready(function () 
	   {
		   /*$("#assignee").chosen({width:"170px",
    height: "2.5em"});*/
	$('body .summoselect').SumoSelect({search: true, searchText: 'Enter here.'});
	$('.show-help').tipso({background:'#777777',color:'#fff',position: 'top',size: 'small'});
	var fd;	
	var sectiontoshow_after_applyingfilter = 1; // 1 - consilidated view , 2 insight view, 3 pub view
	var showchart_after_applyingfilter = '';
	var insightclicked = 0;
	var reportid = '<?php echo $reportmetainfo["rid"];?>';
	var exportdata;
	var currentpage;

var charttitle;		
	
	                ipindicatorinsights();
					function ipindicatorinsights()
					{
						
						fd = $("#filtersformcriteria").serialize();
						$('#middle-container-left-wrapper').empty();
						
							

                       $('#middle-container-left-wrapper').html("<div id='processing'>We are processing your request.... <img src='images/process.gif'/></div>");
                       
                       	
                        $.ajax({
                           type: "POST",
                           url: "retrievetext.php",
                           data: fd+"&action=getinsight&chart=55",
                           timeout: 600000,
						   dataType: "json",
						   success: function (data) 
						   {
	
							    $('#processing').remove();
							    if(data!=0)
								  {
									  
									  var x =[];
									  var y =[];
									  var z =[];
									  var m = [];
									  for (var i = 0; i < data.length; i++) 
								      {
										  y.push(data[i].year);
										  x.push(data[i].type);
										  z.push(data[i].count);
										  m.push(data[i].reporttag);
										  
									  } 
									  
									   

       
                                     var ouputArray = removeDuplicates(m);
									 for (var i = 0; i < ouputArray.length; i++) 
								      {
										  
										 var globalx =[];
                                         var globaly = [];
                                         var globalz =[]; 
										 var globalmetadata;
										 var chartid= "chart-"+i;
										  $('#middle-container-left-wrapper').append('<div class="insight-card"><div class="insight-card-inner" id="'+chartid+'"></div></div>');
									        
									 for (var j = 0; j < m.length; j++) 
								      {
										  
										  if(m[j]==ouputArray[i])
										  {
										  globaly.push(y[j]);
										  globalx.push(x[j]);
										  globalz.push(z[j]);
										  
										  }
										  
									  } 
									        
											globalmetadata ={'drilldown':'enable','divisiontype':ouputArray[i],'x':'type'};
											changecharttype('h3Dbar',ouputArray[i],chartid,globalx,globaly,globalz,globalmetadata);
									  
									  } 
									 
									 
										   
											
							       }
								   else
								   {
									   $('#middle-container-left-wrapper').html("<div>No result set found.</div>");
								   }

                                 							   
                          },
                          error: function (e) 
						  {
                           console.log("ERROR : ", e);
                           if(e.status=='401')
							            {
								           window.location.replace('index.php');
							            }
                          }
                      });
					  
					  						
					}	
					               function removeDuplicates(inputArray) 
									   {
                                        var i;
                                        var len = inputArray.length;
                                        var outputArray = [];
                                        var temp = {};

                                       for (var i = 0; i < len; i++) {
                                            temp[inputArray[i]] = 0;
                                               }
                                        for (i in temp) 
										    {
                                             outputArray.push(i);
                                            }
                                          return outputArray;
                                        }	
						
/*-----------------------Insights----------------------------------------*/	
	
				
function changecharttype(charttype,charttitle,chartposid,globalx,globaly,globalz,globalmetadata)
	   {
		            $('#insight-result-count-clickedpoint').html('');
					
		            var tracecolor= ['#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844','#f44336','#f85b47','#fc6f59','#ff826b','#ff947d','#ffa590','#ffb6a4','#ffc7b8','#ffd7cd','#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844'];
		            var layout = {uniformtext:{minsize:8, mode:'hide'},font: {family: 'Bauziet, Telegraf, Bahnschrift, sans-serif'},colorway : tracecolor,title:{text:charttitle.toUpperCase(),xref:'container',x:0,xanchor:'left',family: 'Bauziet', color:'#1E150B', size:24, pad:{l : 20}},paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1','yaxis':{'automargin': true,'tickfont':{}},'xaxis':{autotick: true,'categoryorder':'total descending','tickfont':{}},showlegend: false,hovermode: "closest",barmode: 'stack',annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}],geo: {bgcolor:'#F6EEE1',countrycolor:'#ccc',coastlinecolor:'#ccc',showframe: false,showcountries:true,projection: {type: 'robinson'}},margin: {}};
					
					var bubblelayout = {font: {family: 'Bauziet, Telegraf, Bahnschrift, sans-serif'},colorway : tracecolor,title:{text:charttitle.toUpperCase(),xref:'container',x:0,xanchor:'left',family: 'Bauziet', color:'#1E150B', size:24,pad:{l : 20}},paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1','yaxis': {autotick: false,'automargin': true,'tickfont':{}},'xaxis':{autotick: false,tickangle: 35,'tickfont':{}},showlegend: false,hovermode: "closest",annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}],margin: {}};
					
					/*-------custom settings-----*/
					bubblelayout.xaxis.tickangle = $('#xaxislabelangle').val();
				    layout.xaxis.tickangle = $('#xaxislabelangle').val();
					
					bubblelayout.yaxis.tickangle = $('#yaxislabelangle').val();
				    layout.yaxis.tickangle = $('#yaxislabelangle').val();
					
					bubblelayout.xaxis.tickfont.size = $('#xaxislabelfont').val();
				    layout.xaxis.tickfont.size = $('#xaxislabelfont').val();
					
					bubblelayout.yaxis.tickfont.size = $('#yaxislabelfont').val();
				    layout.yaxis.tickfont.size = $('#yaxislabelfont').val();
						
					if($('#xaxislabel').prop('checked')==true)
					{
						bubblelayout.xaxis.automargin = true;
						layout.xaxis.automargin = true;
					}
					else
					{
						bubblelayout.xaxis.automargin = false;
						layout.xaxis.automargin = false;
					}
					
					if($('#yaxislabel').prop('checked')==true)
					{
						bubblelayout.yaxis.automargin = true;
					    layout.yaxis.automargin = true;
					}
					else
					{
						bubblelayout.yaxis.automargin = false;
						layout.yaxis.automargin = false;
					}
					
					var pvalue = $("input[name='palette']:checked").val();
					var paletteselected=[];
                    if(pvalue=='p1')
					{
                      bubblelayout.colorway = palette1;
					  layout.colorway = palette1;
					  paletteselected = palette1;
                    }
					else if(pvalue=='p2')
					{
                      bubblelayout.colorway = palette2;
					  layout.colorway = palette2;
					  paletteselected = palette2;
                    }
					else if(pvalue=='p3')
					{
                      bubblelayout.colorway = palette3;
					  layout.colorway = palette3;
					  paletteselected = palette3;
                    }
					else if(pvalue=='p4')
					{
                      bubblelayout.colorway = palette4;
					  layout.colorway = palette4;
					  paletteselected = palette4;
                    }
					else if(pvalue=='p5')
					{
                      bubblelayout.colorway = palette5;
					  layout.colorway = palette5;
					  paletteselected = palette5;
                    }
					else if(pvalue=='p6')
					{
                      bubblelayout.colorway = palette6;
					  layout.colorway = palette6;
					  paletteselected = palette6;
                    }
					else if(pvalue=='p7')
					{
                      bubblelayout.colorway = palette7;
					  layout.colorway = palette7;
					  paletteselected = palette7;
                    }
					/*---------------------------*/
					var stackedcolors = ['#F4BDAE','#F9E2B8','#FFF2D7','#C0EFF5','#53C6D9'];
					var stackedcolors2 = ["#4c78a8", "#f58518", "#e45756", "#72b7b2", "#54a24b", "#eeca3b", "#b279a2", "#ff9da6", "#9d755d", "#bab0ac"];
					var stackedlayout = {font: {family: 'Telegraf,Bahnschrift, sans-serif'},colorway : stackedcolors2,title:charttitle,paper_bgcolor:'#F6EEE1',plot_bgcolor:'#F6EEE1',barmode: 'stack',showlegend: true,hovermode: "closest",legend: {},xaxis:{'categoryorder':'total descending'},yaxis:{},annotations: [{xref: 'paper',yref: 'paper',x: 1,xanchor: 'left',y: 0,yanchor: 'top',text: '&#169; iCuerious',showarrow: false,opacity:0.3}]};
					var ccdata = {"AM":"ARM","AR":"ARG","AT":"AUT","AU":"AUS","BA":"BIH","BE":"BEL","BG":"BGR","BR":"BRA","BY":"BLR","CA":"CAN","CH":"CHE","CL":"CHL","CN":"CHN","CO":"COL","CR":"CRI","CS":"CSK","CU":"CUB","CY":"CYP","CZ":"CZE","DD":"DDR","DE":"DEU","DK":"DNK","DO":"DOM","DZ":"DZA","EC":"ECU","EE":"EST","EG":"EGY","ES":"ESP","FI":"FIN","FR":"FRA","GB":"GBR","GE":"GEO","GR":"GRC","GT":"GTM","HK":"HKG","HN":"HND","HR":"HRV","HU":"HUN","ID":"IDN","IE":"IRL","IL":"ISR","IN":"IND","IS":"ISL","IT":"ITA","JO":"JOR","JP":"JPN","KE":"KEN","KG":"KGZ","KR":"KOR","KZ":"KAZ","LT":"LTU","LU":"LUX","LV":"LVA","MA":"MAR","MC":"MCO","MD":"MDA","ME":"MNE","MN":"MNG","MO":"MAC","MT":"MLT","MW":"MWI","MX":"MEX","MY":"MYS","NI":"NIC","NL":"NLD","NO":"NOR","NZ":"NZL","PA":"PAN","PE":"PER","PH":"PHL","PL":"POL","PT":"PRT","RO":"ROU","RS":"SRB","RU":"RUS","SA":"SAU","SE":"SWE","SG":"SGP","SI":"SVN","SK":"SVK","SM":"SMR","SV":"SLV","TH":"THA","TJ":"TJK","TN":"TUN","TR":"TUR","TT":"TTO","TW":"TWN","UA":"UKR","US":"USA","UY":"URY","UZ":"UZB","VN":"VNM","YU":"YUG","ZA":"ZAF","ZM":"ZMB","ZW":"ZWE"};
					var config = {
						           displaylogo: false,
					               displayModeBar: false,
								   responsive: true, 
								   modeBarButtonsToRemove:["zoom2d", "pan2d", "select2d", "lasso2d", "zoomIn2d", "zoomOut2d", "autoScale2d", "resetScale2d", "hoverClosestCartesian", "hoverCompareCartesian", "zoom3d", "pan3d", "resetCameraDefault3d", "resetCameraLastSave3d", "hoverClosest3d", "orbitRotation", "tableRotation", "zoomInGeo", "zoomOutGeo", "resetGeo", "hoverClosestGeo", "sendDataToCloud", "hoverClosestGl2d", "hoverClosestPie", "toggleHover", "resetViews", "toggleSpikelines", "resetViewMapbox"],
								   modeBarButtonsToAdd: [
                                                         /*{
                                                          name: 'Comments',
                                                          icon: {'svg':'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="grey" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'},
                                                          direction: 'up',
                                                          click: function(gd) 
	                                                           { 
	                                                           var url = "viewcomment.php?reportid="+reportid+"&chartid="+showchart_after_applyingfilter;
	                                                           window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=300,left=500,width=400,height=350");
						                                       }
						                                 },
														 {
                                                          name: 'Export Chart Data',
                                                          icon: {'svg':'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M13 12h3l-4 4-4-4h3V8h2v4zm2-8H5v16h14V8h-4V4zM3 2.992C3 2.444 3.447 2 3.999 2H16l5 5v13.993A1 1 0 0 1 20.007 22H3.993A1 1 0 0 1 3 21.008V2.992z"/></svg>'},
														  direction: 'up',
                                                          click: function(gd) 
	                                                           { 
	                                                               exportchartdata();
						                                       }
						                                 }*/
														 ]
				                 };
		   
		   
									  $('#tester').remove();
					                  //$('#middle-container-left-wrapper').append("<div class='insight-card'><div class='insight-card-inner' id='"+chartposid+"'></div></div>");
									  TESTER = document.getElementById(chartposid);
									  
									 
									  
									  
									  if(charttype=='h3Dbar')
									  {
										  layout.yaxis.categoryorder = 'total ascending';
										  layout.showlegend = true;
										  layout.yaxis.automargin = true;
										  
										  var data = [{
                                                  x: globalz,
                                                  y: globalx,
                                               type:'bar',
											   orientation:'h',
											   opacity:0.8,
											   mode:'lines+markers',
                                              text : globalz,
										 customdata: globaly,
									  hovertemplate: "<b>%{x}</b> :%{y}(%{customdata})<extra></extra>",
									   textposition: 'auto',
                                            transforms: [{
                                              type: 'groupby',
                                              groups: globaly,
                                               }]												   
											      }];
										  Plotly.newPlot(
									              TESTER, 
												   data,
												  layout,
												  config);
												  if(globalmetadata.drilldown=='enable')
												  {
													  TESTER.on('plotly_click', function(data){
													  $("#insight-result-count-clickedpoint").html('<img src="images/process.gif">');
													  setTimeout(function(){
                                                       var pts = [];
	                                                   var ptsval = [];
                                                      for(var i=0; i < data.points.length; i++)
	                                                  {
		                                               if(typeof globalmetadata.x !== "undefined")
													   {
		                                               
		                                               var pointclicked_xvalue = data.points[i].y;
		                                               ptsval.push('type='+encodeURIComponent(pointclicked_xvalue));
													   }
													   if(typeof globalmetadata.divisiontype !== "undefined")
													   {
														   if(globalmetadata.divisiontype =='Other')
														   {
															   ptsval.push('divisiontype=');
														   }
														   else
														   {
															    ptsval.push('divisiontype='+encodeURIComponent(globalmetadata.divisiontype));
														   }
		                                                
													   }
                                                       }
	
	                                                   var redirecturl = 'clientfiles.php?'+ptsval.join('&');
                                                       window.open(redirecturl, '_blank');
                                                      },1000);
													  });
													  
												  }
									  }
									  
	   }
	   
	   
	   
	   var palette1 = ['#696969','#556b2f','#8b4513','#228b22','#483d8b','#008b8b','#9acd32','#00008b','#8fbc8f','#8b008b','#b03060','#ff4500','#ff8c00','#ffd700','#7cfc00','#deb887','#00ff7f','#dc143c','#00ffff','#00bfff','#0000ff','#a020f0','#1e90ff','#fa8072','#90ee90','#add8e6','#ff1493','#7b68ee','#ee82ee','#ffc0cb'];			 
	   for ( var i = 0; i < palette1.length; i++ ) 
	   {
		   $('#p1').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette1[i]+"'></div>");
	   }
	 var palette2 = ['#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844','#f44336','#f85b47','#fc6f59','#ff826b','#ff947d','#ffa590','#ffb6a4','#ffc7b8','#ffd7cd','#f3cec9', '#e7a4b6', '#cd7eaf', '#a262a9', '#6f4d96', '#3d3b72', '#182844'];
	   for ( var i = 0; i < palette2.length; i++ ) 
	   {
		   $('#p2').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette2[i]+"'></div>");
	   }
	   var palette3 = ['#228b22','#00008b','#b03060','#ff4500','#ffff00','#deb887','#00ff00','#00ffff','#ff00ff','#6495ed'];
	   for ( var i = 0; i < palette3.length; i++ ) 
	   {
		   $('#p3').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette3[i]+"'></div>");
	   }
	   var palette4 = ['#003f5c','#2f4b7c','#665191','#a05195','#d45087','#f95d6a','#ff7c43','#ffa600'];
	   for ( var i = 0; i < palette4.length; i++ ) 
	   {
		   $('#p4').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette4[i]+"'></div>");
	   }
	   var palette5 = ['#004c6d','#25617e','#3f768f','#588ca0','#72a3b2','#8cb9c4','#a8d0d7','#c4e7ea','#e1ffff'];
	   for ( var i = 0; i < palette5.length; i++ ) 
	   {
		   $('#p5').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette5[i]+"'></div>");
	   }
       var palette6 = ["#ffa842","#663dc2","#dcc730","#9493ff","#3c7700","#d3007b","#8bd97d","#b80026","#01d0bb","#ff753b","#01c9e6","#bc6000","#016eb0","#ff9c4d","#ffa2fa","#007549","#894000","#fdb68f","#715600","#905c41"];
       for ( var i = 0; i < palette6.length; i++ ) 
	   {
		   $('#p6').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette6[i]+"'></div>");
	   }
	   var palette7 = ['#F3613A','#F58668','#FBC3B4','#615A52','#887F78','#C9BEAA','#CBCC64','#63CE80','#95CDCE','#F2A7B3','#E25476','#AF4B72','#7F407F','#D82550','#EF3C18','#F2613A','#F58768','#FBC3B3','#C8BEAA','#887F77','#615A53','#E3C399','#CACC64','#EDCC3E','#ED993E','#A1673D','#758452','#A0B762','#ACD354','#BDF464','#A5EEA1','#BDE0BA','#A4C6A0','#4D9F49','#63CE7F','#84D8A6','#CC69B2','#CE9BCE','#9483EC','#AF68DD','#383887','#6B609D','#5252BF','#309F9D','#95CCCE','#BCDCE5','#49A3F9','#31C8F9','#30FBF9'];
       for ( var i = 0; i < palette7.length; i++ ) 
	   {
		   $('#p7').append("<div style='display:inline-block;width:10px;height:10px;background-color:"+palette7[i]+"'></div>");
	   }
        $(".palette").click(function(){
            var radioValue = $("input[name='palette']:checked").val();
            if(radioValue){
                changecharttype($('#switchchart').val(),charttitle);
            }
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
	
  