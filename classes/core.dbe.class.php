<?php

class core 
{

    static $connection;

    static $db;
    static $dbindex;
    static $dbcount;

    public function __construct($connection) {
        $this -> InitializeConnection($connection);

        $this -> GetDatabases();

        $this -> db -> GetTables();

        $this -> connection -> ClearCache();
    }

    public function InitializeConnection($connection) {
        if ($connection == null) {
            if ($_SESSION['engine'] == 'mysql')
                $this -> connection = new MysqlExplorer();
            if ($_SESSION['engine'] == 'pgsql')
                $this -> connection = new PostgresExplorer();
            if ($_SESSION['engine'] == 'mssql')
                $this -> connection = new MssqlExplorer();
            if ($_SESSION['engine'] == 'oci8')
                $this -> connection = new OracleExplorer();
            if ($_SESSION['engine'] == 'interbase')
                $this -> connection = new FirebirdExplorer();
            if ($_SESSION['engine'] == 'sqlite')
                $this -> connection = new SqliteExplorer();
        } else
            $this -> connection = $connection;
        $this -> connection -> Initialize();

        if ($this -> connection -> link === false) {
            session_unset();
            session_destroy();
            $this -> error = new error("<p/>Error connecting to server!<p/>" . $this -> connection -> ConnectionError() . "");
        }
    }

    public function GetDatabases() {
        $this -> dbindex = htmlspecialchars($_REQUEST['db'], ENT_QUOTES);
        $this -> user = $_SESSION['dbuser'];

        if ($this -> connection -> DatabaseScan() != null) {
            $x = 0;
            $this -> connection -> Execute($this -> connection -> DatabaseScan());

            while ($dbs = $this -> connection -> FetchRow()) {

                if (!in_array($dbs[0], $this -> connection -> exclude_dbs)) {
                    $this -> dblist[$x] = new db($dbs[0], $this -> connection, $x);
                    $x++;
                }
            }

            if ($this -> dbindex == null)
                $this -> dbindex = 0;
            $this -> db = $this -> dblist[$this -> dbindex];
            $this -> dbcount = $x;

            if ($this -> connection -> DatabaseScan() == null)
                $this -> db -> valid = 1;
            $this -> connection -> SelectDatabase($this -> db -> name);

        }

        if ($this -> dbcount < 1)
            $this -> db = new db(null, $this -> connection, $x);
    }
}
?>
