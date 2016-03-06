<?php

foreach (\AIIX\Form::fieldsetIDs('fieldset/checks1') as $control_id) {
    echo \AIIX\Form::control($control_id);
}

