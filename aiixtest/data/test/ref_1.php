<?php

$v1 = $_array;
$d1 = new AIIXData($v1, true);

$v2 =& AIIXData::refp($v1, '@b');
$v3 =& $d1->ref('@b');

$v4 =& AIIXData::refp($v1, 'a/x');
$v5 =& $d1->ref('a/x');

$v6 =& $d1->ref('a/u/i');
$v7 =& $d1->ref('a/u/z');

foreach (array('d1','v1','v2','v3','v4','v5','v6', 'v7') as $_name) {
    AIIXTest::$SHARED[$_name] =& $$_name;
}

