<<<<<<< HEAD
# aiix.php #

```php

$data = new AIIXdata($array);
$data->set('key1/key2', 'value1')->set('key3', 'value2');
$value = $data->get('key1/key2', 'default');

```

```php

AIIXForm::create($formdata);

----------------------------

echo AIIXForm::label('firstname');
echo AIIXForm::control('firstname');

```

more: https://github.com/ailixter/aiix.php/wiki
=======
# aiix.php

```
$data = new AIIXdata($array);
$data->set('key1/key2', 'value1')->set('key3', 'value2');
$value = $data->get('key1/key2', 'default');
```

```
AIIXForm::create($formdata);

----------------------------

AIIXForm::label('firstname');
AIIXForm::control('firstname');
```
>>>>>>> aa6afc1... Update README.md
