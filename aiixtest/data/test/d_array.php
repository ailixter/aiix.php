<?php

$array = $_array;
$array['ext'] = true;

$v1 = \AIIX\Data::take($array, 'a');
$v2 = \AIIX\Data::take($array, 'zed', 'def');
$v3 = \AIIX\Data::required($array, 'a');
$v4 = \AIIX\Data::required($array, 'zed');
$v5 = \AIIX\Data::extract($array, 'zed', 'def');
$v6 = \AIIX\Data::extract($array, 'ext');

$data = new \AIIX\Data($array, true);

$v7 = count($data);
$v8 = $data['a'];

foreach ($data as $_key => $_val) {
    $$_key = $_val;
}
