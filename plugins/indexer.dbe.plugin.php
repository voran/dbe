<?php


$plugin = 'indexer';
$action = 'index';


class indexer extends dbeinterface implements plugin
{
private $order;




/*////////////////////////////////////////////////////////       LIST		//////////////////////////////////////////////////*/
function __construct($table)
{

	$this->name = 'indexer';
	$this->author = 'Yavor Stoychev';
	$this->version = '0.10';
	$this->table = $table;

	if ($this->table->connection->result_id == null)
	{

		if ($this->table->connection->keyword != null) $query = $this->table->connection->KeywordScan($this->table);
		else $query = $this->table->GenerateSelect();


		if ($this->table->name != null) 
		{	
			$this->table->connection->Execute($this->table->connection->LimitQuery($this->table->connection->OrderQuery($query, $this->table->field)));
		}

	

		$this->table->GetResult();
	}
}


function Show()
{
	$this->ShowControlForms();

	echo "<div><fieldset>";

	if ($this->table->rowcount < 1) echo "<div class='heading'>Note</div>No Results to Display";
		
	else
	{

		echo "<div class='heading'>Displaying ".$this->table->rowcount." results from table '".$this->table->name."'</div><table><tr class='thead' >";
	
		for ($x  = 0; $x < $this->table->fieldcount; $x++)
		{
			$this->ShowColumn($x);
		}

		echo "<td></td><td></td><td></td><td></td></tr>";
			
		$this->ShowData();
		echo "</table>";
	}

	echo "</fieldset></div>";
}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/









/*/////////////////////////////////////////////       SHOW DATA		///////////////////////////////////////////////////////////*/
function ShowData()
{

	$this->GenerateSortButtons();	


	for ($y = 0; $y < $this->table->rowcount; $y++)
	{

	


		echo "<tr>";
		for($x = 0; $x < $this->table->fieldcount; $x++)
		{
			echo "<td>".htmlspecialchars($this->table->field[$x]->value[$y], ENT_QUOTES)."</td>";
		}

		if ($this->table->name != null)
		{		
		 echo "<td><img src='./img/buttons/edit.png' width='16' height='16' title='Edit' alt='Edit' onclick=\"submitform('".$this->table->name."_edit".$y."')\" /></td>
		<td><img src='./img/buttons/del.png' width='16' height='16' title='Delete' alt='Delete' onclick=\"submitform('".$this->table->name."_del".$y."')\" /></td>
		<td><a onclick=\"submitform('".$this->table->name."_view".$y."')\">View</a></td>";
		}


		if ($table_ref != $table)// TODO: REIMPLEMENT
		{
			echo "<form action='".$_SERVER['PHP_SELF']."' method='get' name='Set' id='Set'>";

			echo $this->CommonHiddenInputs().$this->PrimaryKeyInputs($y).$this->ForeignKeyInputs($y);

			echo "<td><input type='submit' value='Set' class='text'/></td></form>";

		}


		else echo "<td></td>";


		echo "</tr>";
				
	}
			
}


/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
}	//EOC
