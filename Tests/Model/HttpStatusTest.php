<?php

namespace LoftDigital\SymfonyRestBundle\Tests\Model;

use LoftDigital\SymfonyRestBundle\Model\HttpStatus;

/**
 * Class HttpStatusTest
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\Tests\Model
 */
class HttpStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\HttpStatus::getStatusCodeIdMapping
     */
    public function testGetStatusCodeIdMapping()
    {
        $this->assertTrue(is_array((new HttpStatus())->getStatusCodeIdMapping()));
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\HttpStatus::getIdForStatusCode
     */
    public function testGetIdForStatusCode()
    {
        $status = new HttpStatus();
        foreach ($status->getStatusCodeIdMapping() as $code => $messageId) {
            $this->assertEquals($messageId, $status->getIdForStatusCode($code));
        }

        $this->assertNull($status->getIdForStatusCode(0));
    }
}
