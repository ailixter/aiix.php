<?php namespace Example1;
use AIIX\Form;

class Custom {

    function listObjects () {
        if (Form::submitted()) {
            $operation = Form::filtered('operation');
            list($object_id, $method) = each($operation);
            if (method_exists($this, $method)) {
                $this->{$method}($object_id);
            }
            else {
                trigger_error(__class__."::$method does not exists");
            }
        }

        $objects = $this->dbQuery('select id, name, comment, protected from t_objects');
        foreach ($objects as $object) {
            /* there are several dufferent ways to put data into
             * the form elements, but you could use the way below:
             */
            Form::choose()
                ->set('name/value',        $object['name'])
                ->set('comment/value',     $object['comment'])
                ->set('protected/checked', $object['protected'] ? 'checked' : null);

            echo "<!-- $object[id] -->\n";
            // output name & comment
            foreach (Form::fieldsetIDs('fieldset1') as $control_id) {
                echo Form::label(array($control_id, $object['id']));
                foreach (Form::message($control_id) as $message) {
                    echo "<p class=error>$message</p>";
                }
                echo Form::control(array($control_id, $object['id']));
            }
            // output checkbox, if it's for example optional
            echo Form::checkbox(Form::attrs(array('protected', $object['id'])));
            // ouput operation buttons
            echo Form::button(Form::attrs(array('save', $object['id'])));
            echo Form::button(Form::attrs(array('delete', $object['id'])));
            echo "<!-- /$object[id] -->\n";
        }
    }

    private function save ($object_id) {
        if (Form::validateAll(Form::fieldsetIDs('fieldset1'))) {
            Form::filtered("protected/$object_id"); // just to put it to filtered values
            $all_safe_data = Form::filtered();
            // use it somehow
        }
    }

    private function dbQuery ($sql) {
        return array(
            array(
                'id'        => 123,
                'name'      => 'John',
                'comment'   => null,
                'protected' => false
            ),
            array(
                'id'        => 456,
                'name'      => 'Mary',
                'comment'   => 'had a lamb',
                'protected' => true
            )
        );
    }
}

$controller = new Custom;
$controller->listObjects();
