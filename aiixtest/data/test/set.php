<?php

$_a = array(true, false, 'xyz' => array('aiix'));

$v1 = $_array;
$d1 = new AIIXData($v1, true);

AIIXData::setp($v1, 'c', true);
$d1->set('c', true);

AIIXData::setp($v1, 'd', $_a);
$d1->set('d', $_a);

$d1->set('e/f', 'ef');
$d1->set('e/f/g', 'efg');

$d1->set('k1/k2', 'k')->set('m1/m2', 'm');

AIIXData::setp($v1, ' multi word / key ', 'qwerty');
$d1->set(' multi word / key ', 'querty');

//echo $d1->get('d/xyz/0');

return $this->is_true(
    AIIXData::getp($v1, 'c')        === true,
    $d1->get('c')                   === true,
    AIIXData::getp($v1, 'd/xyz/0')  === 'aiix',
    $d1->get('d/xyz/0')             === 'aiix',
    AIIXData::getp($v1, ' multi word / key ') === 'qwerty',
    $d1->get(' multi word / key ')            === 'querty'
);