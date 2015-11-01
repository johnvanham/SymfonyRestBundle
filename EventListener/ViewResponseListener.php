<?php

namespace LoftDigital\RestBundle\EventListener;

use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\EventListener\ViewResponseListener as FosViewResponseListener;

/**
 * View response listener service updates kernel view output
 *
 * Listener is dependent on the FOSRestBundle
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\EventListener
 */
class ViewResponseListener extends FosViewResponseListener
{
    /** @var bool */
    protected $customViewDefined = true;

    /** @var View */
    protected $view;

    /** @var Request */
    protected $request;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Initializes a new response object
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @return array|mixed
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $this->setRequest($event->getRequest());
        $this->createView($event);
        $this->setRequestIdHeader();

        if ($this->view->getFormat() === null) {
            $this->view->setFormat($this->request->getRequestFormat());
        }

        $viewHandler = $this->container->get('fos_rest.view_handler');

        $response = $viewHandler->handle($this->view, $this->request);

        $event->setResponse($response);
    }

    /**
     * Creates view object
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @return array|mixed
     */
    public function createView($event)
    {
        $controllerReturnData = $event->getControllerResult();
        if ($controllerReturnData instanceof View) {
            $this->view = $controllerReturnData;

            return $this;
        }

        if (!$this->container->getParameter('fos_rest.view_response_listener.force_view')) {
            $this->view = (new TemplateListener($this->container))->onKernelView($event);

            return $this;
        }

        $this->customViewDefined = false;

        $this->view = new View($controllerReturnData);

        return $this;
    }

    /**
     * Sets unique request identifier to HTTP headers
     *
     * Each request - response should be able to identify with unique Request ID
     *
     * TODO: Implement request - response tracking
     *
     * @return void
     */
    public function setRequestIdHeader()
    {
        if (!array_key_exists('request-id', $this->view->getHeaders())) {
            $this->view->setHeader('Request-Id', uniqid());
        }
    }
}
