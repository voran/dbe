<?php

class error
{
    public function __construct($message) {
        echo "<div><fieldset class='error'><legend>Error</legend>" . $message . "<br /><br />
		<p class='centered'><a href='" . $_SERVER['PHP_SELF'] . "'>Back</a></p></fieldset></div>";
    }

}
?>
