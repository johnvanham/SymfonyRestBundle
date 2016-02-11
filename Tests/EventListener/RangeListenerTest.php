<?php

namespace LoftDigital\SymfonyRestBundle\Tests\EventListener;

use LoftDigital\SymfonyRestBundle\EventListener\RangeListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RangeListenerTest
 *
 * @package LoftDigital\SymfonyRestBundle\Tests\EventListener
 */
class RangeListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::onKernelRequest
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
        $this->assertEquals(null, $request->get('range'));
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::onKernelRequest
     */
    public function testOnKernelRequestWithHeaders()
    {
        $order = 'desc';
        $max = 20;
        $offset = 2;
        $range = 'name';

        $request = new Request();
        $request->headers->set('range', "$range ; order=$order,max=$max,offset=$offset;");

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
        $this->assertEquals($range, $request->get('range'));
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::setMax
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::getMax
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::setOffset
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::getOffset
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::getOrder
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::setOrder
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::getMaxLimit
     * @covers \LoftDigital\SymfonyRestBundle\EventListener\RangeListener::setMaxLimit
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
