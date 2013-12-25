<?php
class DBEStyle
{

    public function __construct()
    {

        echo "<style type='text/css'>
    	<!--
    td,th 
    {
    	color: " . COLOR_TEXT . ";
    	font-size: 12px;
    	margin-right:40px;
    }
    
    body 
    {
    	background-color: #f5f5f5;
    	font-size: 12px
    }
    
    
    a:link { 
       color: " . COLOR_ACTIVE . "; 
       text-decoration: none; 
    } 
    a:visited { 
       text-decoration: none; 
       color: " . COLOR_ACTIVE . "; 
       } 
    a:hover { 
       text-decoration: none; 
       color: " . COLOR_LINK . "; 
    } 
    
    a:active { 
       text-decoration: none; 
       color: " . COLOR_ACTIVE . ";
    }
    
    .text:link { 
       color: " . COLOR_ACTIVE . "; 
       text-decoration: none; 
    } 
    .text:visited { 
       text-decoration: none; 
       color: " . COLOR_ACTIVE . "; 
       } 
    .text:hover { 
       text-decoration: none; 
       color: " . COLOR_LINK . "; 
    } 
    
    .text:active { 
       text-decoration: none; 
       color: " . COLOR_ACTIVE . ";
    }
    
    .dbe_hidden_panel
    { 
    	visibiltiy:hidden; 
    }
    
    div
    {
    	margin-bottom:15px;
    	padding:auto;
    	display:table;
    	margin-left:auto;
    	margin-right:auto;
    	text-align:center;
    	background-color: " . COLOR_TABLE . ";
    }
    
    fieldset
    {
    	margin:10px;
    	text-align:left;
    }
    
    
    .ok
    {
    	border-color:#00FF00;
    }
    
    
    .warning
    {
    	border-color:#FFA200;
    }
    
    .error
    {
    	border-color:#FF0000;
    }
    
    
    .button
    {
    	float:right;
    }
    
    div .heading
    {
    	width:100%;
    	text-align:left;
    	background-color:  " . COLOR_HEADING . ";	
    
    }

    
    .cloud
    {
    	text-align:center;
    	margin:10px;
    	word-spacing:20px;
    }
    
    .centered
    {
    	text-align:center;
    }
    
    span
    {
    	margin:20px;
    }
    
    
    table
    {
    	width:100%;
    	margin-left:auto;
    	margin-right:auto;
    	white-space:nowrap;
    	background-color: " . COLOR_TABLE . "
    	border: none;
    	text-align: left;
    }
    
    tr
    {
    	white-space:nowrap;
    	background-color:" . COLOR_TABLE . ";
    	height: 12px;
    	
    }
    
    .thead
    {
    	font-weight:bold;
    }
    
    .thead td:hover
    {
    	background-color:" . COLOR_TABLE . ";
    }
    
    tr:hover
    {
    	background-color:" . COLOR_HOVER . ";
    }
    -->
    	</style>";
    }

}
?>