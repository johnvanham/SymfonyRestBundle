<?php

namespace LoftDigital\RestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestController
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Controller
 */
class RestController extends FOSRestController
{
    /** @var Request */
    protected $request;

    /**
     * @return Request
     */
    public function getRequest()
    {
        if ($this->request) {
            return $this->request;
        }

        $this->request = $this->container->get('request_stack')->getCurrentRequest();

        return $this->request;
    }

    /**
     * Get maximum range for a list request
     *
     * @return int
     */
    public function getMax()
    {
        return $this->container->get('request_stack')->getCurrentRequest()->get('max');
    }

    /**
     * Get offset for a list request
     *
     * @return int
     */
    public function getOffset()
    {
        return (int) $this->getRequest()->get('offset');
    }

    /**
     * Get order for a list request
     *
     * @return string one of (asc|desc)
     */
    public function getOrder()
    {
        return $this->getRequest()->get('order');
    }

    /**
     * Get range for a list request
     *
     * @return mixed|null
     */
    public function getRange()
    {
        return $this->getRequest()->get('range');
    }
}
