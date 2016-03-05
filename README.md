# aiix.php #

```php

$data = new \AIIX\Data($array);
$data->set('key1/key2', 'value1')->set('key3', 'value2');
$value = $data->get('key1/key2', 'default');

```

```php

\AIIX\Form::create($formdata);

----------------------------

echo \AIIX\Form::label('firstname');
echo \AIIX\Form::control('firstname');

```

more: https://github.com/ailixter/aiix.php/wiki
