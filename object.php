<?php

/*
 * (C) 2015, AII (Alexey Ilyin).
 */

class AIIXObject
{

    public function __get ($prop) {
        return method_exists($this, $method = "get_$prop") ?
            $this->$method() : $this->get_property($prop);
    }
    public function __set ($prop, $value) {
        return method_exists($this, $method = "set_$prop") ?
            $this->$method() : $this->set_property($prop);
    }

    protected function get_property ($prop) {
        throw new Exception(get_class($this)." has no property $prop to read");
    }
    protected function set_property ($prop) {
        throw new Exception(get_class($this)." has no property $prop to write");
    }

}