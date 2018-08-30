<?php


namespace MarcW\Silence\Console\Question;

use Symfony\Component\Console\Question\Question;

class DateTimeQuestion extends Question
{
    public function __construct(string $question, $default = null)
    {
        if ($default instanceof \DateTime) {
            $default = $default->format('Y-m-d H:i:s');
        }

        parent::__construct($question, $default);

        $this->setNormalizer(function($value) {
            if (!$value) {
                return null;
            }

            try {
                return new \DateTime($value);
            } catch(\Exception $exception) {
            }

            return null;
        });
    }
}
