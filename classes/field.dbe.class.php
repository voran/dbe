<?php 
class field extends component
{

    public $value;
    public $primary;
    public $unique;
    public $length;
    public $nullable;
    public $subconstraint;
    public $superconstraint;
    public $constraint_value;

    public function __construct($fieldobj, $index)
    {

        $this -> value = array();
        $this -> parent = array();
        $this -> child = array();

        $this -> index = $index;
        $this -> name = $fieldobj -> name;
        $this -> type = $fieldobj -> type;
        $this -> length = $fieldobj -> length;
        $this -> unique = $fieldobj -> unique;
        $this -> nullable = $fieldobj -> nullable;
        $this -> primary = $fieldobj -> primary;

    }

    public function __get($data)
    {
        return $this -> $data;
    }

    public function __set($data, $value)
    {
        $this -> $data = $value;
    }

}
?>
