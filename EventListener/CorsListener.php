<?php

namespace LoftDigital\SymfonyRestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * CORS Listener
 *
 * Inject Access-Control-Expose-Headers into response header
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
 * @package LoftDigital\SymfonyRestBundle\EventListener
 */
class CorsListener
{
    /**
     * Kernel response listener
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $exposeHeaders = implode(', ', [
            'Accept-Ranges',
            'Content-Range',
            'Next-Range',
        ]);

        $allowHeaders = implode(', ', [
            'Accept',
            'Content-Type',
            'Range',
            'Authorization',
        ]);

        $responseHeaders = $event->getResponse()->headers;
        $responseHeaders->set('Access-Control-Expose-Headers', $exposeHeaders);
        $responseHeaders->set('Access-Control-Allow-Headers', $allowHeaders);
    }
}
