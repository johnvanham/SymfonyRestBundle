<?php

namespace LoftDigital\RestBundle\EventListener;

use FOS\UserBundle\Model\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

/**
 * JWT Created Listener
 *
 * Lister appends data to JWT after a web token was created
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2016
 * @package LoftDigital\RestBundle\EventListener
 */
class JWTCreatedListener
{
    /**
     * Add data after JWT was created
     *
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $event->getRequest();
        if ($request === null) {
            return;
        }

        $user = $event->getUser();

        $payload = $event->getData();
        $payload['id'] = $user instanceof User ? $user->getId() : null;

        $event->setData($payload);
    }
}
