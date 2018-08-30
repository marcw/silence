<?php


namespace MarcW\Silence\Console\Question;


use Symfony\Component\Console\Question\ChoiceQuestion;

class LanguageQuestion extends ChoiceQuestion
{
    public function __construct(string $question, $default = null)
    {
        $languages = json_decode(file_get_contents(__DIR__.'/../../Resources/language.json'), true);
        $choices = array_combine(array_values($languages), array_values($languages));

        parent::__construct($question, $choices, $default);
    }
}
