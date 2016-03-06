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

for ($i = 0; $i <= 2; ++$i) {
    $valid["$i.0"] = \AIIX\Form::validate("validate$i.0");
    $valid["$i.1"] = \AIIX\Form::validate("validate$i.1");
}

$controller = new Custom;
for (; $i <= 5; ++$i) {
    $valid["$i.0"] = \AIIX\Form::validate("validate$i.0", $controller);
    $valid["$i.1"] = \AIIX\Form::validate("validate$i.1", $controller);
}

$message = array();
for ($i = 0; $i <= 5; ++$i) {
    $message["$i.0"] = \AIIX\Form::message("validate$i.0");
    $message["$i.1"] = \AIIX\Form::message("validate$i.1");
}
