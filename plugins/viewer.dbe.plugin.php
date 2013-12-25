<?php

$plugin = 'viewer';
$action = 'view';

class viewer extends dbeinterface implements plugin
{
    public function __construct($table) {
        $this -> table = $table;
        $this -> table -> GetValues();

        $this -> table -> connection -> Execute($this -> table -> GenerateConditionalSelect());
        $this -> table -> GetResult();

    }

    public function Show() {

        $mainlist = new indexer($this -> table);
        $mainlist -> Show();

        for ($x = 0; $x < count($this -> table -> parent); $x++) {
            if ($this -> table -> parent[$x] != $this -> table -> parent[$x - 1])
                $a = 0;
            else
                $a++;

            $this -> table -> connection -> Execute($this -> table -> parent[$x] -> GenerateCustomSelect($this -> table -> fnum[$x] -> parent[$a] -> index, $this -> table -> fnum[$x] -> value[0]));
            $this -> table -> parent[$x] -> GetResult();

            $list = new indexer($this -> table -> parent[$x]);
            $list -> Show();

        }

        for ($x = 0; $x < count($this -> table -> child); $x++) {

            if ($this -> table -> child[$x] != $this -> table -> child[$x - 1])
                $a = 0;
            else
                $a++;

            $this -> table -> connection -> Execute($this -> table -> child[$x] -> GenerateCustomSelect($this -> table -> pnum[$x] -> child[$a] -> index, $this -> table -> pnum[$x] -> constraint_value[0]));
            $this -> table -> child[$x] -> GetResult();

            $list = new indexer($this -> table -> child[$x]);
            $list -> Show();
        }
    }
}
?>
