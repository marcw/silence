<?php


namespace MarcW\Silence\Validator;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ItunesArtworkValidator extends ConstraintValidator
{
    private $publicDir;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->publicDir = $parameterBag->get('dir.public');
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }
        $path = sprintf("%s/%s", rtrim($this->publicDir), ltrim($value));

        list ($width, $height) = getimagesize($path);

        if ($width < 1400) {
            $this->context->buildViolation($constraint->widthMessage) ->addViolation();

        }

        if ($height < 1400) {
            $this->context->buildViolation($constraint->heightMessage)->addViolation();
        }
    }
}