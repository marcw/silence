<?php


namespace MarcW\Silence\Command;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Silence\Console\Question\ArtworkQuestion;
use MarcW\Silence\Console\Question\BoolQuestion;
use MarcW\Silence\Console\Question\DateTimeQuestion;
use MarcW\Silence\Console\Question\LanguageQuestion;
use MarcW\Silence\Console\Question\MediaQuestion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

abstract class AbstractCommand extends Command
{
    /** * @var object */
    protected $subject;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor  */
    protected $propertyAccessor;
    /** @var SymfonyQuestionHelper  */
    protected $questionHelper;
    /** @var ParameterBagInterface */
    protected $parameterBag;
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->parameterBag = $parameterBag;
        $this->validator = $validator;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

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

    protected function askQuestion(Question $question)
    {
        $helper = $this->getHelper('question');

        return $helper->ask($this->getInput(), $this->getOutput(), $question);
    }

    protected function askForString(string $question, string $property)
    {
        $default = $this->sanitizeDefaultString($this->propertyAccessor->getValue($this->getSubject(), $property));
        $value = $this->askQuestion(new Question($question, $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }

    protected function askForBool(string $question, string $property)
    {
        $default = $this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new BoolQuestion($question, $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }

    protected function askForDatetime(string $question, string $property)
    {
        $default = $this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new DateTimeQuestion($question, $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }

    protected function askForChoice(string $question, string $property, array $choices)
    {
        $default =$this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new ChoiceQuestion($question, $choices, $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }


    protected function askForLanguage(string $property)
    {
        $default = $this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new LanguageQuestion('In which language is this podcast?', $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }

    protected function askForAudioFile(string $property)
    {
        $default = $this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new MediaQuestion('Please provide the path to the Media file', $this->parameterBag->get('dir.public'), $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }

    protected function askForImageFile(string $property)
    {
        $default = $this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new ArtworkQuestion('Please provide the path to the Artwork file (min 1400x1400)', $this->parameterBag->get('dir.public'), $default));
        $this->propertyAccessor->setValue($this->subject, $property, $value);
    }

    protected function validateSubject(): bool
    {
        $list = $this->validator->validate($this->subject);
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
