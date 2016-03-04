<?php

/*
 * (C) 2015, AII (Alexey Ilyin).
 */

require '../../data.php';

$_array = array(
    'a' => array(
        'x' => 123,
        'y' => '@b'
    ),
    '@b' => array(
        0 => 'zero',
        1 => 'one'
    )
);

$_data = new AIIXData($_array, true);

