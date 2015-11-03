<?php

namespace LoftDigital\RestBundle\Model;

/**
 * Response Interface
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Model
 */
interface ResponseInterface
{
    /**
     * Returns response data
     *
     * @return array
     */
    public function getData();

    /**
     * Returns response headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Returns response status code
     *
     * @return int
     */
    public function getStatusCode();
}
