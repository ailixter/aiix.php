<?php

$v1 = AIIXData::hasp($_array, 'a');
$v2 = $_data->has('a');

$v3 = AIIXData::hasp($_array, 'a/x');
$v4 = $_data->has('a/x');

$v5 = $_data->has('a/u');
$v6 = $_data->has('a/u/z');

$v7 = $_data->has(' a / x ');

return $this->is_true(
    $v3, $v4, $v3, $v4, $v7,
    !$v5, !$v6
);