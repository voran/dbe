<?php

//EXAMPLE:
/*

define('FORMATS', 'XML,CSV,SQL'));			//supported import and export formats 


You are free to modify the "XML,CSV,SQL" section to suit your needs.
DO NOT PUT SPACES AFTER COMMAS IN "XML,CSV,SQL"!
*/


define('DEFAULT_LIMIT', 1);
define('DEFAULT_ORDERBY', 0);
define('DEFAULT_ORDERTYPE', 'ASC');


define('DEFAULT_DBHOST', '127.0.0.1');		//Host address
define('DEFAULT_DBUSER', 'root');		//Username
define('DEFAULT_DBPASS', '');	//Password (Setting this to your ACTUAL PASSWORD is a VERY BAD IDEA!!!)



//color scheme
define('COLOR_ACTIVE', '#0050FF');
define('COLOR_TEXT', '#000000');
define('COLOR_LINK', '#FF0000');
define('COLOR_HOVER', '#d3dce3');
define('COLOR_TABLE', '#e5e5e5');
define('COLOR_HEADING', '#A6B6BA');

define('TITLE', 'DBE');			//Title


define('EDIT_ALIGN', 'Y');	//alignment of add and edit fields: Y for vertical, X for horizontal

$width['input']['X'] = '12';
$width['input']['Y'] = '20';


define('EDIT_FIELD_SIZE', $width['input'][EDIT_ALIGN]); //edit field width



//icon links
define('ICON_ASC', './img/buttons/asc.png');
define('ICON_DESC', './img/buttons/desc.png');
define('ICON_ASC_SELECTED', './img/buttons/asc-selected.png');
define('ICON_DESC_SELECTED', './img/buttons/desc-selected.png');




//$this->box['items_per_row'] = 6;


//maximum number of rows that a referenced table may contain and activate a dropdown list... if more rows are present, a search dialog will be displayed (work in progress)
define('DROPDOWN_MAX_ITEMS', 9);
				
define('AUTOINCREMENT_PRIMARY_COLUMN', false);			

define('UPLOAD_MAX_FILE_SIZE', 1000000);			//size in bytes

define('EDIT_DROPDOWN_EMPTY', '---');				//the empty entry in the dropdown list

///////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// END OF USER OPTIONS  ////////////////////////////// 



///////////////////////////////////////////////////////////////////////////////////
//////////////////////////////// DEVELOPER OPTIONS  ///////////////////////////////
//WARNING!! TAMPERING WITH THOSE SETTINGS IS NOT RECOMMENDED AS IT MAY CAUSE UNEXPECTED BEHAVIOR
//EDIT THE LINES BELOW AT YOUR OWN RISK

define('VERSION', '0.7.5 Beta');		//Version		

define('ACTIONS', 'index->list,edit->insert,query->execute,import->import'); 	//pages must be defined staticallty here, otherwise they WILL NOT appear in the menu

define('FORMATS', 'XML,CSV,SQL');			//supported import and export formats 

define('LIMIT', '10,20,50,100,250,500,1000,5000,10000'); //limit values

define('TEMP_FOLDER', './temp');			//path to writable temp folder

define('DEFAULT_ACTION', 'index');			//default action

define('DEFAULT_DELIMITER', ';');			//default delimiter

///////////////////////////////////////////////////////////////////////////////////
//////////////////////////// END OF DEVELOPER OPTIONS  /////////////////////////

?>
