<?php

namespace LoftDigital\RestBundle\Tests\EventListener;

use LoftDigital\RestBundle\EventListener\ViewResponseListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class ViewResponseListenerTest
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Tests\EventListener
 */
class ViewResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $viewHandler;

    /** @var ContainerInterface */
    protected $container;

    /** @var ViewResponseListener */
    protected $listener;

    public function setUp()
    {
        $this->viewHandler = $this->getMock('FOS\RestBundle\View\ViewHandlerInterface');
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->listener = new ViewResponseListener($this->container);
    }

    /**
     * @param Request $request
     * @param mixed   $result
     *
     * @return \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent|
     *          \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResponseEvent(Request $request, $result)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->atLeastOnce())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $event->expects($this->any())
            ->method('getControllerResult')
            ->will($this->returnValue($result));

        return $event;
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\ViewResponseListener::setRequest
     * @covers \LoftDigital\RestBundle\EventListener\ViewResponseListener::getRequest
     */
    public function testGetSetRequest()
    {
        $request = new Request();
        $request->setRequestFormat('test-format');
        $this->assertInstanceOf(
            'LoftDigital\RestBundle\EventListener\ViewResponseListener',
            $this->listener->setRequest($request)
        );
        $this->assertEquals($request, $this->listener->getRequest());
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\ViewResponseListener::onKernelView
     * @covers \LoftDigital\RestBundle\EventListener\ViewResponseListener::getView
     */
    public function testOnKernelView()
    {
        $request = new Request();
        $request->setRequestFormat('json');
        $response = new Response();

        $view = $this->getMockBuilder('FOS\RestBundle\View\View')
            ->disableOriginalConstructor()
            ->getMock();
        $view->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue(array()));
        $view->expects($this->once())
            ->method('getFormat')
            ->will($this->returnValue(null));

        $this->viewHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf('FOS\RestBundle\View\View'), $this->equalTo($request))
            ->will($this->returnValue($response));

        $this->container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('fos_rest.view_handler'))
            ->will($this->returnValue($this->viewHandler));

        $event = $this->getResponseEvent($request, $view);

        $this->assertNull($this->listener->getView());
        $this->listener->onKernelView($event);
        $this->assertNotNull($this->listener->getView());
    }

    /**
     * @covers \LoftDigital\RestBundle\EventListener\ViewResponseListener::createView
     */
    public function testCreateView()
    {
        $request = new Request();
        $request->setRequestFormat('json');

        $view = $this->getMockBuilder('FOS\RestBundle\View\View')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var $event \PHPUnit_Framework_MockObject_MockObject|GetResponseForControllerResultEvent */
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->atLeast(1))
            ->method('getControllerResult')
            ->will($this->onConsecutiveCalls($view, array('data' => 'Test data')));

        $this->assertInstanceOf(
            'LoftDigital\RestBundle\EventListener\ViewResponseListener',
            $this->listener->createView($event)
        );
    }
}
