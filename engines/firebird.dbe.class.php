<?php

class FirebirdExplorer extends connection {

    public function __construct()
    {
        $this -> admin_username = 'SYSDBA';
    }

    /*//////////////////////////////////////////////       ALIASES        /////////////////////////////////////////////////////////*/
    public function Connect($server, $user, $password)
    {    $this -> link = @ibase_connect($server, $user, $password);
    }

    public function FetchRow()
    {
        return $this -> FixArray(ibase_fetch_row($this -> result_id));
    }

    public function NumFields()
    {
        return ibase_num_fields($this -> result_id);
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
        return $this -> Execute("SELECT MAX(`" . $table -> pnum[0] -> name . "`) FROM `" . $table -> name . "`");
    }

    public function ServerError($empty)
    {
        return ibase_errmsg();
    }

    public function ConnectionError()
    {
        return ibase_errmsg();
    }

    public function EscapeString($string)
    {
        return $string;
    }

    public function EncapsulateField($string)
    {
        return "\"" . $string . "\"";
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
            $this -> result_id = ibase_query($query);
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
        return "SELECT FIRST 2 * FROM " . $tablename . "";
        //SKIP for offset
    }

    function OrderQuery($query, $column)
    {
        return $query . " ORDER BY " . $column[$this -> orderby] -> name . " " . $this -> ordertype;
    }

    function LimitQuery($query)
    {
        $limit = explode(',', LIMIT);
        return str_replace("SELECT", "SELECT FIRST " . $limit[$this -> limit] . "", $query);
    }

    function DatabaseScan()
    {
        return null;
    }

    function TableScan($dbname)
    {
        return 'SELECT RDB$RELATION_NAME FROM RDB$RELATIONS WHERE RDB$SYSTEM_FLAG = 0 AND RDB$VIEW_BLR IS NULL ORDER BY RDB$RELATION_NAME';
    }

    public function RelationScan($dbname, $tablename) {
        return 'SELECT DISTINCT target_index.RDB$RELATION_NAME AS PK_TABLE, source_index.RDB$RELATION_NAME AS FK_TABLE,
                        target_segments.RDB$FIELD_NAME AS PK_COLUMN,
                    source_segments.RDB$FIELD_NAME AS FK_COLUMN,
                    
                        source_index.RDB$INDEX_NAME AS CONSTRAINT_NAME

                        FROM RDB$INDICES source_index JOIN RDB$INDICES target_index
                           ON source_index.RDB$FOREIGN_KEY=target_index.RDB$INDEX_NAME
                            JOIN RDB$INDEX_SEGMENTS source_segments
                           ON source_index.RDB$INDEX_NAME=source_segments.RDB$iNDEX_NAME
                            JOIN RDB$INDEX_SEGMENTS target_segments
                           ON target_index.RDB$INDEX_NAME=target_segments.RDB$iNDEX_NAME
                        WHERE source_index.RDB$RELATION_NAME = \'' . $tablename . '\'';

    }

    public function GetPrimary($tablename)
    {
        $this -> Execute('SELECT target_segments.RDB$FIELD_NAME AS PK_COLUMN,
                    source_segments.RDB$FIELD_NAME AS FK_COLUMN,
                    source_index.RDB$RELATION_NAME AS FK_TABLE,
                        source_index.RDB$INDEX_NAME AS CONSTRAINT_NAME

                        FROM RDB$INDICES source_index JOIN RDB$INDICES target_index
                           ON source_index.RDB$FOREIGN_KEY=target_index.RDB$INDEX_NAME
                            JOIN RDB$INDEX_SEGMENTS source_segments
                           ON source_index.RDB$INDEX_NAME=source_segments.RDB$iNDEX_NAME
                            JOIN RDB$INDEX_SEGMENTS target_segments
                           ON target_index.RDB$INDEX_NAME=target_segments.RDB$iNDEX_NAME
                        WHERE  target_index.RDB$RELATION_NAME = \'' . $tablename . '\'');

        while ($row = $this -> FetchRow()) {
            $primary[++$i] = $row[0];
        }

        return $primary;
    }

    public function GetNullable($tablename)
    {
        $this -> Execute('select f.rdb$field_name 
    from rdb$relation_fields f where f.rdb$null_flag  = \'1\' and f.rdb$relation_name = ' . $this -> EncapsulateData($tablename) . '');

        while ($row = $this -> FetchRow()) {
            $nullable[++$i] = $row[0];
        }

        return $nullable;
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

        $fieldobject = ibase_field_info($this -> result_id, $i);
        //Returns an array with the following keys: name, alias, relation, length and type.

        $field -> name = rtrim($fieldobject['name']);

        $field -> length = $fieldobject['length'];

        $field -> type = $fieldobject['type'];

        return $field;

    }

    public function FixArray($array)
    {

        if ($array[0] != false) {
            for ($c = 0; $c < count($array); $c++) {
                if ($array[$c] == false)
                    break;
                $array_fixed[$c] = rtrim($array[$c]);

            }
            return $array_fixed;
        } else
            return false;
    }
}
?>
