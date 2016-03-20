<?php namespace Validation;

class Custom {
    function validate_custom ($path, $filtered, $args) {
        echo "path:  $path\n";
        echo "value: ";var_export($filtered);echo"\n";
        echo "args:  ";var_export($args);echo"\n";
        $valid_if_true = $filtered === $args;
        \AIIX\Form::msgIfFalse($valid_if_true, $path, 123, 'custom key');
    }
}

$valid = array();

for ($_i = 0; $_i <= 2; ++$_i) {
    $valid["$_i.0"] = \AIIX\Form::validate("validate$_i.0");
    $valid["$_i.1"] = \AIIX\Form::validate("validate$_i.1");
}

$controller = new Custom;
for (; $_i <= 5; ++$_i) {
    $valid["$_i.0"] = \AIIX\Form::validate("validate$_i.0", $controller);
    $valid["$_i.1"] = \AIIX\Form::validate("validate$_i.1", $controller);
}

$message = array();
for ($_i = 0; $_i <= 5; ++$_i) {
    $message["$_i.0"] = \AIIX\Form::message("validate$_i.0");
    $message["$_i.1"] = \AIIX\Form::message("validate$_i.1");
}
