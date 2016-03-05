<?php

class Test extends \AIIX\Object {
    protected $prop = 123;
    protected function _get_property ($prop) {
        return $this->{$prop};
    }
    protected function _set_property ($prop, $value) {
        return $this->{$prop} = $value;
    }
    protected function _get_prop () {
        return $this->prop;
    }
}
//$testobject = new Test;