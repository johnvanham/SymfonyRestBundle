<?php

namespace LoftDigital\SymfonyRestBundle\EventListener;

use FOS\RestBundle\View\View;
use LoftDigital\SymfonyRestBundle\Model\ListResponse;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Process controller output data
 *
 * Listener updates a format of the {@link ListResponse} object to a format acceptable by FOS rest {@link View} object.
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\EventListener
 */
class ViewResponseListener extends TemplateListener
{
    /**
     * @param GetResponseForControllerResultEvent $event
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $event->setControllerResult($this->processResult($event->getControllerResult()));
    }

    /**
     * Process request
     *
     * @param $controllerResult
     *
     * @return View
     */
    public function processResult($controllerResult)
    {
        $response = $controllerResult;
        if ($controllerResult instanceof ListResponse) {
            $view = new View(
                $controllerResult->getData(),
                $controllerResult->getStatusCode(),
                $controllerResult->getHeaders()
            );

            $response = $view;
        }

        return $response;
    }
}
