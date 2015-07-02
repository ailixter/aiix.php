<?php

/*
 * (C) 2015, AII (Alexey Ilyin).
 */

require_once __dir__.'/object.php';

class AIIXData extends AIIXObject
    implements Countable, Iterator, ArrayAccess
{
    protected $data, $ts = '/';

    /**
     *
     * @param array $data
     * @param mixed $pool bool | array
     */
    public function __construct (array $data = array(), $pool = false) {
        $this->data = $data;
        $pool and self::build($this->data, is_array($pool) ? $pool : $data);
    }

    /**
     * Get value at $path, $default otherwise.
     * @param string $path null | 'key(/key)*'
     * @param mixed  $default
     * @return mixed
     */
    public function get ($path = null, $default = null) {
        return self::getp($this->data, $path, $default, $this->ts);
    }

    /**
     * Check if the value at $path exists.
     * @param  string $path
     * @return boolean
     */
    public function has ($path) {
        return self::hasp($this->data, $path);
    }

    /**
     * Get reference to the value at $path.
     * &$aiixdata->ref() returns reference to raw data array.
     * @param string $path null | 'key(/key)*'
     * @param mixed  $default
     * @return mixed
     */
    public function &ref ($path = null) {
        return self::refp($this->data, $path);
    }


    /**
     * Set the value at $path.
     * @param  string $path
     * @param  mixed $value
     * @return \AIIXData
     */
    public function set ($path, $value) {
        self::setp($this->data, $path, $value);
        return $this;
    }

    /**
     * Set the reference at $path.
     * @param  string $path
     * @param  mixed &$value
     * @return \AIIXData
     */
    public function setref ($path, &$value) {
        self::setrefp($this->data, $path, $value);
        return $this;
    }

    /**
     * Add the value at $path.
     * @param  string $path
     * @param  mixed $value
     * @param  mixed $key scalar | null
     * @return \AIIXData
     */
    public function add ($path, $value, $key = null) {
        self::addp($this->data, $path, $value, $key);
        return $this;
    }

    /**
     * Add the reference at $path.
     * @param  string $path
     * @param  mixed &$value
     * @param  mixed $key scalar | null
     * @return \AIIXData
     */
    public function addref ($path, &$value, $key = null) {
        self::addrefp($this->data, $path, $value, $key);
        return $this;
    }

    //---=====[ facade ]=====---//

    /**
     * If exists $pool[$data([key])+], copy it to $data([key])+ recursively.
     * @param array $data
     * @param mixed $pool false | array
     */
    public static function build (&$data, $pool = false) {
        $pool or $pool = $data;
        foreach ($data as &$val) {
            if (!is_scalar($val)) {
                settype($val, 'array')
                and self::build($val, $pool);
            }
            else if (isset($val[0]) && $val[0] === '@' && isset($pool[$val])) {
                is_scalar($val = $pool[$val])
                or settype($val, 'array');
            }
        }
    }

    public static function getp ($data, $path = null, $default = null, $ts='/') {
        if (isset($path)) for ($name = strtok($path,$ts);
            $name !== false; $name = strtok($ts)) {
            if (!strlen($name = trim($name))) continue;
            if (is_scalar($data) || !isset($data[$name])) {
                return is_callable($default) ?
                    call_user_func($default, $path) :
                    $default;
            }
            $data = $data[$name];
        }
        return $data;
    }

    public static function hasp ($data, $path, $ts='/') {
        for ($name = strtok($path,$ts);
            $name !== false; $name = strtok($ts)) {
            if (!strlen($name = trim($name))) continue;
            if (is_scalar($data) || !isset($data[$name])) return false;
            $data = $data[$name];
        }
        return true;
    }

    public static function &refp (&$data, $path = null, $ts='/') {
        if (isset($path)) for ($name = strtok($path,$ts);
            $name !== false; $name = strtok($ts)) {
            if (!strlen($name = trim($name))) continue;
            if (is_scalar($data)) $data = array();
            $data = &$data[$name];
        }
        return $data;
    }

    public static function setp (&$data, $path, $value, $ts='/') {
        $data = &self::refp($data, $path, $ts);
        $data = $value;
    }

    public static function setrefp (&$data, $path, &$value, $ts='/') {
        for ($name = strtok($path, $ts);
            $name !== false; $name = strtok($ts)) {
            if (!strlen($name = trim($name))) continue;
            if (is_scalar($data)) $data = array();
            $tnam = $name;
            $tdat = &$data;
            $data = &$data[$name];
        }
        assert('isset($tnam)');
        $tdat[$tnam] = &$value;
    }

    public static function addp (&$data, $path, $value, $key = null, $ts='/') {
        $data = &self::refp($data, $path, $ts);
        settype($data, 'array');
        if (isset($key)) $data[$key] = $value;
        else $data[] = $value;
    }

    public static function addrefp (&$data, $path, &$value, $key = null, $ts='/') {
        $data = &self::refp($data, $path, $ts);
        settype($data, 'array');
        if (isset($key)) $data[$key] =& $value;
        else $data[] =& $value;
    }

    /**
     * Get $array[$key] if it's set, $default otherwise.
     * @param  array|ArrayAccess $array
     * @param  mixed $key
     * @param  mixed $default
     * @return mixed
     */
    public static function take ($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : (
            is_callable($default) ?
                call_user_func($default, $key) :
                $default
        );
    }

    /**
     * Get $array[$key] if it's set, or failed assertion otherwise.
     * @param type $array
     * @param type $key
     * @return type
     */
    public static function required ($array, $key) {
        assert("isset(\$array['$key'])");
        return $array[$key];
//        if (isset($array[$key])) return $array[$key];
//        trigger_error("'$key' not found", E_USER_ERROR);
    }

    /**
     * Get and unset $array[$key] if it's set, return $default otherwise.
     * @param type $array
     * @param type $key
     * @param type $default
     * @return type
     */
    public static function extract (&$array, $key, $default = null) {
        if (!isset($array[$key])) return $default;
        $result = $array[$key];
        unset($array[$key]);
        return $result;
    }

    //---=====[ data sources ]=====---//

    /**
     * Parse key=vals string (kvs).
     * @param  string $kvs
     * @return array
     */
    public static function kvs ($kvs) {
        if (preg_match_all('/^([^=\n\r]+)\s*=\s*(.*)$/m',
            $kvs, $m, PREG_PATTERN_ORDER)) {
            return array_combine(
                array_map('trim',$m[1]),
                array_map('trim',$m[2])
            );
        }
        return array();
    }

    /**
     * Parse ini-file.
     * @param  string $filename
     * @param  bool   $sections
     * @return array
     */
    public static function ini ($filename, $sections = true) {
        return parse_ini_file($filename, $sections);
    }

    //---=====[ Interface implementation ]=====---//

    public function current () {
        return current($this->data);
    }
    public function key () {
        return key($this->data);
    }
    public function next () {
        return next($this->data);
    }
    public function rewind () {
        reset($this->data);
    }
    public function valid () {
        return key($this->data) !== null;
    }
    public function count () {
        return count($this->data);
    }
    public function offsetSet($offset, $value) {
        $this->data[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->data[$offset]) ?
            $this->data[$offset] : null;
    }
}


