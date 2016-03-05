<?php

$array2 = $array1 = $_array;
\AIIX\Data::build($array1);
\AIIX\Data::build($array2, array('@b' => 'pool'));

$data1 = new \AIIX\Data($_array);
$data2 = new \AIIX\Data($_array, true);
$data3 = new \AIIX\Data($_array, array('@b' => 'pool'));

return AIIXTest::is_true(
    $data1['a']['y']    === '@b',
    $data2['a']['y'][0] === 'zero',
    $data3['a']['y']    === 'pool'
);