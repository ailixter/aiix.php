<?php

$attrs = \AIIX\Form::attrs('test1');
echo \AIIX\Form::input($attrs);

$attrs_with_suffix = \AIIX\Form::attrs(array('test1', 'product', 'id'));
echo \AIIX\Form::input($attrs_with_suffix);

echo \AIIX\Form::control('test2');
