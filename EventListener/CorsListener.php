<?php

namespace LoftDigital\RestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * CORS Listener
 *
 * Inject Access-Control-Expose-Headers into response header
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
 * @package LoftDigital\RestBundle\EventListener
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
        $allowHeaders = implode(', ', [
            'Accept-Ranges',
            'Content-Range',
            'Next-Range'
        ]);

        $responseHeaders = $event->getResponse()->headers;
        $responseHeaders->set('Access-Control-Expose-Headers', $allowHeaders);
    }
}
