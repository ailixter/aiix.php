<?php error_reporting(E_STRICT);

/*
 * (C) 2015, AII (Alexey Ilyin).
 */

require '../../form.php';

$form = \AIIX\Form::create(\AIIX\Form::ini('formdata.ini'));

foreach (array_keys($form->get()) as $control_id) {
    $attrs = \AIIX\Form::attrs($control_id);
    isset($attrs['-testval']) && isset($attrs['name'])
    and $_POST[$attrs['name']] = $attrs['-testval'];
}

unset($form);