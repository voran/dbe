<?php


class PostgresExplorer extends connection
{
public $escapechars;

	
	public function __construct()	
	{
		$this->admin_username = 'postgres';

	}
/*//////////////////////////////////////////////       ALIASES		/////////////////////////////////////////////////////////*/
public function Connect($server, $user, $password) 	{	$this->link = @pg_connect("host=".$server."  user=".$user." password=".$password."");	}
public function FetchRow() 		     		{	return pg_fetch_row($this->result_id);			}
public function NumFields()				{	return pg_num_fields($this->result_id);			}
public function Result($i)				{	return pg_result($this->result_id, $i);				}
public function SelectDatabase($db)			
{	$this->link = @pg_connect("host=".$_SESSION['dbhost']." user=".$_SESSION['dbuser']." password=". $_SESSION['dbpass']." dbname=".$db);		}
public function SelectDatabaseSchema()			{	return null;						}
public function GetMax($table)				{	$this->Execute("SELECT MAX(\"".$table->field->pnum[0]->name."\") FROM \"".$table->name."\"");	}
public function ServerError($empty)			{	return pg_last_error();						}
public function ConnectionError()			{	return null;						}
public function EscapeString($string)			{	return pg_escape_string($string);	}	//REQUIRES POSTGRES 7.2 or later

public function EncapsulateField($string)		{	return "\"".$string."\"";					}




public function EncapsulateData($data)		
{	
	if ($data == null || $data == "") return "NULL";
	else return "'".$this->EscapeString($data)."'";
						
}


public function Execute($query)                                                           
	{
	if ($query != null)
	
		{
			$this->result_id = @pg_query($query); // Perform Query

			
			//Debug
			if ($this->result_id === false) 
				{
					$message  = '<p/>Invalid query: ' . $this->ServerError(null) . "\n";
					$message .= '<p/>Whole query: ' . $query;
					$this->error = new error($message);
				}
		}
	else $this->result_id =  true;
	}
	
	
	function GenerateDefault($tablename)                                                           
	{
		return 'SELECT * FROM '.$this->EncapsulateField($tablename).' LIMIT 1 OFFSET 0';
	}

	function OrderQuery($query, $column)
	{
		return $query." ORDER BY ".$column[$this->orderby]->name." ".$this->ordertype;
	}
	
	
	function LimitQuery($query)                                                           
	{
		$limit = explode(',',LIMIT);
		return $query." LIMIT ".$limit[$this->limit]." OFFSET 0";
	}
	
	function DatabaseScan()
	{
		return "SELECT datname FROM pg_database WHERE datname <> 'template0' AND datname <> 'template1'";
	}
	
	
	
	function TableScan($dbname)
	{
		return "SELECT tablename FROM pg_tables where tablename !~ '^pg_+' and tableowner='".$_SESSION['dbuser']."' and schemaname='public'";
	}

public function RelationScan($dbname, $tablename)
{
	return	'SELECT ccu.table_name AS PK_TABLE, tc.table_name AS FK_TABLE, ccu.column_name AS PK_COLUMN, kcu.column_name AS FK_COLUMN, tc.constraint_name AS CONSTRAINT_NAME			
FROM 
    information_schema.table_constraints AS tc 
    JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
    JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
WHERE tc.table_name = \''.$tablename.'\' AND constraint_type = \'FOREIGN KEY\' AND tc.table_catalog = \''.$dbname.'\'';


}
		
function GetNullable($tablename)
{
	$this->Execute("SELECT column_name FROM information_schema.columns WHERE information_schema.columns.is_nullable <> 'YES' AND information_schema.columns.table_name='".$tablename."'");


	while ($row = $this->FetchRow())
	{
		$nullable[++$i] = $row[0];
	}

	return $nullable;

}



function GetPrimary($tablename)
{
	$this->Execute("SELECT k.column_name from pg_constraint c, information_schema.key_column_usage k
	WHERE c.conname = k.constraint_name
	and k.table_name = '".$tablename."' and c.contype = 'p'");

	while ($row = $this->FetchRow())
	{
		$primary[++$i] = $row[0];
	}

	return $primary;

}
	
	
	
public function KeywordScan($table)
{


	$query = "SELECT DISTINCT * FROM ".$this->EncapsulateField($table->name)." WHERE ";

	if ($this->searchin == null)
	{
		
		
		for($x = 0; $x < $table->fieldcount; $x++)
		{	
			if ($x != 0) $query = $query." OR ";
			$query = $query."CAST(".$this->EncapsulateField($table->field[$x]->name)." AS TEXT) LIKE ('%".$this->keyword."%')";			
		}
	}
	else	
	{
		$query = $query."CAST(".$this->EncapsulateField($table->field[$this->searchin]->name)." AS TEXT) LIKE ('%".$this->keyword."%')";
	}
			
	return $query;
}

	public function FetchField($i)	
	{

		$field->name = pg_field_name($this->result_id, $i);
		$field->type = pg_field_type($this->result_id, $i);
		$field->size = pg_field_size($this->result_id, $i);
		$field->nullable = false;
			

		return $field;	

			

	}
}
?>
