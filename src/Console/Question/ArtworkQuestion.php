<?php


namespace MarcW\Silence\Console\Question;


use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class ArtworkQuestion extends ChoiceQuestion
{
    public function __construct(string $question, string $publicDir, $default = null)
    {
        $finder = new Finder();
        $finder->files()
            ->in($publicDir)
            ->name('*.png')
            ->name('*.jpg')
            ->name('*.jpeg')
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = str_replace($publicDir, '', $file->getPathname());
        }

        $choices = array_combine(array_values($files), array_values($files));

        parent::__construct($question, $choices, $default);
    }
}
