<?php

namespace LoftDigital\SymfonyRestBundle\Controller;

/**
 * Interface UserResponseRestrictionInterface
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
 * @package LoftDigital\SymfonyRestBundle\Controlle
 */
interface UserResponseRestrictionInterface
{
    /**
     * Set logged in user to handler class
     */
    public function setUser();
}
