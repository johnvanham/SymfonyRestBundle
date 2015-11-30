<?php

namespace LoftDigital\RestBundle\EventListener;

use Doctrine\ORM\Query\QueryException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use LoftDigital\RestBundle\Model\HttpStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Exception Listener
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\EventListener
 */
class ExceptionListener
{
    /** @var ViewHandler */
    protected $controller;

    /** @var null|LoggerInterface  */
    protected $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param ViewHandler $controller
     * @param LoggerInterface|null $logger
     */
    public function __construct($controller, LoggerInterface $logger = null)
    {
        $this->controller = $controller;
        $this->logger = $logger;
    }

    /**
     * Handle kernel exception
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $message = $exception->getMessage();
        $code = 400;

        if ($exception instanceof QueryException) {
            $message = 'Request invalid, validate usage and try again.';
        }

        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        }

        $event->setResponse(
            $this->controller->handle(
                new View(['id' => (new HttpStatus())->getIdForStatusCode($code), 'message' => $message], $code),
                $event->getRequest()
            )
        );
    }
}
