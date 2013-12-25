<?php

class DBEClass {

    public $DBE;
    public $action;
    public $plugins = array();
    public $actions = array();

    function __construct()
    {
        $this -> action = htmlspecialchars($_REQUEST['action'], ENT_QUOTES);

        if ($this -> action == null)
            $this -> action = DEFAULT_ACTION;
        if ($_REQUEST['logout'] != null) {
            session_start();
            session_unset();
            session_destroy();
        }

        $this -> InitializeConnection();
        $this -> LoadPlugins();
        $this -> ShowContent();
    }

    public function InitializeConnection()
    {
        session_start();
        if ($_SESSION['dbuser'] == null) {
            $_SESSION['dbhost'] = htmlspecialchars($_REQUEST['dbhost'], ENT_QUOTES);
            $_SESSION['dbuser'] = htmlspecialchars($_REQUEST['dbuser'], ENT_QUOTES);
            $_SESSION['dbpass'] = htmlspecialchars($_REQUEST['dbpass'], ENT_QUOTES);
            $_SESSION['engine'] = htmlspecialchars($_REQUEST['engine'], ENT_QUOTES);
        }

        if ($_SESSION['dbuser'] == null)
            $login = new login();
        else {
            if (!extension_loaded($_SESSION['engine'])) {
                session_unset();
                session_destroy();
                $error = new error($_REQUEST['engine'] . " is not present in your PHP installation. Please install it and try again.");
            } else {
                $this -> DBE = new core(null);
                $this -> table = $this -> DBE -> db -> table;
            }
        }
    }

    public function __autoload($class)
    {
        require_once './classes/' . $class . '.dbe.class.php';
    }

    public function LoadPlugins()
    {
        $i = 0;

        foreach (glob("./plugins/*.dbe.plugin.php") as $filename) {
            require_once $filename;
            if ($action == $this -> action)
                $this -> plugins[$i++] = new $plugin($this -> table);
            else if (!in_array($action, $this -> actions))
                array_push($this -> actions, $action);
        }
    }

    public function ShowContent()
    {
        $header = new header();

        $menu = new menu($this -> DBE, $this -> action);

        foreach ($this->plugins as $plugin) {
            $plugin -> Show();
        }
        $footer = new footer();
    }
}
?>
