<?php

namespace LoftDigital\SymfonyRestBundle\Model;

/**
 * HTTP Status
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\Model
 */
class HttpStatus
{
    /**
     * Returns status code - response ID mapping
     *
     * @return array
     */
    public function getStatusCodeIdMapping()
    {
        return [
            400 => 'bad_request',
            422 => 'invalid_params',
            404 => 'not_found',
            403 => 'forbidden',
            409 => 'conflict',
            405 => 'not_allowed',
        ];
    }

    /**
     * Returns response ID for the given status code
     *
     * @param int $statusCode
     *
     * @return string|null
     */
    public function getIdForStatusCode($statusCode)
    {
        if (array_key_exists($statusCode, $this->getStatusCodeIdMapping())) {
            return $this->getStatusCodeIdMapping()[$statusCode];
        }

        return null;
    }
}
