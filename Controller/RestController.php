<?php

namespace LoftDigital\RestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use LoftDigital\RestBundle\Model\HttpStatus;
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

        return $this->view(['id' => (new HttpStatus())->getIdForStatusCode(400), 'message' => $message], 400);
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

        return $this->view(['id' => (new HttpStatus())->getIdForStatusCode(422), 'message' => $message], 422);
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

        return $this->view(['id' => (new HttpStatus())->getIdForStatusCode(404), 'message' => $message], 404);
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
