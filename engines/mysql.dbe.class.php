<?php

class MysqlExplorer extends connection
{
	public $escapechars;

	public $schema;
	
	
	

	
	
public function __construct()	
{
	$this->schema = 'information_schema';
	$this->exclude_tables = array('PROFILING');
	$this->admin_username = 'root';

}
/*//////////////////////////////////////////////       ALIASES		/////////////////////////////////////////////////////////*/
public function Connect($server, $user, $password) 	{	$this->link = @mysql_connect($server, $user, $password);		}
public function FetchRow() 		     		{	return @mysql_fetch_row($this->result_id);			}
public function NumFields()				{	return @mysql_num_fields($this->result_id);			}
public function SelectDatabase($db)			{	return @mysql_select_db($db, $this->link);			}
public function SelectDatabaseSchema()			{	return @mysql_select_db($this->schema, $this->link);		}
public function GetMax($table)				{	return $this->Execute("SELECT MAX(`".$table->pnum[0]->name."`) FROM `".$this->table->name."`");	}
public function ServerError($empty)			{	return mysql_error();						}
public function ConnectionError()			{	return mysql_error();						}
public function GetPrimary()				{	return null;							}
public function GetNullable()				{	return null;							}
public function EscapeString($string)			{	return mysql_real_escape_string($string);			}
public function EncapsulateField($string)		{	return "`".$string."`";						}




public function EncapsulateData($data)		
{	
	if ($data == null || $data == "") return "NULL";
	else return "'".$this->EscapeString($data)."'";
						
}


public function Execute($query)                                                           
	{
	if ($query != $empty)
	
		{
			$this->result_id = @mysql_query($query); // Perform Query

			
			//Debug
			if ($this->result_id === false) 
				{
					$message  = '<p/>Invalid query: ' . $this->ServerError($empty) . "\n";
					$message .= '<p/>Whole query: ' . $query;
					$this->error = new error($message);

				}
		}
	else $this->result_id =  true;
	}
	
	
	function GenerateDefault($tablename)                                                           
	{
		return "SELECT * FROM `".$tablename."` LIMIT 0, 1";
	}

	function OrderQuery($query, $column)
	{
		return $query." ORDER BY ".$column[$this->orderby]->name." ".$this->ordertype;
	}
	
	
	function LimitQuery($query)                                                           
	{
		$limit = explode(',',LIMIT);
		return $query." LIMIT 0, ".$limit[$this->limit];
	}
	
	function DatabaseScan()
	{
		return "SHOW DATABASES";
	}
	
	
	
	function TableScan($dbname)
	{
		return "SHOW TABLES FROM `".$dbname."`";
	}

public function RelationScan($dbname, $tablename)
{
	return "SELECT DISTINCT `KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME` , `KEY_COLUMN_USAGE`.`TABLE_NAME`, `KEY_COLUMN_USAGE`.`REFERENCED_COLUMN_NAME`, `KEY_COLUMN_USAGE`.`COLUMN_NAME`, `TABLE_CONSTRAINTS`.`CONSTRAINT_NAME`
	FROM `KEY_COLUMN_USAGE` , `TABLE_CONSTRAINTS`
	WHERE ( `TABLE_CONSTRAINTS`.`TABLE_NAME` = '".$tablename."' OR `KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME` = '".$tablename."')
	AND `KEY_COLUMN_USAGE`.`CONSTRAINT_SCHEMA` = '".$dbname."'
	AND `TABLE_CONSTRAINTS`.`CONSTRAINT_NAME` = `KEY_COLUMN_USAGE`.`CONSTRAINT_NAME` AND `KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME` IS  NOT NULL";

}
		
	
	
	
public function KeywordScan($table)
{
	
	$query = "SELECT DISTINCT * FROM ".$this->EncapsulateField($table->name)." WHERE ";

	if ($this->searchin == null)
	{	
		for($x = 0; $x < $table->fieldcount; $x++)
		{	
			if ($x != 0) $query = $query." OR ";
			$query = $query.$this->EncapsulateField($table->field[$x]->name)." LIKE '%".$this->keyword."%'";			
		}
	}
	else	
	{
		$query = $query.$this->EncapsulateField($table->field[$this->searchin]->name)." LIKE '%".$this->keyword."%'";
	}

	return $query;
}

	public function FetchField($i)	
		{	
			
			$fieldobject =  mysql_fetch_field($this->result_id, $i);


			$field->name = $fieldobject->name;
		
			$field->type = $fieldobject->type;

			$field->length = $fieldobject->max_length;

			$field->unique = $fieldobject->unique_key;
			
			if ($fieldobject->not_null == 1) $field->nullable = false;
			else $field->nullable = true;

			if ($fieldobject->primary_key == 1) $field->primary = true;
			else $field->primary = false;

			return $field;
			

		}
}
