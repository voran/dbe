<?php
$plugin = 'finder';
$action = 'index';

class finder extends dbeinterface implements plugin
{

    public function __construct($table)
    {
        $this -> table = $table;
        $this -> name = 'seeker';
        $this -> action = 'index';
        $this -> author = 'Yavor Stoychev';
        $this -> version = '0.10';
    }

    public function Show()
    {
        $this -> ShowPanel();
    }

    /*///////////////////////////////////////       SELECT  	/////////////////////////////////////////////////////////////*/
    function Select($arrValues, $selectName, $default, $formname, $nullEntry)
    {
        if ($formname != null)
            $output = "<select name='" . $selectName . "' onchange=\"submitform('" . $formname . "')\">";
        else
            $output = "<select name='" . $selectName . "'>";

        if ($default != null)
            $output = "" . $output . "<option value='" . $default . "' selected='selected'>" . $arrValues[$default] . "</option>";
        else
            $output = "" . $output . "<option value=''>" . $nullEntry . "</option>";

        for ($x = 0; $x < count($arrValues); $x++) {

            if ($arrValues[$default] != $arrValues[$x]) {
                $output = "" . $output . "<option value='" . $x . "'>" . $arrValues[$x] . "</option>";
            }

        }
        $output = "" . $output . "</select>";
        return $output;
    }

    /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
    /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

    function ShowPanel()
    {
        for ($x = 0; $x < $this -> table -> fieldcount; $x++) {
            $fieldname[$x] = $this -> table -> field[$x] -> name;

        }

        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='POST' name='search' id='search'>";

        $selection = $this -> Select($fieldname, "searchin", $this -> table -> connection -> searchin, "search", "ALL");

        $limit = $this -> Select(explode(',', LIMIT), "limit", $this -> table -> connection -> limit, "search", "NONE");

        echo $this -> CommonHiddenInputs(0) . $this -> PrimaryKeyInputs(0) . $this -> ForeignKeyInputs(0) . "<div><fieldset>
			<div class='heading'>Search</div>
			<input name='keyword' type='text' size='30' value = '" . $this -> table -> connection -> keyword . "'/> in " . $selection . "  Limit " . $limit . "
			<input type='submit' value='Go!'/></fieldset></div></form>";

    }
}
?>
