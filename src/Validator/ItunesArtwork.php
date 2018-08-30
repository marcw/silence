<?php


namespace MarcW\Silence\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ItunesArtwork extends Constraint
{
    public $widthMessage = "This image should have a width >= 1400px";
    public $heightMessage = "This image should have a height >= 1400px";
}