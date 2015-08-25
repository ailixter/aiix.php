<?php
require_once __dir__.'/data.php';

class AIIXInput extends AIIXData
{
    public function __construct (array &$data) {
        parent::__construct(array(), false);
        $this->data = &$data;
        $this->ts   = '[/]';
    }

    public function filter ($var, $filter, $options) {
        $result = filter_var($var, $filter, $options);
        if ($filter == FILTER_VALIDATE_BOOLEAN) settype($result, 'string');
        return $result;
    }

    public function value ($path, $default=null,
        $filter=FILTER_DEFAULT, $options=null)
    {
        return $this->filter($this->get($path, $default), $filter, $options);
    }
}

class AIIXForm extends AIIXData
{
    /** Options. */
    protected $EOT      = "\n",
              $CANCEL   = 'cancel',
              $DEFTYPE  = 'text';


    protected  $mod;
    private $input, $output, $alerts;
    /** <code>$var = AIIXForm::choose()->input->get('var'); </code>. */
    protected function _get_input () {
        return $this->input;
    }
    protected function _get_output () {
        return $this->output;
    }
    protected function _get_alerts () {
        return $this->alerts;
    }

    /**
     *
     * <code>
     * //   Construct and choose new form to use:
     * AIIXForm::choose(new MyAIIXForm($data, new AIIXInput($_POST), '.ru');
     * </code>
     * @param array $data
     * @param AIIXInput $input
     * @param string $mod : modificator, i.e. language - '.ru'
     */
    public function __construct (array $data, AIIXInput $input, $mod=null) {
        $this->EOT      = self::extract($data, '-EOT',      $this->EOT);
        $this->CANCEL   = self::extract($data, '-CANCEL',   $this->CANCEL);
        $this->DEFTYPE  = self::extract($data, '-DEFTYPE',  $this->DEFTYPE);
        parent::__construct($data, true);
        $this->input    = $input;
        $this->output   = new AIIXData;
        $this->alerts   = new AIIXData;
        $this->mod      = $mod;
    }

    protected static $forms, $form;

    /**
     *
     * @param  array  $data
     * @param  mixed  $input : AIIXInput | (null) default input
     * @param  string $mod
     * @param  mixed  $fkey  : null | (scalar) specific form key
     * @param  string $class : form class
     * @return AIIXForm
     */
    public static function create (array $data,
        $input=null, $mod=null, $fkey=null, $class='AIIXForm')
    {
        $form = new $class($data, $input ? $input : new AIIXInput($_POST), $mod);
        return isset($fkey) ?
            self::choose(self::$forms[$fkey] = $form) :
            self::choose(self::$forms[] = $form);
    }

    /**
     *
     * @param  mixed $mix : AIIXForm | (scalar) specific form key
     * @return AIIXForm
     */
    public static function choose ($mix = null) {
        isset($mix)
        and self::$form = $mix instanceof AIIXForm ?
            $mix : self::required(self::$forms, $mix);
        return self::$form;
    }

    public static function cstr ($path = null) {
        return addcslashes(self::$form->get($path), "\0..\37\'\"");
    }

    public static function controls ($callback = null) {
        return array_filter(self::$form->data, $callback ? //???
            $callback : array(__class__, 'isControl'));
    }

    public static function controlIDs ($callback = null) {
        return array_keys(self::controls($callback));
    }

    /**
     * <code>
     *   [<id>]
     *   -control = <type>
     *   ; -dont-enumerate = false
     * </code>
     *
     * @param  array $attrs
     * @return bool
     */
    public static function isControl ($attrs) {
        return isset($attrs['-control'])
            && empty($attrs['-dont-enumerate']);
    }

    public static function fieldset ($fset) {
        $result = array();
        foreach (self::fieldsetIDs($fset) as $id) {
            $result[$id] = self::$form[$id];
        }
        return $result;
    }

    public static function fieldsetIDs ($fset) {
        $result = self::$form->get($fset);
        is_array($result) or $result = $result ?
            array_map('trim',explode(',',$result)) : array();
        ksort($result);
        return $result;
    }

    /**
     * @param mixed $mix - string | array(id, suffix, ...) where suffix is scalar | 'index/...'.
     * @return array
     */
    public static function attrs ($mix) {
        if (is_array($mix)) {
            $id = array_shift($mix);
            $suffix = $mix
            or $array = true;
        }
        else {
            $id = $mix;
            $suffix = array();
        }
        $attrs = self::take(self::$form, $id, array());
        isset($array) && !isset($attrs['-array'])
        and $attrs['-array'] = true;
        isset($attrs['-suffix'])
        and array_unshift($suffix, $attrs['-suffix']);
        $attrs['-suffix'] = $suffix = join('/', array_filter($suffix,'strlen'));
        isset($attrs['id'])    or $attrs['id']    = $id;
        isset($attrs['name'])  or $attrs['name']  = $attrs['id'];
        isset($attrs['-path']) or $attrs['-path'] = join('/',
                                  array_filter(array($attrs['name'], $suffix),'strlen'));
        return $attrs;
    }

