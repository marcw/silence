<?php


namespace MarcW\Podcast\Command;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ValidatorBuilder;

abstract class AbstractCommand extends Command
{
    /** * @var object */
    protected $subject;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    protected function setSubject($subject)
    {
        $this->subject = $subject;
    }

    protected function getSubject()
    {
        return $this->subject;
    }

    protected function askForString(string $property)
    {
        $helper = $this->getHelper('question');
        $pa = PropertyAccess::createPropertyAccessor();

        $question = new Question(ucfirst(Inflector::camelize($property)), $this->sanitizeDefaultString($pa->getValue($this->getSubject(), $property)));
        $value = $helper->ask($this->getInput(), $this->getOutput(), $question);

        $pa->setValue($this->subject, $property, $value);
    }

    protected function askForBool(string $property)
    {
        $helper = $this->getHelper('question');
        $pa = PropertyAccess::createPropertyAccessor();

        $question = new ConfirmationQuestion(ucfirst(Inflector::camelize($property)), $pa->getValue($this->getSubject(), $property));
        $question->setValidator(function($value) {
            if (false !== $value && true !== $value) {
                return false;
            }

            return true;
        });
        $value = $helper->ask($this->getInput(), $this->getOutput(), $question);

        $pa->setValue($this->subject, $property, $value);
    }

    protected function askForChoice(string $property, array $choices)
    {
        $helper = $this->getHelper('question');
        $pa = PropertyAccess::createPropertyAccessor();

        $question = new ChoiceQuestion(ucfirst(Inflector::camelize($property)), $choices, $pa->getValue($this->getSubject(), $property));
        $value = $helper->ask($this->getInput(), $this->getOutput(), $question);

        $pa->setValue($this->subject, $property, $value);
    }

    protected function askForLanguage(string $property)
    {
        $languages = json_decode(file_get_contents(__DIR__.'/../Resources/language.json'), true);
        $this->askForChoice($property, array_values($languages));
    }

    protected function askForAudioFile(string $property)
    {
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__.'/../../public/media')
            ->name('*.mp3')
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }

    protected function askForImageFile(string $property)
    {
        $finder = new Finder();
        $finder->files()
               ->in(__DIR__.'/../../public/media')
               ->name('*.png')
               ->name('*.jpg')
               ->name('*.jpeg')
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }

    protected function validateSubject(): bool
    {
        $builder = new ValidatorBuilder();
        $builder->enableAnnotationMapping();
        $validator = $builder->getValidator();

        $list = $validator->validate($this->subject);
        if (0 === count($list)) {
            return true;
        }

        /** @var ConstraintViolationInterface $violation */
        foreach ($list as $violation) {
            $this->output->writeln(sprintf('%s: <error>%s</error>', $violation->getPropertyPath(), $violation->getMessage()));
        }

        return false;
    }

    private function sanitizeDefaultString($value): string
    {
        if (null === $value) {
            return '';
        }

        return (string) $value;
    }
}