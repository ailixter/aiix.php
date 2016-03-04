<?php

$v2 = $v3 = 'qwerty';

$v4++;
$v5++;

$v6   = 456;
$v7[] = 789; // see also add()

foreach (array('d1','v1','v2','v3','v4','v5','v6','v7') as $_name) {
    unset(AIIXTest::$SHARED[$_name]);
}
