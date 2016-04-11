<?php

namespace LoftDigital\SymfonyRestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use LoftDigital\SymfonyRestBundle\Model\HttpStatus;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * REST Controller
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\Controller
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
     * The request could not be understood by the server due to malformed
     * syntax. The client SHOULD NOT repeat the request without modifications.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/400_-_Bad_Request
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusBadRequest($message = null)
    {
        if ($message === null) {
            $message = 'Request invalid, validate usage and try again.';
        }

        return $this->view(
            [
                'id' => (new HttpStatus())->getIdForStatusCode(400),
                'message' => $message
            ],
            400
        );
    }

    /**
     * 422 - Unprocessable Entity
     *
     * Server understands the content type of the request entity, and the syntax
     * of the request entity is correct but was unable to process the contained
     * instructions.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/422_-_Unprocessable_Entity
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusUnprocessableEntity($message = null)
    {
        if ($message === null) {
            $message = 'Request failed, validate parameters and try again.';
        }

        return $this->view(
            [
                'id' => (new HttpStatus())->getIdForStatusCode(422),
                'message' => $message],
            422
        );
    }

    /**
     * 404 - Not Found
     *
     * The server has not found anything matching the Request-URI.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/404_-_Not_Found
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusNotFound($message = null)
    {
        if ($message === null) {
            $message = 'Request failed, the specified resource does not exist.';
        }

        return $this->view(
            [
                'id' => (new HttpStatus())->getIdForStatusCode(404),
                'message' => $message
            ],
            404
        );
    }

    /**
     * 409 - Conflict
     *
     * The request could not be completed due to a conflict with the current
     * state of the resource.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/409_-_Conflict
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusConflict($message = null)
    {
        if ($message === null) {
            $message = 'Request failed, the resource already exist.';
        }

        return $this->view(
            [
                'id' => (new HttpStatus())->getIdForStatusCode(409),
                'message' => $message
            ],
            409
        );
    }

    /**
     * 403 - Forbidden
     *
     * The server understood the request, but is refusing to fulfill it.
     * Authorization will not help and the request SHOULD NOT be repeated.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/403_-_Forbidden
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusForbidden($message = null)
    {
        if ($message === null) {
            $message = 'Request forbidden, no access to resource.';
        }

        return $this->view(
            [
                'id' => (new HttpStatus())->getIdForStatusCode(403),
                'message' => $message
            ],
            403
        );
    }

    /**
     * 405 - Method Not Allowed
     *
     * The method specified in the Request-Line is not allowed for the resource
     * identified by the Request-URI. The response MUST include an Allow header
     * containing a list of valid methods for the requested resource.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/405_-_Method_Not_Allowed
     *
     * @param string|null $message
     *
     * @return View
     */
    public function statusMethodNotAllowed($message = null)
    {
        if ($message === null) {
            $message = 'Request not allowed.';
        }

        return $this->view(
            [
                'id' => (new HttpStatus())->getIdForStatusCode(405),
                'message' => $message
            ],
            405
        );
    }

    /**
     * 200 - OK
     *
     * The request has succeeded. The information returned with the response
     * is dependent on the method used in the request.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/200_-_OK
     *
     * @param array|object|string $object
     *
     * @return View
     */
    public function statusOk($object)
    {
        return $this->view($object, 200);
    }

    /**
     * 201 - Created
     *
     * The request has been fulfilled and resulted in a new resource being
     * created.
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

    /**
     * 204 - No Content
     *
     * The server has fulfilled the request but does not need to return
     * an entity-body, and might want to return updated metainformation.
     * The response MAY include new or updated metainformation in the form
     * of entity-headers, which if present SHOULD be associated with
     * the requested variant.
     *
     * @link http://www.restpatterns.org/HTTP_Status_Codes/204_-_No_Content
     *
     * @return View
     */
    public function statusNoContent()
    {
        return $this->view(null, 204);
    }

    /**
     * Process form errors
     *
     * @param FormErrorIterator $formErrors
     *
     * @return View
     */
    protected function processFormValidationError(FormErrorIterator $formErrors)
    {
        $invalidFields = [];
        foreach ($formErrors as $error) {
            /** @var ConstraintViolation $violation */
            $violation = $error->getCause();

            if ($violation->getConstraint() instanceof UniqueEntity) {
                return $this->statusConflict();
            }

            $invalidFields[] = [
                'field' => $this->getValidationErrorField(
                    $violation->getPropertyPath()
                ),
                'message' => $error->getMessage(),
            ];
        }

        $message = $invalidFields;
        if (empty($invalidFields)) {
            $message = 'Form doesn\'t contain all required fields';
        }

        return $this->statusBadRequest([
            'invalid_fields' => $message,
        ]);
    }

    /**
     * Process violation errors
     *
     * @param ConstraintViolationList $violations
     *
     * @return View
     */
    protected function processValidationErrors(
        ConstraintViolationList $violations
    ) {
        $invalidFields = [];
        foreach ($violations->getIterator() as $violation) {
            $invalidFields[] = [
                'field' => $this->getValidationErrorField(
                    $violation->getPropertyPath()
                ),
                'message' => $violation->getMessage()
            ];
        }

        return $this->statusBadRequest([
            'invalid_fields' => $invalidFields,
        ]);
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getValidationErrorField($property)
    {
        $property = strpos($property, 'data.') === 0
            ? substr($property, 5)
            : $property;

        return ltrim(
            strtolower(preg_replace('/[A-Z]/', '_$0', $property)),
            '_'
        );
    }
}
