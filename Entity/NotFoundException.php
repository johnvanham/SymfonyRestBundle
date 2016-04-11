<?php

namespace LoftDigital\SymfonyRestBundle\Entity;

use Exception;

/**
 * Not Found Exception
 *
 * Exception returned when the resource was not found
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
 * @package LoftDigital\SymfonyRestBundle\Entity
 */
class NotFoundException extends Exception
{
    /**
     * Constructor
     *
     * @param string $message
     */
    public function __construct($message = 'Resource was not found.')
    {
        parent::__construct($message);
    }
}
