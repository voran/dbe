<?php

class SQLiteExplorer extends connection
{

    public function __construct() {}

    /*//////////////////////////////////////////////       ALIASES		/////////////////////////////////////////////////////////*/
    public function Connect($server, $user, $password)
    {
        $this -> link = @sqlite_open($server);
    }

    public function FetchRow()
    {
        return sqlite_fetch_array($this -> result_id, SQLITE_NUM);
    }

    public function NumFields()
    {
        return sqlite_num_fields($this -> result_id);
    }

    public function SelectDatabase($db)
    {
        return null;
    }

    public function SelectDatabaseSchema()
    {
        return null;
    }

    public function GetMax($table)
    {
        return null;
    }

    public function ServerError($empty)
    {
        return "Error!";
    }

    public function ConnectionError()
    {
        return "Error!";
    }

    public function EscapeString($string)
    {
        return sqlite_escape_string($string);
    }

    public function EncapsulateField($string)
    {
        return $string;
    }

    public function EncapsulateData($data)
    {
        if ($data == null || $data == "")
            return "null";
        else
            return "'" . $this -> EscapeString($data) . "'";

    }

    public function Execute($query)
    {
        if ($query != $empty) {
            $this -> result_id = sqlite_query($query, $this -> link, $errormsg);
            // Perform Query

            //Debug
            if ($this -> result_id === false) {
                $message = '<p/>Invalid query: ' . $errormsg . "\n";
                $message .= '<p/>Whole query: ' . $query;
                $error = new DBEErrorPanel($message);
            }
        }
    }

    function GenerateDefault($tablename)
    {
        return "SELECT * FROM " . $tablename . " LIMIT 4";
    }

    function OrderQuery($query, $column)
    {
        return $query . " ORDER BY " . $column[$this -> orderby] -> name . " " . $this -> ordertype;
    }

    function LimitQuery($query)
    {
        $limit = explode(',', LIMIT);
        return $query . " LIMIT " . $limit[$this -> limit];
        //OFFSET 0 , LIMIT x
    }

    function DatabaseScan()
    {
        return null;
    }

    function TableScan($dbname)
    {
        return "SELECT name FROM sqlite_master WHERE type='table'";
    }

    public function RelationScan($dbname, $tablename)
    {
        return null;
    }

    function GetNullable($tablename)
    {
        $this -> Execute("PRAGMA table_info(" . $tablename . ")");

        while ($row = $this -> FetchRow()) {
            if ($row[5] == 1)
                $nullable[++$i] = $row[1];
        }

        return $nullable;
    }

    function GetPrimary($tablename)
    {
        $this -> Execute("PRAGMA table_info(" . $tablename . ")");

        while ($row = $this -> FetchRow()) {
            if ($row[3] == 1)
                $primary[++$i] = $row[1];
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
        //$typearr = sqlite_fetch_column_types($table, $link);

        $field -> name = sqlite_field_name($this -> result_id, $i);
        $field -> length = "NULL";
        //$field->type = $typearr[$i];

        return $field;
    }
}
%>