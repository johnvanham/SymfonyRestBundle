<?php

namespace LoftDigital\RestBundle\Tests\EventListener;

use LoftDigital\RestBundle\EventListener\RangeListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RangeListenerTest
 *
 * @package LoftDigital\RestBundle\Tests\EventListener
 */
class RangeListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::onKernelRequest
     */
    public function testOnKernelRequestDefault()
    {
        $order = 'desc';
        $max = 124;
        $offset = 22;

        $request = new Request();

        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->atLeastOnce())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $listener = new RangeListener();
        $listener->setMax($max);
        $listener->setOffset($offset);
        $listener->setOrder($order);
        $listener->onKernelRequest($event);

        $this->assertEquals($order, $request->get('order'));
        $this->assertEquals($max, $request->get('max'));
        $this->assertEquals($offset, $request->get('offset'));
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::onKernelRequest
     */
    public function testOnKernelRequestWithHeaders()
    {
        $order = 'desc';
        $max = 20;
        $offset = 2;

        $request = new Request();
        $request->headers->set('range', "name ..; order=$order,max=$max,offset=$offset;");

        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->atLeastOnce())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $listener = new RangeListener();
        $listener->onKernelRequest($event);

        $this->assertEquals($order, $request->get('order'));
        $this->assertEquals($max, $request->get('max'));
        $this->assertEquals($offset, $request->get('offset'));
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::setMax
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::getMax
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::setOffset
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::getOffset
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::getOrder
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::setOrder
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::getMaxLimit
     * @covers \LoftDigital\RestBundle\EventListener\RangeListener::setMaxLimit
     */
    public function testSettersAndGetter()
    {
        $request = new Request();

        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->atLeastOnce())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $listener = new RangeListener();
        $listener->onKernelRequest($event);

        $listener->setOffset(231);
        $listener->setMax(22);
        $listener->setMaxLimit(122);
        $listener->setOrder('asc');

        $this->assertEquals(231, $listener->getOffset());
        $this->assertEquals(22, $listener->getMax());
        $this->assertEquals(122, $listener->getMaxLimit());
        $this->assertEquals('asc', $listener->getOrder());
    }
}
