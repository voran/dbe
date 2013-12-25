<?php

$plugin = 'exporter';
$action = 'index';

class exporter extends dbeinterface implements plugin
{
    private $rbtn;
    private $export;
    protected $delimiter;

    public function __construct($table)
    {
        $this -> name = 'exporter';
        $this -> version = '0.1';
        $this -> author = 'Yavor Stoychev';
        $this -> action = 'index';
        $this -> table = $table;
    }

    public function Show()
    {
        if ($this -> table -> rowcount > 0) {
            $handle = @fopen(TEMP_FOLDER . "/.test", w);

            if (!$handle) {
                $error = new error("DBE cannot write into temp folder. File functions disabled");
            } else {

                fclose($handle);
                (TEMP_FOLDER . '/.test');
                $this -> Initialize();

            }
        }
    }

    public function Initialize()
    {
        $this -> format = htmlspecialchars($_REQUEST['format'], ENT_QUOTES);
        $this -> export = htmlspecialchars($_REQUEST['export'], ENT_QUOTES);
        $this -> delimiter = htmlspecialchars($_REQUEST['delimiter'], ENT_QUOTES);
        if ($this -> delimiter == null)
            $this -> delimiter = DEFAULT_DELIMITER;

        echo "<div><fieldset><form action='" . $_SERVER['PHP_SELF'] . "' method='post' name='format' id='format'>
            <div class='heading'>Export " . $this -> table -> rowcount . " rows from " . $this -> table -> name . "</div>
            <fieldset><legend>Format</legend><p class='centered'>" . $this -> ShowFormatOptions() . "</p>" . $this -> CommonHiddenInputs() . "</fieldset></form>";

        if ($this -> format != null) {

            if ($this -> export == 1) {
                if ($this -> format == 'XML')
                    $this -> ExportXML();
                else if ($this -> format == 'CSV')
                    $this -> ExportCSV();
                else if ($this -> format == 'SQL')
                    $this -> ExportSQL();
                else
                    $status = false;

                echo "<fieldset class='ok'><legend>Export Successful</legend>
                    <p class='centered'><a href = './download.php?file=" . $this -> table -> name . "." . strtolower($this -> format) . "' target='_blank'>Download File</a></p></fieldset>";
            } else
                $this -> ShowAdditionalOptions();
        }
        echo "</fieldset></div>";
    }

    public function ExportXML()
    {
        $file = new DOMDocument('1.0', 'iso-8859-1');
        $file -> formatOutput = true;

        $comment = 'DBE XML DUMP';
        $comment = $file -> createComment($comment);
        $comment = $file -> appendChild($comment);

        $comment = 'version ' . VERSION . '';
        $comment = $file -> createComment($comment);
        $comment = $file -> appendChild($comment);

        $comment = 'Engine ' . $_SESSION['engine'] . '';
        $comment = $file -> createComment($comment);
        $comment = $file -> appendChild($comment);

        if ($this -> DBE -> db -> name != null)
            $element['db'] = $file -> createElement($this -> DBE -> db -> name);
        else
            $element['db'] = $file -> createElement("root");
        $element['db'] = $file -> appendChild($element['db']);

        for ($y = 0; $y < $this -> table -> rowcount; $y++) {
            $element['table'] = $file -> createElement($this -> table -> name);
            $element['table'] = $element['db'] -> appendChild($element['table']);

            for ($x = 0; $x < $this -> table -> fieldcount; $x++) {
                $element[$fieldset[$x]['name']] = $file -> createElement($this -> table -> field[$x] -> name);
                $element[$fieldset[$x]['name']] = $element['table'] -> appendChild($element[$fieldset[$x]['name']]);

                $element['value'] = $file -> createTextNode($this -> table -> field[$x] -> value[$y]);
                $element['value'] = $element[$fieldset[$x]['name']] -> appendChild($element['value']);
            }
        }

        if (file_exists(TEMP_FOLDER . '/' . $this -> table -> name . '.xml')) {
            $handle = fopen(TEMP_FOLDER . '/' . $this -> table -> name . '.xml', r);
            fclose($handle);
            unlink(TEMP_FOLDER . '/' . $this -> table -> name . '.xml');
        }

        $save = $file -> save(TEMP_FOLDER . '/' . $this -> table -> name . '.xml');
    }

    public function ExportCSV()
    {
        if (file_exists(TEMP_FOLDER . '/' . $this -> table -> name . '.csv')) {
            $handle = fopen(TEMP_FOLDER . '/' . $this -> table -> name . '.csv', r);
            fclose($handle);
            unlink(TEMP_FOLDER . '/' . $this -> table -> name . '.csv');
        }

        $names = $this -> table -> FetchFieldNames();
        $values = $this -> table -> FetchFieldValues();

        $handle = fopen(TEMP_FOLDER . '/' . $this -> table -> name . '.csv', w);

        fputcsv($handle, $names, $this -> delimiter);

        for ($y = 0; $y < $this -> table -> rowcount; $y++) {
            fputcsv($handle, $values[$y], $this -> delimiter);
        }

        fclose($handle);
    }

    public function ExportSQL()
    {
        if (file_exists(TEMP_FOLDER . '/' . $this -> table -> name . '.sql')) {
            $handle = @fopen(TEMP_FOLDER . '/' . $this -> table -> name . '.sql', r);
            fclose($handle);
            unlink(TEMP_FOLDER . '/' . $this -> table -> name . '.sql');
        }
        $handle = fopen(TEMP_FOLDER . '/' . $this -> table -> name . '.sql', w);

        fwrite($handle, "-- DBE " . VERSION . " SQL DUMP --\n");
        fwrite($handle, "-- Table " . $this -> table -> name . ": --\n");

        $insert = $this -> table -> GenerateInsert();

        fwrite($handle, "" . $insert . ";\n");

        fclose($handle);
    }
}
?>
