<?php

namespace LoftDigital\SymfonyRestBundle\Tests\Model;

use ArrayIterator;
use ArrayObject;
use Doctrine\ORM\Tools\Pagination\Paginator;
use LoftDigital\SymfonyRestBundle\Model\ListResponse;

/**
 * Class ListResponseTest
 *
 * @package LoftDigital\SymfonyRestBundle\Tests\Model
 */
class ListResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var int */
    protected $pageItemCount;

    /** @var int */
    protected $offset;

    /** @var Paginator */
    protected $paginator;

    /** @var ArrayIterator */
    protected $iterator;

    /** @var array */
    protected $data;

    /** @var array */
    protected $acceptRanges;

    /** @var int */
    protected $totalCount;

    /** @var string */
    protected $range;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->pageItemCount = 20;
        $this->offset = 10;
        $this->data = range(1, $this->pageItemCount);
        $this->acceptRanges = array('email', 'name', 'date');
        $this->range = 'email';
        $this->totalCount = 371;

        $arrayObject = new ArrayObject($this->data);
        $this->iterator = $arrayObject->getIterator();

        /** @var \Doctrine\ORM\Tools\Pagination\Paginator|\PHPUnit_Framework_MockObject_MockObject $paginator */
        $this->paginator = $this->getMockBuilder('\Doctrine\ORM\Tools\Pagination\Paginator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->paginator->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($this->iterator));

        $this->paginator->expects($this->any())
            ->method('count')
            ->will($this->returnValue($this->totalCount));
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::__construct
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::__construct
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getTotalItemCount
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getIterator
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getPageItemCount
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getOffset
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getAcceptRanges
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getRange
     */
    public function testConstructor()
    {
        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);

        $this->assertEquals($this->totalCount, $listResponse->getTotalItemCount());
        $this->assertEquals($this->iterator, $listResponse->getIterator());
        $this->assertEquals($this->pageItemCount, $listResponse->getPageItemCount());
        $this->assertEquals($this->offset, $listResponse->getOffset());
        $this->assertEquals($this->acceptRanges, $listResponse->getAcceptRanges());
        $this->assertEquals($this->range, $listResponse->getRange());
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getData
     */
    public function testGetData()
    {
        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);

        $this->assertEquals($this->data, $listResponse->getData());
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getNextRangeHeader
     */
    public function testGetNextRangeHeader()
    {
        $nextRangeHeader = array(
            'Next-Range' => sprintf(
                '%s; max=%d,offset=%s',
                $this->range,
                $this->pageItemCount,
                $this->offset +$this->pageItemCount
            )
        );

        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($nextRangeHeader, $listResponse->getNextRangeHeader());
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getContentRangeHeader
     */
    public function testGetContentRangeHeader()
    {
        $contentRangeHeader = array(
            'Content-Range' => sprintf(
                '%s %d-%d/%d',
                $this->range,
                $this->offset + 1,
                $this->offset + $this->pageItemCount,
                $this->totalCount
            )
        );
        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($contentRangeHeader, $listResponse->getContentRangeHeader());

        $offset = $this->totalCount - 10;
        $contentRangeHeader = array(
            'Content-Range' => sprintf(
                '%s %d-%d/%d',
                $this->range,
                $offset + 1,
                $this->totalCount,
                $this->totalCount
            )
        );
        $listResponse = new ListResponse($this->paginator, $offset, $this->range, $this->acceptRanges);
        $this->assertEquals($contentRangeHeader, $listResponse->getContentRangeHeader());
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getAcceptRangesHeader
     */
    public function testGetAcceptRangesHeader()
    {
        $acceptRangesHeader = array(
            'Accept-Ranges' => implode(', ', $this->acceptRanges)
        );

        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($acceptRangesHeader, $listResponse->getAcceptRangesHeader());
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getHeaders
     */
    public function testGetHeaders()
    {
        $headers = array(
            'Accept-Ranges' => implode(', ', $this->acceptRanges),
            'Content-Range' => sprintf(
                '%s %d-%d/%d',
                $this->range,
                $this->offset + 1,
                $this->offset + $this->pageItemCount,
                $this->totalCount
            ),
            'Next-Range' => sprintf(
                '%s; max=%d,offset=%s',
                $this->range,
                $this->pageItemCount,
                $this->offset +$this->pageItemCount
            ),
        );

        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($headers, $listResponse->getHeaders());
    }

    /**
     * @covers \LoftDigital\SymfonyRestBundle\Model\ListResponse::getStatusCode
     */
    public function testGetStatusCode()
    {
        $listResponse = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals(206, $listResponse->getStatusCode());

        $totalCount = 11;
        $data = range(1, 20);
        $arrayObject = new ArrayObject($data);
        $iterator = $arrayObject->getIterator();

        /** @var \Doctrine\ORM\Tools\Pagination\Paginator|\PHPUnit_Framework_MockObject_MockObject $paginator */
        $paginator = $this->getMockBuilder('\Doctrine\ORM\Tools\Pagination\Paginator')
            ->disableOriginalConstructor()
            ->getMock();

        $paginator->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $paginator->expects($this->any())
            ->method('count')
            ->will($this->returnValue($totalCount));

        $listResponse = new ListResponse($paginator, $this->offset, $this->range, $this->acceptRanges);

        $this->assertEquals(200, $listResponse->getStatusCode());
    }
}
