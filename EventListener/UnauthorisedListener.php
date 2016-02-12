<?php

namespace LoftDigital\SymfonyRestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

/**
 * Unauthorised Listener
 *
 * Symfony returns HTTP 401 header for both invalid token and unauthorised
 * request. This listener throws exception for expired tokens. The exception is
 * processed in Exception Listener, where original header and message are
 * amended.
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
 * @package LoftDigital\SymfonyRestBundle\EventListener
 */
class UnauthorisedListener
{
    /**
     * Kernel response listener
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response->getContent() === '' && $response->getStatusCode() === 401) {
            throw new CredentialsExpiredException('Credentials have expired.');
        }
    }
}
