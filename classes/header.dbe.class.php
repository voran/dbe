<?php

class header extends dbeinterface {

    public function __construct($dbe = null)
    {
        echo " <!DOCTYPE html>
    	<html>
    	<head>
    	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    	<title>" . TITLE;
        
        if ($dbe -> db != null)
        echo " |||  " . $dbe -> action . " " . $dbe -> db -> name . " :: " . $dbe -> db -> table -> name;

        echo "</title>";

        $style = new DBEStyle();

        echo
        "</head><body>
        <script>
        function submitform(formname)
        {
            document.forms[formname].submit();
        }
        </script>";
    }

}
?>
