<?php

namespace LoftDigital\RestBundle\EventListener;

use JMS\Serializer\Serializer;
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
    protected $serializer;

    /**
     * Constructor
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

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

        $payload = $event->getData();
        $payload['user'] = $this->serializer->serialize($event->getUser(), 'json');

        $event->setData($payload);
    }
}
