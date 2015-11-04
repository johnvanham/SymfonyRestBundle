<?php

namespace LoftDigital\RestBundle\Tests\EventListener;

use FOS\RestBundle\View\View;
use LoftDigital\RestBundle\EventListener\ViewResponseListener;
use LoftDigital\RestBundle\Model\ListResponse;
use Rss\CustomerApiBundle\Entity\Customer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class ViewResponseListenerTest
 *
 * @package LoftDigital\RestBundle\Tests\EventListener
 */
class ViewResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ViewResponseListener */
    protected $listener;

    /** @var GetResponseForControllerResultEvent */
    protected $event;

    /** @var ContainerInterface */
    protected $container;

    /** @var ListResponse */
    protected $listResponse;

    /** @var Customer */
    protected $customer;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->customer = new Customer();
        $this->customer->setEmail('test@email.com');

        $listResponse = $this->getMockBuilder('\LoftDigital\RestBundle\Model\ListResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $listResponse->expects($this->any())
            ->method('getData')
            ->willReturn($this->customer);
        $listResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);
        $listResponse->expects($this->any())
            ->method('getHeaders')
            ->willReturn(array());

        $this->listResponse = $listResponse;

        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->listener = new ViewResponseListener($this->container);
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\ViewResponseListener::processResult
     */
    public function testProcessResult()
    {
        $response = $this->listener->processResult($this->listResponse);
        $view = new View(
            $this->listResponse->getData(),
            $this->listResponse->getStatusCode(),
            $this->listResponse->getHeaders()
        );

        $this->assertEquals($view, $response);
        $this->assertEquals($this->customer, $this->listener->processResult($this->customer));
    }
}
