<?php namespace AIIX;

/*
 * (C) 2016, AII (Alexey Ilyin).
 */

class Object
{

    public function __get ($prop) {
        return method_exists($this, $method = "_get_$prop") ?
            $this->$method() : $this->_get_property($prop);
    }
    public function __set ($prop, $value) {
        return method_exists($this, $method = "_set_$prop") ?
            $this->$method() : $this->_set_property($prop, $value);
    }

    protected function _get_property ($prop) {
        throw new Exception(get_class($this)
            ." has no property '$prop' to read");
    }
    protected function _set_property ($prop, $value) {
        throw new Exception(get_class($this)
            ." has no property '$prop' to write gettype($value)");
    }

}