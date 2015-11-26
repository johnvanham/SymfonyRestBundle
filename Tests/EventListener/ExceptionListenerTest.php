<?php

namespace LoftDigital\RestBundle\Tests\EventListener;

use Doctrine\ORM\Query\QueryException;
use FOS\RestBundle\View\View;
use LoftDigital\RestBundle\EventListener\ExceptionListener;
use LoftDigital\RestBundle\Handler\UnsupportedRangeFormatException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ExceptionListenerTest
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Tests\EventListener
 */
class ExceptionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        $request = new Request();

        $event1 = new GetResponseForExceptionEvent(
            new TestKernel(),
            $request,
            'foo',
            new UnsupportedRangeFormatException('Unsupported range title')
        );

        $event2 = new GetResponseForExceptionEvent(
            new TestKernel(),
            $request,
            'foo',
            new QueryException('Query exception')
        );

        return [[$event1, $event2]];
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\ExceptionListener::onKernelException
     *
     * @dataProvider provider
     *
     * @param GetResponseForExceptionEvent $event1
     * @param GetResponseForExceptionEvent $event2
     */
    public function testOnKernelException($event1, $event2)
    {
        $response = new Response('Test response');

        $viewHandler  = $this->getMock('\FOS\RestBundle\View\ViewHandler');
        $viewHandler->expects($this->any())
            ->method('handle')
            ->willReturn($response);

        /** @var ExceptionListener|\PHPUnit_Framework_MockObject_MockObject $listener */
        $listener = $this->getMockBuilder('\LoftDigital\RestBundle\EventListener\ExceptionListener')
            ->setConstructorArgs([$viewHandler])
            ->setMethods(['getView'])
            ->getMock();

        $listener->expects($this->any())
            ->method('getView')
            ->willReturn(new View('test view'));

        $listener->onKernelException($event1);
        $this->assertEquals($response->getContent(), $event1->getResponse()->getContent());

        $response = new Response('Test response 2');

        $viewHandler  = $this->getMock('\FOS\RestBundle\View\ViewHandler');
        $viewHandler->expects($this->any())
            ->method('handle')
            ->willReturn($response);

        /** @var ExceptionListener|\PHPUnit_Framework_MockObject_MockObject $listener */
        $listener = $this->getMockBuilder('\LoftDigital\RestBundle\EventListener\ExceptionListener')
            ->setConstructorArgs([$viewHandler])
            ->setMethods(['getView'])
            ->getMock();

        $listener->expects($this->any())
            ->method('getView')
            ->willReturn(new View('test view'));

        $listener->onKernelException($event2);
        $this->assertEquals($response->getContent(), $event2->getResponse()->getContent());
    }
}
