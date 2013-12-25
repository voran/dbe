<?php

class db extends component
{
    protected $user;
    protected $readonly;
    protected $valid;
    public $table;
    protected $tableindex;
    protected $tablelist;
    protected $connection;
    public $tablecount;

    public function __construct($name, $connection, $index)
    {
        $this -> name = $name;
        $this -> user = $_SESSION['dbuser'];
        $this -> connection = $connection;
        $this -> index = $index;

    }

    public function GetTables()
    {

        $this -> tableindex = htmlspecialchars($_REQUEST['table'], ENT_QUOTES);

        $x = 0;
        $table = array();

        $this -> connection -> Execute($this -> connection -> TableScan($this -> name));
        while ($tables = $this -> connection -> FetchRow()) {
            $table[$x++] = $tables[0];
        }
        $x = 0;
        foreach ($table as $tbl) {
            if (!in_array($tbl, $this -> connection -> exclude_tables)) {
                $this -> tablelist[$x] = new table($this -> index, $x, $tbl, $this -> connection);
                $x++;
            }
        }

        if ($this -> tableindex == null)
            $this -> tableindex = 0;

        $this -> table = $this -> tablelist[$this -> tableindex];

        $this -> tablecount = $x;

        if ($this -> tablecount == 0)
            $this -> table = new table($this -> index, 0, null, $this -> connection);

        $this -> GetRelations();
    }

    public function FetchTable($name)
    {
        for ($x = 0; $x < $this -> tablecount; $x++) {
            if ($this -> tablelist[$x] -> name == $name) {
                return $this -> tablelist[$x];
            }
        }
    }

    public function GetRelations()
    {
        $this -> connection -> SelectDatabaseSchema();
        $this -> connection -> Execute($this -> connection -> RelationScan($this -> name, $this -> table -> name));

        $a = 0;
        $b = 0;

        $rel = array();

        while ($row = $this -> connection -> FetchRow()) {
            $rel[++$w] = $row;
        }

        for ($y = 0; $y < $this -> table -> fieldcount; $y++){
            $c = 0;
            foreach ($rel as $row) {

                if ($this -> table -> field[$y] -> name == $row[3] && $this -> table -> name == $row[1]) {
                    //echo "we're in a child table!<p>";
                    $this -> table -> parent[$a] = $this -> FetchTable($row[0]);
                    $this -> table -> field[$y] -> parent[$c] = $this -> table -> parent[$a] -> FetchField($row[2]);
                    $this -> table -> field[$y] -> superconstraint[$c] = $row[4];
                    $this -> table -> fnum[$a] = $this -> table -> field[$y];
                    $a++;
                    $c++;

                }
            }

        }

        for ($y = 0; $y < $this -> table -> fieldcount; $y++) {
            $d = 0;
            foreach ($rel as $row) {
                //echo "we're in a parent table!<p>";
                if ($this -> table -> field[$y] -> name == $row[2] && $this -> table -> name == $row[0]) {
                    $this -> table -> child[$b] = $this -> FetchTable($row[1]);
                    $this -> table -> field[$y] -> child[$d] = $this -> table -> child[$b] -> FetchField($row[3]);
                    $this -> table -> field[$y] -> subconstraint[$d] = $row[4];
                    $this -> table -> pnum[$b] = $this -> table -> field[$y];
                    $b++;
                    $d++;
                }

            }
        }
        $this -> connection -> SelectDatabase($this -> name);

    }

    public function __get($data)
    {
        return $this -> $data;
    }

    public function __set($data, $value)
    { $this -> $data = $value;
    }
}
?>
