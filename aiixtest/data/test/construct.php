<?php

$array2 = $array1 = $_array;
AIIXData::build($array1);
AIIXData::build($array2, array('@b' => 'pool'));

$data1 = new AIIXData($_array);
$data2 = new AIIXData($_array, true);
$data3 = new AIIXData($_array, array('@b' => 'pool'));

return AIIXTest::is_true(
    $data1['a']['y']    === '@b',
    $data2['a']['y'][0] === 'zero',
    $data3['a']['y']    === 'pool'
);