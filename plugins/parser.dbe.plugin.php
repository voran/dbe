<?php

$plugin = 'parser';
$action = 'query';

class parser extends dbeinterface implements plugin
{

    public $query;

    public function __construct($table)
    {
        $this -> table = $table;
        $this -> name = 'parser';
        $this -> author = 'Yavor Stoychev';
        $this -> version = '0.10';
        $this -> action = 'query';

        $this -> query = htmlspecialchars($_REQUEST['query']);

        $this -> table -> GetValues();

        if ($this -> query == 'delete')
            $this -> query = $this -> table -> GenerateDelete();
        if ($this -> query == 'insert')
            $this -> query = $this -> table -> GenerateInsert();
        if ($this -> query == 'update')
            $this -> query = $this -> table -> GenerateUpdate();

    }

    public function Show()
    {
        echo "<div><fieldset><div class='heading'>Query Sequence to Execute</div>
        <form method='post' action='" . $_SERVER['PHP_SELF'] . "'>" . $this -> CommonHiddenInputs() . "
        <textarea name='query' cols='100' rows='5'>" . $this -> query . "</textarea><br/>
        <input class='button' type='submit' value='Go!'/></form></fieldset></div>";

        if ($this -> query != null) {
            $this -> table -> connection -> ExecuteSQLSequence($this -> query);

            if ($this -> table -> connection -> result_id !== false) {
                echo "<div><fieldset class='ok'><legend>Note</legend><p class='centered'>Executed Successfully</p></fieldset></div>";
            }

            if ($this -> table -> connection -> result_id !== true) {
                $this -> table -> CustomResult();
                $list = new indexer($this -> table);
                $list -> Show();
            }

            if (htmlspecialchars($_REQUEST['redirect'], ENT_QUOTES) == 'index')
                $this -> Redirect();

        }
    }
}
?>