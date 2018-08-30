<?php


namespace MarcW\Silence\Console\Question;


use Symfony\Component\Console\Question\ConfirmationQuestion;

class BoolQuestion extends ConfirmationQuestion
{
    public function __construct(string $question, bool $default = true, string $trueAnswerRegex = '/^y/i')
    {
        parent::__construct($question, $default, $trueAnswerRegex);

        $this->setValidator(function($value) {
            if (false !== $value && true !== $value) {
                return false;
            }

            return true;
        });
    }
}
