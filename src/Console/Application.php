<?php


namespace MarcW\Silence\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;

class Application extends BaseApplication
{
    protected function getDefaultHelperSet()
    {
        return new HelperSet(array(
            new FormatterHelper(),
            new DebugFormatterHelper(),
            new ProcessHelper(),
            new SymfonyQuestionHelper(),
        ));
    }
}
