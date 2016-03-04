<?php

$v  = $_data->get();

$v1 = AIIXData::getp($_array, 'a');
$v2 = $_data->get('a');

$v3 = AIIXData::getp($_array, 'a/x');
$v4 = $_data->get('a/x');

$v5 = $_data->get('a/u', 456);
$v6 = $_data->get('a/u/z', '789');

$v7 = $_data->get(' a / x ', '???');

return $this->is_true(
    $v3 === $v4,
    $v3 === $v7
);