<?php

$array = $_array;
$array['ext'] = true;

$v1 = AIIXData::take($array, 'a');
$v2 = AIIXData::take($array, 'zed', 'def');
$v3 = AIIXData::required($array, 'a');
$v4 = AIIXData::required($array, 'zed');
$v5 = AIIXData::extract($array, 'zed', 'def');
$v6 = AIIXData::extract($array, 'ext');

$data = new AIIXData($array, true);

$v7 = count($data);
$v8 = $data['a'];

foreach ($data as $_key => $_val) {
    $$_key = $_val;
}
