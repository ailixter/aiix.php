<?php

$_a = array(true, false, 'xyz' => array('aiix'));

$v1 = $_array;
$d1 = new AIIXData($v1, true);

AIIXData::addp($v1, 'a', 'added1');
$d1->add('a', 'added2');

AIIXData::addp($v1, 'a/x', 'added3');
$d1->add('a/x', 'added4');

AIIXData::addp($v1, 'a', 'added5', 'five');
$d1->add('a', 'added6', 'six');

$d1->add('a/x', 'added7', 'seven')->add('a/y', 'added8', 'eight');

$d1->add('zzz', 'added9', 'nine');


$d1->addref('@b/x', $v10, 'ten');
$d1->addref('@b/x', $v11, 'eleven')->addref('eleven', $v11);
$v11 = 11;