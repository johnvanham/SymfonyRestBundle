<?php

namespace LoftDigital\RestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
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

    const REQUEST_RANGE = 'range';
    const REQUEST_OFFSET = 'offset';
    const REQUEST_ORDER = 'order';
    const REQUEST_MAX = 'max';

    /**
     * 400 - Bad Request
     *
     * The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat
     * the request without modifications.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/400_-_Bad_Request
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusBadRequest($message = null)
    {
        if ($message == null) {
            $message = 'Request invalid, validate usage and try again.';
        }

        return $this->view(['id' => 'bad_request', 'message' => $message], 400);
    }

    /**
     * 422 - Unprocessable Entity
     *
     * Server understands the content type of the request entity, and the syntax of the request entity is correct
     * but was unable to process the contained instructions.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/422_-_Unprocessable_Entity
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusUnprocessableEntity($message = null)
    {
        if ($message == null) {
            $message = 'Request failed, validate parameters and try again.';
        }

        return $this->view(['id' => 'invalid_params', 'message' => $message], 422);
    }

    /**
     * 404 - Not Found
     *
     * The server has not found anything matching the Request-URI.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/404_-_Not_Found
     *
     * @param null $message
     *
     * @return View
     */
    public function statusNotFound($message = null)
    {
        if ($message == null) {
            $message = 'Request failed, the specified resource does not exist.';
        }

        return $this->view(['id' => 'not_found', 'message' => $message], 404);
    }

    /**
     * 201 - Created
     *
     * The request has been fulfilled and resulted in a new resource being created.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/201_-_Created
     *
     * @param array|object $object
     *
     * @return View
     */
    public function statusCreated($object)
    {
        return $this->view($object, 201);
    }
}
