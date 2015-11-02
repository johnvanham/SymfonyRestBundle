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
        return $this->container->get('request_stack')->getCurrentRequest()->get('offset');
    }

    /**
     * Get order for a list request
     *
     * @return string one of (asc|desc)
     */
    public function getOrder()
    {
        return $this->container->get('request_stack')->getCurrentRequest()->get('order');
    }
}
