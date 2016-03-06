<?php namespace I18N;

class Translator {
    static $dictionary = array(
        "Labeled Control"   => "Элемент с меткой",
        "Complex"           => "Сложный",
        "labeled5"          => "По-русски",
        "abcd"              => "АБВГ"
    );
    static function translate ($text) {
        return isset(self::$dictionary[$text]) ?
            self::$dictionary[$text] : $text;
    }
}

\AIIX\Form::setTranslator("\I18N\Translator::translate");

echo \AIIX\Form::label('labeled1');
echo \AIIX\Form::label('labeled3');
echo \AIIX\Form::label('labeled5');

echo \AIIX\Form::translate('abcd');
echo \AIIX\Form::translate('xyz');