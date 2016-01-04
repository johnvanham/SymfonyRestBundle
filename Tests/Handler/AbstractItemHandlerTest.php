<?php

namespace LoftDigital\RestBundle\Tests\Handler;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use LoftDigital\RestBundle\Handler\AbstractItemHandler;

/**
 * Class AbstractItemHandlerTest
 *
 * @package LoftDigital\RestBundle\Tests\Handler
 */
class AbstractItemHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $entityManager;

    /** @var string */
    protected $entityClass;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->entityManager = $this->getMock('\Doctrine\ORM\EntityManagerInterface');
        $this->entityClass = 'RssCustomerApiBundle:Customer';
    }

    /**
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::__construct
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::getEntityManager
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::getEntityClass
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::getRepository
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::getRange
     */
    public function testConstructor()
    {
        $entityManager = $this->entityManager;
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue('dummy repository'));

        /** @var AbstractItemHandler|\PHPUnit_Framework_MockObject_MockObject $itemHandler */
        $itemHandler = $this->getMockForAbstractClass(
            '\LoftDigital\RestBundle\Handler\AbstractItemHandler',
            array($entityManager, $this->entityClass)
        );

        $this->assertEquals($entityManager, $itemHandler->getEntityManager());
        $this->assertEquals($this->entityClass, $itemHandler->getEntityClass());
        $this->assertEquals('dummy repository', $itemHandler->getRepository());
    }

    /**
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::setRange
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::getRange
     *
     * @expectedException \LoftDigital\RestBundle\Handler\UnsupportedRangeFormatException
     */
    public function testSetRange()
    {
        /** @var AbstractItemHandler|\PHPUnit_Framework_MockObject_MockObject $itemHandler */
        $itemHandler = $this->getMockForAbstractClass(
            '\LoftDigital\RestBundle\Handler\AbstractItemHandler',
            array($this->entityManager, $this->entityClass)
        );

        $itemHandler->expects($this->any())
            ->method('getAcceptRanges')
            ->will($this->returnValue(array('email', 'id')));

        $this->assertEquals(null, $itemHandler->getRange());

        $itemHandler->setRange(null);
        $this->assertEquals(null, $itemHandler->getRange());

        $itemHandler->setRange('email');
        $this->assertEquals('email', $itemHandler->getRange());

        $itemHandler->setRange('non-existing range');
    }

    /**
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::setOrder
     * @covers \Rss\CustomerApiBundle\Handler\CustomerHandler::getOrder
     *
     * @expectedException \LoftDigital\RestBundle\Handler\UnsupportedOrderException
     */
    public function testSetOrder()
    {
        /** @var AbstractItemHandler|\PHPUnit_Framework_MockObject_MockObject $itemHandler */
        $itemHandler = $this->getMockForAbstractClass(
            '\LoftDigital\RestBundle\Handler\AbstractItemHandler',
            array($this->entityManager, $this->entityClass)
        );

        $this->assertEquals($itemHandler->getDefaultOrder(), $itemHandler->getOrder());

        $itemHandler->setOrder(null);
        $this->assertEquals($itemHandler->getDefaultOrder(), $itemHandler->getOrder());

        $itemHandler->setOrder(Criteria::ASC);
        $this->assertEquals(Criteria::ASC, $itemHandler->getOrder());

        $itemHandler->setOrder('dummy order');
    }

    /**
     * @covers \LoftDigital\RestBundle\Handler\AbstractItemHandler::getAcceptOrders
     */
    public function testGetAcceptOrders()
    {
        /** @var AbstractItemHandler|\PHPUnit_Framework_MockObject_MockObject $itemHandler */
        $itemHandler = $this->getMockForAbstractClass(
            '\LoftDigital\RestBundle\Handler\AbstractItemHandler',
            array($this->entityManager, $this->entityClass)
        );

        $this->assertTrue(is_array($itemHandler->getAcceptOrders()));
    }

    /**
     * @covers \LoftDigital\RestBundle\Handler\AbstractItemHandler::getDefaultOrder
     */
    public function testGetDefaultOrder()
    {
        /** @var AbstractItemHandler|\PHPUnit_Framework_MockObject_MockObject $itemHandler */
        $itemHandler = $this->getMockForAbstractClass(
            '\LoftDigital\RestBundle\Handler\AbstractItemHandler',
            array($this->entityManager, $this->entityClass)
        );

        $this->assertTrue(is_string($itemHandler->getDefaultOrder()));
    }
}
