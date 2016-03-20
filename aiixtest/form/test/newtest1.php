<?php

echo \AIIX\Form::control('a');
echo \AIIX\Form::control('b'); // out of formdata

echo \AIIX\Form::control('x');
echo \AIIX\Form::control('z'); // out of formdata, no post data

echo \AIIX\Form::control('select.0');
echo \AIIX\Form::control('select.1');

echo \AIIX\Form::control('comment.0'); // no post data
echo \AIIX\Form::control('comment.1'); // post data provided

echo \AIIX\Form::checkbox(\AIIX\Form::attrs('c'));
echo \AIIX\Form::checkbox(\AIIX\Form::attrs('c1')); // out of formdata, no post data

for ($_i = 1; $_i <= 4; ++$_i) {
    echo "\n";
    echo \AIIX\Form::control("checkbox$_i.0"); // no post data
    echo \AIIX\Form::control("checkbox$_i.1"); // post data provided
}