    //---=====[ INPUT ]=====---//

    protected static function readInput ($attrs, &$dst) {
        return $accepted = self::$form->accept($attrs)
        and !is_null($value = self::$form->filter($attrs))
        and $dst = $value;
    }

    /**
     * By default ok to accept anything if !input[$this->CANCEL]
     * assuming cancel's attrs have empty name suffix.
     * @param  mix $attrs - should handle null from submitted().
     * @return bool
     */
    protected function accept ($attrs) {
        return $this->input->data && !$this->filter(array(
            '-path' => $this->CANCEL
        ));
    }

    /**
     *
     * @return bool
     */
    public static function submitted () {
        return self::$form->accept(null);
    }
    /**
     *
     * @param mixed $mix
     * @return string
     */
    public static function filtered ($mix = null) {
        return isset($mix) ?
            self::$form->filter(self::attrs($mix)) :
            self::$form->output;
    }

    protected function filter ($attrs) {
        $path   = $attrs['-path'];
        $result = $this->output->get($path, null);
        if (!is_null($result)) {
            //  already filtered.
            return $result;
        }

        $result =& $this->input->ref($path);
        is_null($result) and $result = self::take($attrs, '-default');

        $filter = self::take($attrs, '-filter', FILTER_DEFAULT);
        if (!$filter || $filter === FILTER_DEFAULT && is_null($result)) {
            //  no filter. the result as is.
            $this->output->setref($path, $result);
            return $result;
        }

        //  a filter is specified
        //  TODO custom filters, i.e. 'must-exist'
        if (is_scalar($filter)) {
            //  when -filter = FILTER_ID
            $result = $this->input->filter($result, $filter, null);
            $this->checkError($result, $path, $filter, $filter);
        }
        else foreach ($filter as $fid => $options) {
            //  when -filter[FILTER_ID] = options
            if ($fid === FILTER_CALLBACK) parseCallbackOptions ($options);
            $result = $this->input->filter($result, $fid, $options);
            $this->checkError($result, $path, $fid, $fid);
        }

        $this->output->setref($path, $result);
        return $result;
    }

    protected function parseCallbackOptions (&$options) {
        $tmp = isset($options['options']) ?
                     $options['options'] : $options;
        if (count($tmp = explode('::', $tmp))) {
            switch ($tmp[0]) {
                case '$this': $tmp[0] = $this;
            }
            $tmp = array($tmp[0], $tmp[1]);
        }
        settype($options, 'array');
        $options['options'] = $tmp;
    }

    public static function dumpInput () {
        var_dump(self::$form->input->data);
    }

    /**
     * It's ought to be named "input()", but the latter is already in use.
     * @param type $mix
     * @return type
     */
    public static function requested ($mix=null) {
        return isset($mix) ?
            self::$form->input->get($mix) :
            self::$form->input;
    }

    //---=====[ VALIDATION and ERROR HANDLING ]=====---//

    protected function checkError ($result, $path, $error, $idx=null) {
        if ($result === false) {
            $this->alerts->add($path, $error, $idx);
            return 1;
        }

        if (is_array($result)) {
            $errcount = 0;
            foreach ($result as $idx => $value) {
                $errcount += $this->checkError($value, $path, $error, $idx);
            }
            return $errcount;
        }

        return 0;
    }

    /**
     * Return stored message data.
     * <code>
     * -message.mod[ERROR|FILTER_ID] = "Error message"
     * </code>
     */

    public static function message ($id) {
        $result = array();
        $attrs  = self::attrs($id);
        if (!($alerts = self::$form->alerts->get($attrs['-path']))) return $result;
        $messages = self::take($attrs, "-message".self::$form->mod);//TODO message pool ???
        foreach ((array)$alerts as $ekey => $err) {
            $msg = self::take($messages, $ekey, "{-label} $ekey ".print_r($err,1));//todo ??? method
            foreach ($attrs as $akey => $val) if (is_scalar($val)) {
                $msg = str_replace("{{$akey}}", $val, $msg);
            }
            $result[$ekey] = vsprintf($msg, (array)$err);
        }
        return $result;
    }

    public static function validateAll ($ids, $controller = null) {
        $failed = 0;//TODO use alerted() ???
        foreach ($ids as $id) {
            AIIXForm::validate($id, $controller) or ++$failed;
        }
        return !$failed;
    }

