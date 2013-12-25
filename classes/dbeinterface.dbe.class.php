<?php

class dbeinterface
{

    public $action;

    protected $currentView;
    protected $message;

    protected $max;
    protected $error;

    public $table;

    public $asc;
    public $desc;

    //PLUGIN VARS
    public $version;
    public $author;
    public $website;
    public $description;

    //END PLUGIN VARS

    public function CommonHiddenInputs($action = 'default', $query = null)
    {

        if ($action == 'default')
            $action = $this -> action;

        $inputs = "<input type='hidden' name='db' value='" . $this -> table -> dbindex . "'/>
	<input type='hidden' name='table' value='" . $this -> table -> index . "'/>
	<input type='hidden' name='keyword' value='" . $this -> table -> connection -> keyword . "'/>
	<input type='hidden' name='limit' value='" . $this -> table -> connection -> limit . "'/>
	<input type='hidden' name='orderby' value='" . $this -> table -> connection -> orderby . "'/>
	<input type='hidden' name='ordertype' value='" . $this -> table -> connection -> ordertype . "'/>
	<input type='hidden' name='action' value='" . $action . "'/>";

        if ($query != null)
            $inputs .= "<input type='hidden' name='query' value='" . $query . "'/>";

        return $inputs;
    }

    public function PrimaryKeyInputs($y) {
        for ($i = 0; $i < count($this -> table -> pnum); $i++) {
            $in = $in . "<input type='hidden' name='id" . $i . "' value=\"" . htmlspecialchars($this -> table -> pnum[$i] -> constraint_value[$y], ENT_QUOTES) . "\"/>";
        }
        return $in;
    }

    public function ForeignKeyInputs($y) {
        for ($i = 0; $i < count($this -> table -> fnum); $i++) {
            $in = $in . "<input type='hidden' name='" . $this -> table -> fnum[$i] -> index . "' value=\"" . htmlspecialchars($this -> table -> fnum[$i] -> value[$y], ENT_QUOTES) . "\"/>";
        }
        return $in;
    }

    /*//////////////////////////////////////////////       SHOW COLUMNS		/////////////////////////////////////////////////////////*/
    function ShowColumn($x) {

        if ($this -> table -> rowcount > 1) {
            $this -> GenerateSortButtons();
            echo "<td class='dbe_line_selected'>" . $this -> table -> field[$x] -> name . " " . $this -> asc[$x] . $this -> desc[$x] . "</td>";

        } else {
            echo "<td class='dbe_line_selected'>" . $this -> table -> field[$x] -> name . " </td>";
        }

    }

    /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
    /*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

    public function GenerateSortButtons() {
        for ($x = 0; $x < $this -> table -> fieldcount; $x++) {

            if ($this -> table -> connection -> ordertype == 'ASC' && $this -> table -> connection -> orderby == $x)
                $asc = ICON_ASC_SELECTED;
            else
                $asc = ICON_ASC;
            if ($this -> table -> connection -> ordertype == 'DESC' && $this -> table -> connection -> orderby == $x)
                $desc = ICON_DESC_SELECTED;
            else
                $desc = ICON_DESC;

            $this -> asc[$x] = "<input type='image' src='" . $asc . "' onclick=\"submitform('" . $this -> table -> name . "_SetAsc" . $x . "')\" />";

            $this -> desc[$x] = "<input type='image' src='" . $desc . "' onclick=\"submitform('" . $this -> table -> name . "_SetDesc" . $x . "')\" />";
        }
    }

    public function ShowInput($x) {

        if (in_array($this -> table -> field[$x], $this -> table -> fnum)) {
            return "<input type='hidden' name=\"" . $x . "\" value=\"" . htmlspecialchars($this -> table -> field[$x] -> value[0]) . "\" />" . $this -> table -> field[$x] -> value[0];
        } else
            return "<input size='" . EDIT_FIELD_SIZE . "' name=\"" . $x . "\" value=\"" . htmlspecialchars($this -> table -> field[$x] -> value[0]) . "\" />";

    }

    function ShowControlForms() {

        for ($y = 0; $y < $this -> table -> rowcount; $y++) {

            if ($this -> table -> name != null) {

                echo "<form name='" . $this -> table -> name . "_edit" . $y . "' method='post' action='./'>" . $this -> CommonHiddenInputs('edit') . $this -> PrimaryKeyInputs($y) . "</form>";

                echo "<form name='" . $this -> table -> name . "_del" . $y . "' method='post' action='./'>" . $this -> CommonHiddenInputs('query', 'delete') . $this -> PrimaryKeyInputs($y) . "</form>";

                echo "<form name='" . $this -> table -> name . "_view" . $y . "' method='post' action='./'>" . $this -> CommonHiddenInputs('view') . $this -> PrimaryKeyInputs($y) . "</form>";

            }
        }

        for ($x = 0; $x < $this -> table -> fieldcount; $x++) {

            if ($this -> table -> connection -> ordertype == 'ASC' && $this -> table -> connection -> orderby == $x)
                $asc = ICON_ASC_SELECTED;
            else
                $asc = ICON_ASC;
            if ($this -> table -> connection -> ordertype == 'DESC' && $this -> table -> connection -> orderby == $x)
                $desc = ICON_DESC_SELECTED;
            else
                $desc = ICON_DESC;

            echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' name='" . $this -> table -> name . "_SetAsc" . $x . "'>" . $this -> CommonHiddenInputs() . "
		<input type='hidden' name='ordertype' value='ASC'/><input type='hidden' name='orderby' value='" . $x . "'/>
		</form>";

            echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' name='" . $this -> table -> name . "_SetDesc" . $x . "'>" . $this -> CommonHiddenInputs() . "
		<input type='hidden' name='ordertype' value='DESC'/><input type='hidden' name='orderby' value='" . $x . "'/>
		</form>";
        }
    }

    public function ShowFormatOptions() {
        $formats = explode(',', FORMATS);

        foreach ($formats as $f) {
            $bt .= "<span>";
            if ($f == $this -> format)
                $bt .= "<input type='radio' name='format' value='" . $f . "' onclick=\"submitform('format')\" checked='checked'/>" . $f;
            else
                $bt .= "<input type='radio' name='format' value='" . $f . "' onclick=\"submitform('format')\"/>" . $f;
            $bt .= "</span>";
        }

        return $bt;
    }

    public function ShowAdditionalOptions() {

        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' name='export' id='export' enctype='multipart/form-data'>
	" . $this -> CommonHiddenInputs() . "
	<input name='export' type='hidden' value='1'/>";

        echo "<fieldset><legend>Additional Export Options</legend>";

        if ($this -> format == 'XML') {
            echo "None.";
        } else if ($this -> format == 'CSV') {
            echo "Delimiter: <input type='text' size='1' name='delimiter' value='" . $this -> delimiter . "'/>";
        }

        echo "<br/><input name='format' type='hidden' value='" . $this -> format . "'/>" . $this -> FileInput() . "
	<input type='submit' class='button' value='Go!'/></fieldset></form>";
    }

    public function FileInput() {

        //PSEUDOFUNCTION

    }

    public function Redirect() {
        echo "<form name='return' method='post' action='./index.php'>" . $this -> CommonHiddenInputs() . "</form>
	<script language='javascript' type='text/javascript'>
	<!--
	 submitform('return'); 
	//-->
	</script>";
    }

}
?>
