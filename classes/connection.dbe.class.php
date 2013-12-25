<?php

class connection
{

    public $orderby;
    public $ordertype;
    public $searchin;
    public $keyword;
    public $limit;
    public $limitlist;
    public $link;
    public $result_id;
    public $exclude_tables = array();
    public $exclude_dbs = array();
    public $admin_username;
    public $schema;

    public function Initialize()
    {

        $this -> orderby = htmlspecialchars($_REQUEST['orderby'], ENT_QUOTES);
        $this -> ordertype = htmlspecialchars($_REQUEST['ordertype'], ENT_QUOTES);
        $this -> limit = htmlspecialchars($_REQUEST['limit'], ENT_QUOTES);

        if ($this -> orderby == null)
            $this -> orderby = DEFAULT_ORDERBY;
        if ($this -> ordertype == null)
            $this -> ordertype = DEFAULT_ORDERTYPE;
        if ($this -> limit == null)
            $this -> limit = DEFAULT_LIMIT;

        $this -> searchin = htmlspecialchars($_REQUEST['searchin'], ENT_QUOTES);

        $this -> keyword = htmlspecialchars($_REQUEST['keyword'], ENT_QUOTES);

        $this -> Connect($_SESSION['dbhost'], $_SESSION['dbuser'], $_SESSION['dbpass']);
    }

    public function ClearCache()
    {
        $this -> result_id = null;
    }

    /*/////////////////////////////////////////////////       EXECUTE SQL SEQUENCE	/////////////////////////////////////////////////////*/
    public function ExecuteSQLSequence($query)
    {

        $replacement = '&scol-';

        $start = strpos($query, "'");
        $end = strpos($query, "'", $start + 1);
        $semicolumn = strpos($query, ";");
        $x = 0;

        while (strpos($query, ";", $semicolumn + 1) !== false && $start !== false) {

            if ($start < $semicolumn) {
                if ($semicolumn < $end) {

                    $semicolumn = strpos($query, ";", $semicolumn + 1);

                    $source[++$x] = substr($query, $start, ($end - $start) + 1);

                    $fixed[$x] = str_replace(';', $replacement, $source[$x]);

                }

                $start = strpos($query, "'", $end + 1);
                $end = strpos($query, "'", $start + 1);

            } else
                $semicolumn = strpos($query, ";", $semicolumn + 1);
        }

        $query_fixed = str_replace($source, $fixed, $query);

        $query_array_fixed = explode(';', stripslashes($query_fixed));

        $query_array = str_replace($replacement, ';', $query_array_fixed);

        for ($x = 0; $x < count($query_array); $x++) {
            if (strlen($query_array[$x]) > 1) $this -> Execute($query_array[$x]);
        }

        return $status;
    }
}
?>