    public static function validate ($id, $controller = null, $force = null) {
        $attrs = self::attrs($id);
        if (self::alerted($path = $attrs['-path'])) return false;//FIX logic
        $filtered = self::$form->filter($attrs);
        if (($methods = $force ? $force : self::take($attrs, '-validate'))) {
            is_scalar($methods) and $methods = array($methods => null);
            foreach ($methods as $method => $args) {
                $method = 'validate_'.strtr($method, '.-', '__');
                if (is_callable($callback = array($controller, $method))) {
                    self::callValidator($callback, $path, $filtered, $args);
                }//TODO else ???
                if (is_callable($callback = array(self::$form, $method))) {
                    self::callValidator($callback, $path, $filtered, $args);
                }//TODO else errror ???
            }
        }
        return !self::alerted($path);
    }

    private static function callValidator ($callback, $path, &$filtered, $args) {
        if (is_scalar($filtered)) {
            call_user_func($callback, $path, $filtered, $args);
        }
        else foreach ($filtered as $idx => &$value) {//!!!
            self::callValidator($callback, $path."/$idx", $value, $args);
        }
    }

    /**
     *
     * @param type $mix
     * @param type $count
     * @return mixed count(alerts) | alerts
     */
    public static function alerted ($mix=null, $count=true) {
        $result = isset($mix) ?
            self::$form->alerts->get($mix) :
            self::$form->alerts;
        $count and $result = count($result);
        return $result;
    }
    /**
     *
     * @param bool $result
     * @param type $path
     * @param type $error
     * @param type $idx
     * @return int - error count
     */
    public static function msgIfFalse ($result, $path, $error, $idx=null) {
        return self::$form->checkError($result, $path, $error, $idx);
    }

    protected function validate_required ($path, $filtered, $arg) {
        $this->checkError(strlen($filtered) > 0, $path, $arg, 'required');
    }

    protected function validate_longer ($path, $filtered, $min) {
        $this->checkError(strlen($filtered) >= $min, $path, $min, 'shorter');
    }

    protected function validate_shorter ($path, $filtered, $max) {
        $this->checkError(strlen($filtered) <= $max, $path, $max, 'longer');
    }

    protected function validate_thesame ($path, $filtered, $path2) {
        $filtered2 = $this->filter(array('-path' => $path2));
        $this->checkError($filtered === $filtered2, $path, $path2, 'is different from');
    }

    protected function validate_not ($path, $filtered, $arg) {
        $this->checkError($filtered != $arg, $path, $arg, 'is');
    }

    //---=====[ HTML ]=====---//

    /**
     *
     * @param  string $tag
     * @param  mixed  $attrs  : array | string
     * @param  mixed  $nested : string | array
     * @return string
     */
    public static function tag ($tag, $attrs = '', $nested = null) {
        $result = "<$tag";
        if (is_string($attrs)) {
            $result .= ' '.$attrs;
        }
        else {
            $suffix = self::extract($attrs, '-suffix', '');
            $array  = self::extract($attrs, '-array', false);
            foreach ($attrs as $key => $val) {
                if (!isset($val)) continue;
                if (ord($key[0]) < 0x30) continue;
                switch ($key) {
                case 'class':
                    is_array($val)
                    and $val = join(' ',$val);
                    break;
                case 'style':
                    if (is_array($val)) {
                        $tmps = '';
                        foreach ($val as $skey => $sval) {
                            $tmps .= "$skey:$sval;";
                        }
                        $val = $tmps;
                    }
                    break;
                case 'name':
                    $val .= self::nameSuffix($suffix);
                    $array and $val .= '[]';
                    break;
                case 'id':
                    $val .= self::idSuffix($suffix);
                    break;
                }
                $val = str_replace('"', '&quot;', $val);
                $result .= " $key=\"$val\"";
            }
        }

        if (!is_array($nested))
            $tmps = $nested;
        else {
            $tmps = '';// @todo: use array_reduce
            foreach ($nested as $arg) {
                $tmps .= (is_array($arg) ?
                    self::tag($arg[0], self::take($arg,1), self::take($arg,2)) :
                    (string)$arg);
            }
        }
        return $result.(isset($tmps) ? ">$tmps</$tag>" : '/>').self::$form->EOT;
    }

    protected static function nameSuffix ($suffix) {
        if (!strlen($suffix)) return '';
        return '['.(is_array($suffix) ?
            join('][', $suffix) : str_replace('/', '][', $suffix)).']';
    }

    protected static function idSuffix ($suffix) {
        if (!strlen($suffix)) return '';
        return '_'.(is_array($suffix) ?
            join('_', $suffix) : str_replace('/', '_', $suffix));
    }

