<?php

class OracleExplorer extends connection
{
	
public function __construct()	
{

}
/*//////////////////////////////////////////////       ALIASES		/////////////////////////////////////////////////////////*/
public function Connect($server, $user, $password) 	{	$this->link = @oci_connect($server, $user, $password);		}
public function FetchRow() 		     		{	return oci_fetch_row($this->result_id);			}
public function NumFields()				{	return oci_num_fields($this->result_id);			}
public function Result($i)				{	return oci_result($this->result_id, $i);			}
public function SelectDatabase($db)			{	return null;							}
public function SelectDatabaseSchema()			{	return null;							}
public function GetMax($table)				{	return $this->Execute("SELECT MAX(`".$table->pnum[0]->name."`) FROM `".$table->name."`");	}
public function ServerError($empty)			{	$err = oci_error($resource);	 return $err['message'];	}
public function ConnectionError()			{	$err = oci_error();	 	 return $err['message'];	}
public function EscapeString($string)			{	return str_replace("'", "''", $string);					}//TODO: TEST
public function EncapsulateField($string)		{	return $strung;						}




public function EncapsulateData($data)		
{	
	if ($data == null || $data == "") return "NULL";
	else return "'".$this->EscapeString($data)."'";//TODO: TEST
						
}


public function Execute($query)                                                           
{
	if ($query != $empty)
	{
		$result = oci_parse($this->link, $query);
		
		$exec = @oci_execute($stmnt, OCI_COMMIT_ON_SUCCESS); // Perform Query


		// Debug
		if ($result == false) 
		{
			$message  = '<p/>Invalid query: ' . $this->ServerError($empty) . "\n";
			$message .= '<p/>Whole query: ' . $query;
			$this->error = new DBEErrorPanel($message);
		}
			
	}
		
	if ($exec != null) $this->result_id = $result;
	else $this->result_id = true;
}
	
	
	function GenerateDefault($tablename)                                                           
	{
		return "select * from (SELECT * FROM ".$tablename." ) WHERE ROWNUM <= 1";
	}

	function OrderQuery($query, $column)
	{
		return $query." ORDER BY ".$column[$this->orderby]->name." ".$this->ordertype;
	}
	
	
	function LimitQuery($query)                                                           
	{
		$limit = explode(',',LIMIT);
		return "select * from (".$query.") WHERE ROWNUM <= ".$limit[$this->limit];
	}
	
	function DatabaseScan()
	{
		return null;
	}
	
	
	
	function TableScan($dbname)
	{
		return "SELECT TABLE_NAME FROM USER_TABLES";
	}

public function RelationScan($dbname, $tablename)
{
	return "SELECT PK.TABLE_NAME as PK_TABLE, FK.TABLE_NAME as FK_TABLE, PK.COLUMN_NAME as PK_COLUMN, FK.COLUMN_NAME as FK_COLUMN 
		FROM SYS.ALL_CONS_COLUMNS PK 
		JOIN SYS.ALL_CONSTRAINTS A on A.R_CONSTRAINT_NAME  = PK.CONSTRAINT_NAME 
		JOIN SYS.ALL_CONS_COLUMNS FK ON FK.CONSTRAINT_NAME = A.CONSTRAINT_NAME
			WHERE (PK.TABLE_NAME='".$tablename."' OR FK.TABLE_NAME = '".$tablename."') AND CONSTRAINT_TYPE in ('R', 'U')";

}
		


public function GetPrimary($tablename)
{
	$this->Execute("SELECT A.COLUMN_NAME FROM ALL_CONS_COLUMNS A JOIN ALL_CONSTRAINTS C  ON A.CONSTRAINT_NAME = C.CONSTRAINT_NAME WHERE C.TABLE_NAME = '".$tablename."' AND C.CONSTRAINT_TYPE = 'P'");

	while ($row = $this->FetchRow())
	{
		$primary[++$i] = $row[0];
	}

	return $primary;

}


public function GetNullable($tablename)
{
	$this->Execute("SELECT column_name FROM user_tab_columns WHERE table_name = '".$tablename."' AND nullable = 'N'");

	while ($row = $this->FetchRow())
	{
		$nullable[++$i] = $row[0];
	}

	return $nullable;
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

public function FetchField($i)	//TODO: IMPLEMENT
{	
	$field = new stdClass;

	$field->name = oci_field_name($this->result_id, $i + 1);
		
	$field->type = oci_field_type($this->result_id, $i + 1);

	$field->length = oci_field_size($this->result_id, $i + 1);


	return $field;
			

}
}//EOC
