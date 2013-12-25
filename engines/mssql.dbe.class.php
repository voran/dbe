<?php

class MssqlExplorer extends connection
{

    public function __construct() {
        $this -> schema = 'INFORMATION_SCHEMA';
        $this -> admin_username = 'sa';
    }

    /*//////////////////////////////////////////////       ALIASES        /////////////////////////////////////////////////////////*/
    public function Connect($server, $user, $password)
    {
        $this -> link = @mssql_connect($server, $user, $password);
    }

    public function FetchRow()
    {
        return mssql_fetch_row($this -> result_id);
    }

    public function NumFields()
    {
        return mssql_num_fields($this -> result_id);
    }

    public function Result($i)
    {
        return mssql_result($this -> result_id, $i, 0);
    }

    public function SelectDatabase($db)
    {
        $this -> Execute("USE " . $db);
    }

    public function SelectDatabaseSchema()
    {
        return null;
    }

    public function GetMax($table)
    {
        $this -> Execute("SELECT TOP 1 " . $table -> pnum[0] -> name . " FROM " . $table -> name . " ORDER BY " . $table -> pnum[0] -> index . " DESC");
    }

    public function ServerError($empty)
    {
        return mssql_get_last_message();
    }

    public function ConnectionError()
    {
        return mssql_get_last_message();
    }

    public function EscapeString($string)
    {
        return mysql_real_escape_string($string);
    }

    public function EncapsulateField($string)
    {
        return str_replace("'", "''", $string);
    }

    public function EncapsulateData($data)
    {
        if ($data == null || $data == "")
            return "NULL";
        else
            return "'" . $this -> EscapeString($data) . "'";

    }

    public function Execute($query)
    {
        if ($query != $empty) {
            $this -> result_id = mssql_query($query);
            // Perform Query

            //Debug
            if ($this -> result_id === false) {
                $message = '<p/>Invalid query: ' . $this -> ServerError($empty) . "\n";
                $message .= '<p/>Whole query: ' . $query;
                $this -> error = new DBEErrorPanel($message);
            }
        } else
            $this -> result_id = true;
    }

    function GenerateDefault($tablename)
    {
        return "SELECT TOP 10 * FROM " . $tablename . "";
    }

    function OrderQuery($query, $column)
    {
        return $query . " ORDER BY " . $column[$this -> orderby] -> name . " " . $this -> ordertype;
    }

    function LimitQuery($query)
    {
        $limit = explode(',', LIMIT);
        return str_replace("SELECT", "SELECT TOP " . $limit[$this -> limit] . "", $query);
    }

    function DatabaseScan()
    {
        return "exec sp_databases";
    }

    function TableScan($dbname)
    {
        return "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
    }

    public function RelationScan($dbname, $tablename)
    {
        return "SELECT 

        PK_TABLE  = PK.TABLE_NAME, 
        FK_TABLE  = FK.TABLE_NAME,
        PK_COLUMN = PT.COLUMN_NAME, 
        FK_COLUMN = CU.COLUMN_NAME, 
        CONSTRAINT_NAME = C.CONSTRAINT_NAME 
        FROM 
            INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS C 
            INNER JOIN 
            INFORMATION_SCHEMA.TABLE_CONSTRAINTS FK 
            ON C.CONSTRAINT_NAME = FK.CONSTRAINT_NAME 
            INNER JOIN 
            INFORMATION_SCHEMA.TABLE_CONSTRAINTS PK 
            ON C.UNIQUE_CONSTRAINT_NAME = PK.CONSTRAINT_NAME 
        INNER JOIN 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE CU 
        ON C.CONSTRAINT_NAME = CU.CONSTRAINT_NAME 
        INNER JOIN 
            ( 
                SELECT 
                    i1.TABLE_NAME, i2.COLUMN_NAME 
                FROM 
                    INFORMATION_SCHEMA.TABLE_CONSTRAINTS i1 
                INNER JOIN 
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE i2 
                ON i1.CONSTRAINT_NAME = i2.CONSTRAINT_NAME 
                WHERE i1.CONSTRAINT_TYPE = 'PRIMARY KEY' 
            ) PT 
                ON PT.TABLE_NAME = PK.TABLE_NAME
                WHERE (FK.TABLE_NAME='" . $tablename . "' OR PK.TABLE_NAME='" . $tablename . "')";

    }

    public function GetNullable($tablename)
    {
        $this -> Execute("SELECT c.COLUMN_NAME
            FROM information_schema.tables AS t JOIN information_schema.columns AS c 
                ON
                    t.table_catalog=c.table_catalog AND
                     t.table_schema=c.table_schema AND
                    t.table_name=c.table_name
            WHERE t.TABLE_NAME='" . $tablename . "' AND c.IS_NULLABLE <> 'YES'");

        while ($row = $this -> FetchRow()) {
            $nullable[++$i] = $row[0];
        }

        return $nullable;
    }

    public function GetPrimary($tablename)
    {
        $this -> Execute("SELECT 
        PK_COLUMN = PT.COLUMN_NAME,  
        CONSTRAINT_NAME = C.CONSTRAINT_NAME, PK.CONSTRAINT_TYPE
        FROM 
            INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS C  
            INNER JOIN 
            INFORMATION_SCHEMA.TABLE_CONSTRAINTS PK 
            ON C.UNIQUE_CONSTRAINT_NAME = PK.CONSTRAINT_NAME  
        INNER JOIN 
            ( 
                SELECT 
                    i1.TABLE_NAME, i2.COLUMN_NAME 
                FROM 
                    INFORMATION_SCHEMA.TABLE_CONSTRAINTS i1 
                INNER JOIN 
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE i2 
                ON i1.CONSTRAINT_NAME = i2.CONSTRAINT_NAME  
            ) PT 
                ON PT.TABLE_NAME = PK.TABLE_NAME
                WHERE PK.TABLE_NAME='" . $tablename . "' AND PK.CONSTRAINT_TYPE = 'PRIMARY KEY'");

        while ($row = $this -> FetchRow()) {
            $primary[++$i] = $row[0];
        }

        return $primary;

    }

    public function KeywordScan($table)
    {

        $query = "SELECT DISTINCT * FROM " . $this -> EncapsulateField($table -> name) . " WHERE ";

        if ($this -> searchin == null) {
            for ($x = 0; $x < $table -> fieldcount; $x++) {
                if ($x != 0)
                    $query = $query . " OR ";
                $query = $query . $this -> EncapsulateField($table -> field[$x] -> name) . " LIKE '%" . $this -> keyword . "%'";
            }
        } else {
            $query = $query . $this -> EncapsulateField($table -> field[$this -> searchin] -> name) . " LIKE '%" . $this -> keyword . "%'";
        }

        return $query;
    }

    public function FetchField($i)
    {

        $fieldobject = mssql_fetch_field($this -> result_id, $i);

        $field -> name = $fieldobject -> name;

        $field -> type = $fieldobject -> type;

        $field -> length = $fieldobject -> max_length;

        $field -> unique = $fieldobject -> unique_key;

        return $field;
    }
}
?>
