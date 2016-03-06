<?php

$val1 = \AIIX\Form::filtered('option1');

echo \AIIX\Form::control('option1');
echo \AIIX\Form::label('option1');

$object_id = 123;
$val2_0 = \AIIX\Form::filtered('option2.0');

echo \AIIX\Form::control(array('option2.0', $object_id));
echo \AIIX\Form::label(array('option2.0', $object_id));

$val2_1 = \AIIX\Form::filtered('option2.1');

echo \AIIX\Form::control(array('option2.1', $object_id));
echo \AIIX\Form::label(array('option2.1', $object_id));