    public static function label ($id) {
        $attrs  = self::attrs($id);
        if (self::take($attrs, '-control') == 'hidden') return null;
        $id     = self::take($attrs, 'id');
        $suffix = self::take($attrs, '-suffix', '');
        $for    = $id.self::idSuffix($suffix);
        strlen($suffix) and $suffix = "/$suffix";
        $ldata  = self::extract($attrs, "-label-data");
        $label  = self::extract($attrs, "-label$suffix".self::$form->mod);
        is_array($label) // check for linked label
        and $ldata = array_merge((array)$ldata, $label)
        and $label = self::extract($ldata, "-text$suffix".self::$form->mod);
        strlen($label) or $label = $attrs['id'];
        $ldata['for'] = $for; // link label to control
        return self::tag('label', $ldata, $label);
    }

    /**
     * AIIXForm::control($id, ...);
     * @param  ...
     * @return string
     */
    public static function control () {
        $args  = func_get_args();
        $id    = array_shift($args);
        $attrs = self::attrs($id);
        $type  = self::take($attrs, '-control', self::$form->DEFTYPE)
        or trigger_error("no field type specified for ".print_r($id,1),E_USER_ERROR);
        array_unshift($args, $attrs);
        return call_user_func_array(array(__class__,$type),$args);
    }

    //---=====[ HTML: CONTROLS ]=====---//

    /**
     * Usage:
     * <code>
     * echo AIIXForm::input(array('type'=>'???', 'name'=>'???', ...));
     * </code>
     */

    public static function button ($attrs) {
        $label = self::extract($attrs, "-label".self::$form->mod, $attrs['id']);
        isset($attrs['value']) or $attrs['value'] = '1';
        return self::tag('button', $attrs, $label);
    }

    public static function inputbutton ($attrs) {
        $label = self::extract($attrs, "-label".self::$form->mod, $attrs['id']);
        isset($attrs['value']) or $attrs['value'] = '1';
        return self::tag('input', $attrs, $label);
    }

    public static function input ($attrs) {
        self::readInput($attrs, $attrs['value']);
        return self::tag('input', $attrs);
    }

    public static function hidden ($attrs) {
        self::readInput($attrs, $attrs['value']);
        $attrs['type'] = 'hidden';
        return self::tag('input', $attrs);
    }

    public static function text ($attrs) {
        self::readInput($attrs, $attrs['value']);
        $attrs['type'] = 'text';
        return self::tag('input', $attrs);
    }

    public static function password ($attrs) {
        self::readInput($attrs, $attrs['value']);
        $attrs['type'] = 'password';
        return self::tag('input', $attrs);
    }

    public static function file ($attrs) {
        $attrs['type'] = 'file';
        return self::tag('input', $attrs);
    }

    public static function textarea ($attrs) {
        $text = self::extract($attrs, 'value');
        self::readInput($attrs, $text);
        return self::tag('textarea', $attrs, (string)$text);
    }

    public static function checkbox ($attrs) {
        $value = self::take($attrs, 'value', 'on');
        if (self::readInput($attrs, $input) and !is_null($input)) {
            if (!strlen($input)) {
                unset($attrs['checked']);
            }
            else if ($input === $value) {
                $attrs['checked'] = true;
            }
            else {
                trigger_error("'$input' != '$value'");
            }
        }
        empty($attrs['checked'])
        or $attrs['checked'] = 'checked';
        $attrs['type'] = 'checkbox';
        return self::tag('input', $attrs);
    }

    public static function radio ($attrs) {
        $input = null;
        if (self::readInput($attrs, $input)) {
            if ($input === self::take($attrs, 'value', 'on')) {
                $attrs['checked'] = true;
            }
            else {
                unset($attrs['checked']);
            }
        }
        empty($attrs['checked'])
        or $attrs['checked'] = 'checked';
        $attrs['type'] = 'radio';
        return self::tag('input', $attrs);
    }

    public static function select ($attrs) {
        $selected = self::extract($attrs, 'value');
        self::readInput($attrs, $selected);
        $options = self::required($attrs, "-options".self::$form->mod);
        if (is_scalar($options)) $options = explode('||', $options);
        $html = self::$form->EOT;
        foreach ($options as $val => $data) {
            if (is_scalar($data)) {
                $label = $data;
                $data = array();
            }
            else {
                $label = self::extract($data, "-label".self::$form->mod);
            }
            settype($val, 'string');
            if ($val === "''") $val = '';
            if (is_array($selected) && array_search($val, $selected) !== false
            ||  $selected == $val) {
                $data['selected'] = 'selected';
            }
            else {
                unset($data['selected']);
            }
            $data['value'] = $val;
            $html .= self::tag('option', $data, $label);
        }
        isset($attrs['multiple']) and $attrs['-array'] = true;
        return self::tag('select', $attrs, $html);
    }
}

