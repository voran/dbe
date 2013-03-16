<?php

$plugin = 'editor';
$action = 'edit';


class editor extends dbeinterface implements plugin
{

private $procedure;


 public function __construct($table)
{

	$this->table = $table;
	$this->name = 'editor';
	$this->version = '0.1';
	$this->author = 'Yavor Stoychev';
	$this->action = "edit";



	//$this->DBE->db->GetRelations(); // this will be done automatically when a table object is constructed

	$this->table->GetValues();

	$this->table->connection->Execute($this->table->GenerateConditionalSelect());

	//echo $this->table->GenerateConditionalSelect();//ДЕБЪГ

	$this->table->GetResult();

	$this->table->GetValues();

	if ($this->table->rowcount > 0) $this->procedure = 'update';
	else $this->procedure = 'insert';

}

public function Show()
{	
	if ($this->table->fieldcount > 0)
	{
		$this->ShowDropdownSelects();

		echo "<div><fieldset><div class='heading'>Update</div><form name='update' action='./' method='post'>
		".$this->CommonHiddenInputs('query', $this->procedure).$this->PrimaryKeyInputs(0);
		echo "<table>";

		if (EDIT_ALIGN == 'Y')
		{

			for ($x = 0; $x < $this->table->fieldcount; $x++)
			{
				echo "<tr>";
				$this->ShowColumn($x);
				echo"<td class='dbe_line'>".$this->ShowInput($x)."</td>";
				echo "</tr>";
			}

		}
	
		if (EDIT_ALIGN == 'X')
		{

			echo "<tr>";
			for ($x = 0; $x < $this->table->fieldcount; $x++)
			{
			$this->ShowColumn($x);
						
			}
					
			echo "</tr><tr>";	
				
			for ($x = 0; $x < $this->table->fieldcount; $x++)
			{
				echo"<td class='dbe_line'>".$this->ShowInput($x)."</td>";
						
			}
			echo "</tr>";
		}
		echo "</table><input class='button' type='submit' value='Update'/></form></fieldset></div>";
	}
}


public function Override()
{
		
}

	
	


public function IncrementPrimary()
{
	if (ALLOW_INCREMENT === true && AUTOINCREMENT_PRIMARY_COLUMN === true)
	{
		//$max = dbe_fetch_row(dbe_get_max($this->DBE->table, $this->DBE->pkey['current']['field'][0]));
 		//$this->DBE->table->fieldset[0][$this->pnum[0]] = $max[0] + 1;
	}
}
		


/*//////////////////////////////////////////////       SELECT ROW		/////////////////////////////////////////////////////////*/
function SelectRow($x, $a)
{	

	$output = "<select name='".$this->table->fnum[$x]->index."' onchange=\"submitform('dropdown".$x."')\">";

	if ($this->table->fnum[$x]->nullable == true && $this->table->fnum[$x]->value[0] == null)
	{		
		$output .= "<option value='NULL' selected='selected'>".EDIT_DROPDOWN_EMPTY."</option>";
	}

	else if ($this->table->fnum[$x]->value[0] != null)
	{
		$output .= "<option value='".$this->table->fnum[$x]->value[0]."' selected='selected'>".$this->table->fnum[$x]->value[0]."</option>";


		if ($this->table->fnum[$x]->nullable == true)
		{		
			$output .= "<option value='NULL'>".EDIT_DROPDOWN_EMPTY."</option>";
		}
	}

		
	for ($y = 0; $y < $this->table->parent[$x]->rowcount; $y++)
	{
		$row = $this->table->parent[$x]->pnum[$a]->value[$y];
		if ( $this->table->fnum[$x]->value[0] != $row)	$output .= "<option value='".$row."'>".$row."</option>";

	}
	$output .= "</select>";
		
	return $output;
}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/



//////////////////////////////////////////////       SELECT ROW		/////////////////////////////////////////////////////////*/
function ShowDropdownSelects()
{

	for ($x = 0; $x < count($this->table->parent); $x++)
	{
		if ($this->table->parent[$x] != $this->table->parent[$x-1]) $a = 0;
		else $a++;


		$this->table->parent[$x]->GetFields();
		$this->table->parent[$x]->connection->Execute($this->table->parent[$x]->GenerateSelect());
		$this->table->parent[$x]->GetResult();

		if ($this->procedure == 'insert' && $this->table->fnum[$x]->nullable == false && $this->table->fnum[$x]->value[0] == null)
		{
			$this->table->fnum[$x]->value[0] = $this->table->parent[$x]->pnum[$a]->value[0];
		}
			
		echo "<div><fieldset><form name='dropdown".$x."' method='post'>
		".$this->CommonHiddenInputs().$this->PrimaryKeyInputs(0).$this->ForeignKeyInputs(0).$this->SelectRow($x, $a)."</form>";

		

		$this->table->connection->Execute($this->table->parent[$x]->GenerateCustomSelect($this->table->fnum[$x]->parent[$a]->index, $this->table->fnum[$x]->value[0]));
		
		$this->table->parent[$x]->GetResult();


		$list = new indexer($this->table->parent[$x]);
		$list->Show();
		echo "</fieldset></div>";

	}


		
}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


}//EOC
?>
