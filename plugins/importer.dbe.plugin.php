<?php


$plugin = 'importer';
$action = 'import';


class importer extends dbeinterface implements plugin
{
private $handle;
private $path;
public $format;
public $delimiter;


public function __construct($table)
{
	$this->name = 'importer';
	$this->version = '0.10';
	$this->author = 'Yavor Stoychev';
	$this->table = $table;
	$this->action = htmlspecialchars($_REQUEST['action'], ENT_QUOTES);
}



public function Show()
{
	

	$handle = @fopen(TEMP_FOLDER."/.test", w);

	if(!$handle)
	{
		$error = new  error("DBE cannot write into temp folder. File functions disabled");
	}

	else 
	{
		
		fclose($handle);
		unlink(TEMP_FOLDER.'/.test');
		$this->Initialize();

	}
}




public function Initialize()
{

	$this->table->ClearResult();

	$this->format = htmlspecialchars($_REQUEST['format'], ENT_QUOTES);
	$this->delimiter = htmlspecialchars($_REQUEST['delimiter'], ENT_QUOTES);
	if ($this->delimiter == null) $this->delimiter = DEFAULT_DELIMITER;

	
	echo "<div><fieldset><form action='".$_SERVER['PHP_SELF']."' method='post' name='format' id='format'>
	<div class='heading'>Import data to ".$this->table->name."</div>
	<fieldset><legend>Format</legend><p class='centered'>".$this->ShowFormatOptions()."</p>".$this->CommonHiddenInputs()."</fieldset></form>";
		
	if ($this->format != null) $this->ShowAdditionalOptions();

	
	echo "</fieldset></div>";

	if ($_FILES['file']['size'] <= UPLOAD_MAX_FILE_SIZE)
	{
		if ($_FILES['file']['tmp_name'] != $empty)
		{	
			$this->path = "temp/";
			$this->path = $this->path . basename( $_FILES['file']['name']);

			if(move_uploaded_file($_FILES['file']['tmp_name'], $this->path)) 
			{
				$this->handle = fopen($this->path, 'r');
			}

			else $error = new error("Error opening file!");
				

				if ($this->format == 'SQL')	$status = $this->ImportSQL();
					
				else if ($this->format == 'CSV') $status = $this->ImportCSV();

				else if ($this->format == 'XML') $status = $this->ImportXML();

				if ($status == true)
				{


					$this->table->connection->ExecuteSQLSequence($this->query);

					
					if ($this->table->connection->result_id !== true && $this->table->connection->result_id !== false) 
					{
						
						$this->table->CustomResult();
						$list = new indexer($this->table);
						$list->Show();
					}

					else if ($this->table->connection->result_id == true)	echo "<div><fieldset class='ok'>
					<legend>Note</legend><p class='centered'>Executed Successfully</p></fieldset></div>";

				

				}
	
				else echo "<div><fieldset class='error'>
					<legend>Error</legend><p class='centered'>Document parsing failed!</p></fieldset></div>";
			}

	}
	else $error = new error("Maximum allowed size is ".UPLOAD_MAX_FILE_SIZE." bytes!</p>Current file size is ".$_FILES['file']['size']."");
}

public function FileInput()
{
	echo "Select file: <input name='file' type='file' />";
}




/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
public function ImportXML() 
	{
		$xml = null;
		$data = null;
		
		$rowcount = 0;


		$file = new DOMDocument('1.0', 'iso-8859-1');

		$file->formatOutput = true;
		$result = @$file->load($this->path);

		if ($result == true)
		{
			$xpath = new DOMXPath($file);
	
			$globalnodelist = $xpath->query("//*");
						
			$root = $globalnodelist->item(0); // Database
							 
			$xml['nodelist']['table'] = $root->childNodes;
					 
			
			for ($y = 0; $y < $xml['nodelist']['table']->length; $y++)
			{
								
				$domnode = $xml['nodelist']['table']->item($y);
				$parent = $domnode;
				$domnode = $domnode->firstChild;
				$myname = $domnode->nodeName;
	
				$x = 1;
				$colcount = 0;

				while (!is_null($domnode)) 
				{
					switch ($domnode->nodeType) 
					{
						case XML_ELEMENT_NODE: 
						{
							if ( $domnode->hasChildNodes() && $domnode->firstChild->nodeType==XML_TEXT_NODE) 
							{
								$this->table->field[$colcount]->name = $domnode->nodeName;
								$this->table->field[$colcount]->value[0] = $domnode->firstChild->nodeValue;
								$this->table->rowcount = 1;
										
								$colcount++;
							} 
						
						
							break;
						}
					}
				
			

					$domnode = $domnode->nextSibling;

					if($domnode->nodeName == $myname)	$domnode->nodeName.=($x++);   
					else 	$myname = $domnode->nodeName;
		
				}

				$this->table->fieldcount = $colcount;
				$insert = $this->table->GenerateInsert();
				if ($insert != null) $this->query = $this->query."; ".$insert;

				if ($this->table->field[$colcount - 1] != null)	$rowcount++;
			}



			$this->table->rowcount = $rowcount;
			$this->table->fieldcount = $colcount;
			
			return true;
	}

	else return false;
	}
 /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

public function ImportCSV()
{
	$fields = @fgetcsv($this->handle, $empty, $this->delimiter);
	
		
	$y = 0;				
	while ($resultlist = @fgetcsv($this->handle, $empty, $this->delimiter))
	{
		
		for ($x = 0; $x < $this->table->fieldcount; $x++)				
		{
			$this->table->field[$x]->value[$y] = $resultlist[$x];	
			$this->table->field[$x]->name = $fields[$x];
		}
	$y++;
	}
		


	$this->table->fieldcount = $x;
	$this->table->rowcount = $y;

	if ($fields == null) return false;
	
	$this->query = $this->table->GenerateInsert();

	return true;		

}

public function ImportSQL()
{
	$this->query = @fread($this->handle, filesize($this->path));
	if ($this->query == false) return false;
	else return true;
}

	

}//EOC


