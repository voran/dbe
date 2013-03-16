<?php

class table extends component
{

	public $field;
	public $fieldcount;
	public $rowcount;
	public $connection;
	public $dbindex;
	public $pnum;
	public $fnum;



public function __construct($dbindex, $tableindex, $tablename, $connection)
{
	$this->pnum = array();
	$this->fnum = array();
	$this->parent = array();
	$this->child = array();
	$this->type = 'Tables';
	$this->connection = $connection;
	$this->dbindex = $dbindex;
	$this->name = $tablename;
	$this->index = $tableindex;
	
	if ($this->name != null) $this->GetFields();
}


public function CustomResult()
{
	$this->field = null;

	for ($x = 0; $x < $this->connection->NumFields(); $x++)
	{
		$this->field[$x] = new field($this->connection->FetchField($x), $x);	
	}
	$this->fieldcount = $x;
	
	$this->GetResult();

}

public function GetFields() 
{
	$this->field = null;

	$nullable_array = array();
	$primary_array = array();

	$nullable_array  = $this->connection->GetNullable($this->name);
	$primary_array = $this->connection->GetPrimary($this->name);

	if ($nullable_array == null) $nullable_array = array();
	if ($primary_array == null) $primary_array = array();


	$this->connection->Execute($this->connection->GenerateDefault($this->name));

	for ($x = 0; $x < $this->connection->NumFields(); $x++)
	{
		$this->field[$x] = new field($this->connection->FetchField($x), $x);
		if (in_array($this->field[$x]->name, $primary_array)) $this->field[$x]->primary = true;
		if (count($nullable_array) > 0 && !in_array($this->field[$x]->name, $nullable_array)) $this->field[$x]->nullable = true;
	}
	
	$this->fieldcount = $x;





	$p = 0;
	for ($x = 0; $x < $this->fieldcount; $x++)
	{
		if ($this->field[$x]->primary == true) $this->pnum[$p++] = $this->field[$x];
	}



	if ($p == 0)
	{
		for ($x = 0; $x < $this->fieldcount; $x++)
		{
			$this->field[$x]->primary = true;	//if no primary keys - make all primary :)
			$this->pnum[$x] = $this->field[$x];
		}
	}	
}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/ 





/*/////////////////////////////////////////////////      GET RESULT		/////////////////////////////////////////////////////*/
public function GetResult() 
{
	$this->ClearResult();

	$a = 0;

	if ($this->fieldcount > 0)
	{		
		while ($re = $this->connection->FetchRow())
		{
					
			for ($b = 0; $b < $this->fieldcount; $b++)
			{
				$this->field[$b]->value[$a] = $re[$b];		
			}	
			$a++;
		}
	}

	$this->UpdateConstraintValues();

	$this->rowcount = $a;
	
} 

/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/ 


public function ClearResult()
{
	for ($b = 0; $b < $this->fieldcount; $b++)
	{
			$this->field[$b]->value = null;		
	}

	$this->rowcount = 0;
}




public function GetValues()
{
	for ($x = 0; $x < $this->fieldcount; $x++)
	{
		if ($_REQUEST[$x] != null) $this->field[$x]->value[0] = htmlspecialchars($_REQUEST[$x], ENT_QUOTES);
		if ($_REQUEST[$x] == 'NULL') $this->field[$x]->value[0] = null;		
	}

	for ($x = 0; $x < count($this->pnum); $x++)
	{
		if ($_REQUEST["id".$x] != null) $this->pnum[$x]->constraint_value[0] = htmlspecialchars($_REQUEST["id".$x], ENT_QUOTES);
		if ($_REQUEST["id".$x] == 'NULL') $this->pnum[$x]->constraint_value[0] = null;
		
	}

	$this->rowcount = count($this->field[0]->value);
}


public function UpdateConstraintValues()
{
	for ($x = 0; $x < count($this->pnum); $x++)
	{
		$this->pnum[$x]->constraint_value = $this->pnum[$x]->value;		
	}
}


public function FetchField($name)
{
	for ($x = 0; $x < $this->fieldcount; $x++)
	{
		if ($this->field[$x]->name == $name)	{	return $this->field[$x];		}
	}
}

public function FetchFieldNames()
{
	for ($x = 0; $x < $this->fieldcount; $x++)
	{
		$names[$x] = $this->field[$x]->name;		
	}
	return $names;
}


public function FetchFieldValues()
{
			
	for ($x = 0; $x < $this->fieldcount; $x++)
	{
		for ($y = 0; $y < $this->rowcount; $y++)
		{
			$values[$y][$x] = $this->field[$x]->value[$y];
		}		
	}
	return $values;
}




/*//////////////////////////////////////				 CREATE SELECT QUERY    	///////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function GenerateSelect() 
{

	if ($this->name != null)	return "SELECT * FROM ".$this->connection->EncapsulateField($this->name);
	else return false;
}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


/*//////////////////////////////////////	 CREATE CONDITIONAL SELECT QUERY    	////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function GenerateConditionalSelect() 
{
	return $this->GenerateSelect().$this->GeneratePrimaryKeyCondition();

}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


/*//////////////////////////////////////	 CREATE CONDITIONAL SELECT QUERY    	////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function GenerateCustomSelect($x, $value) 
{
	return $this->GenerateSelect().$this->GenerateCustomCondition($x, $value);

}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

/*//////////////////////////////////////	 CREATE INSERT QUERY    	///////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function GenerateInsert() 
{
	
	if ($this->fieldcount * $this->rowcount > 0)
	{
		
		for ($x = 0; $x < $this->fieldcount; $x++)
		{
			if ($x == 0) $query = "INSERT INTO ".$this->name." (";
			else $query = $query.", ";

			$query = $query.$this->connection->EncapsulateField($this->field[$x]->name);
		}


		

		for($y = 0; $y < $this->rowcount; $y++)
		{
			if ($y == 0) $query = "".$query.") VALUES ";
			else $query = $query.", ";

			for ($x = 0; $x < $this->fieldcount; $x++)
			{
				if ($x == 0) $query = $query."(";
				else $query = "".$query.", ";

				$query = "".$query.$this->connection->EncapsulateData($this->field[$x]->value[$y]);
			}
	
			$query = $query.")";
		}		
		
	}
		
	return $query;
		
}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


/*//////////////////////////////////////		 CREATE UPDATE QUERY    	////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function GenerateUpdate() 
{
	$query = "UPDATE ".$this->connection->EncapsulateField($this->name)." SET ";

	for( $x = 0; $x < $this->fieldcount; $x++)
	{
		if ($x != 0) $query = "".$query.", ";
		$query = $query.$this->connection->EncapsulateField($this->field[$x]->name)." = ".$this->connection->EncapsulateData($this->field[$x]->value[0])."";
	}
			
	return $query.$this->GeneratePrimaryKeyCondition();
		
}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


/*//////////////////////////////////////	 CREATE DELETE QUERY    	///////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function GenerateDelete() 
{
	if ($this->name != null)	return "DELETE FROM ".$this->connection->EncapsulateField($this->name).$this->GeneratePrimaryKeyCondition();
	else return null;

}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/



public function GeneratePrimaryKeyCondition()
{
	
	for( $x = 0; $x < count($this->pnum); $x++)
	{
		if ($x == 0) $query = " WHERE ";
		if ($x != 0) $query = $query." AND ";
		$query = $query.$this->connection->EncapsulateField($this->pnum[$x]->name)." = ".$this->connection->EncapsulateData($this->pnum[$x]->constraint_value[0])."";
	}
	return $query;
}

public function GenerateCustomCondition($x, $value)
{
	return " WHERE ".$this->connection->EncapsulateField($this->field[$x]->name)." = ".$this->connection->EncapsulateData($value)."";
}



public function GenerateForeignKeyCondition()
{
	

	for( $x = 0; $x < count($this->fnum); $x++)
	{
		if ($x == 0) $query = " WHERE ";
		if ($x != 0) $query = $query." AND ";
		$query = $query.$this->connection->EncapsulateField($this->fnum[$x]->name)." = ".$this->connection->EncapsulateData($this->fnum[$x]->value[0])."";
	}
	return $query;
}

	public function __get($data)			{ return $this->$data;	}
	public function __set($data, $value)	{$this->$data = $value;	}
		
}
