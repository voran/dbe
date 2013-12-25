<?php

//LOADER
function __autoload($class)
{
	//echo "autoload...";
	require_once './classes/'.$class.'.dbe.class.php';
}


//CONFIGURATION
require_once "./config/dbe.cfg.php";

//MAIN CLASS
require_once "./dbe.class.php";



//ENGINES
require_once "./engines/mysql.dbe.class.php";
require_once "./engines/postgres.dbe.class.php";
require_once "./engines/mssql.dbe.class.php";
require_once "./engines/oracle.dbe.class.php";
require_once "./engines/firebird.dbe.class.php";
require_once "./engines/sqlite.dbe.class.php";

//STYLES
require_once "./style/style.dbe.class.php";
?>