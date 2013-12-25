<?php

class login extends dbeinterface {

    function __construct()
    {
        $header = new header(null);

        echo "<br/><div><fieldset><div class='heading'>Connect to Server</div><div class='cenetered'><img src='./img/dbe.png' alt='DBE'/></div>
        <form action='" . $_SERVER['PHP_SELF'] . "' method='post' name='Login' id='Login'>
        <table>
        <tr><td>Hostname:</td><td><input name='dbhost' type='text'          value='" . DEFAULT_DBHOST . "' id='dbhost' size='20'/></td></tr>
        <tr><td>Username:</td><td><input name='dbuser' type='text'      value='" . DEFAULT_DBUSER . "' id='dbuser' size='20'/></td></tr>
        <tr><td>Password:</td><td><input name='dbpass' type='password'      value='" . DEFAULT_DBPASS . "' id='dbpass' size='20'/></td></tr>
        </table>
        <fieldset><legend>Engine</legend>

            <input type='radio' name='engine' value='mysql' checked='checked'/>MySQL<br />
            <input type='radio' name='engine' value='mssql'/>MS SQL<br />
            <input type='radio' name='engine' value='pgsql'/>PostgreSQL<br />
            <input type='radio' name='engine' value='interbase'/>FireBird / InterBase<br />
            <input type='radio' name='engine' value='oci8'/>Oracle<br />
            <input type='radio' name='engine' value='sqlite'/>SQLite<br />


        </fieldset>
        <input class='button' type='submit' value='Go!'/></form></fieldset></div>";
        $footer = new footer();
        exit ;
    }
}
?>
