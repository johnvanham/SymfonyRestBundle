<?php

namespace LoftDigital\RestBundle\Tests\Model;

use ArrayIterator;
use ArrayObject;
use Doctrine\ORM\Tools\Pagination\Paginator;
use LoftDigital\RestBundle\Model\ListResponse;

/**
 * Class ListResponseTest
 *
 * @package LoftDigital\RestBundle\Tests\Model
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
     * {inheritdoc}
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
     * @covers \LoftDigital\RestBundle\Model\ListRequest::__construct
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getTotalItemCount
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getIterator
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getPageItemCount
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getOffset
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getAcceptRanges
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getRange
     */
    public function testConstructor()
    {
        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);

        $this->assertEquals($this->totalCount, $listRequest->getTotalItemCount());
        $this->assertEquals($this->iterator, $listRequest->getIterator());
        $this->assertEquals($this->pageItemCount, $listRequest->getPageItemCount());
        $this->assertEquals($this->offset, $listRequest->getOffset());
        $this->assertEquals($this->acceptRanges, $listRequest->getAcceptRanges());
        $this->assertEquals($this->range, $listRequest->getRange());
    }

    /**
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getData
     */
    public function testGetData()
    {
        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);

        $this->assertEquals($this->data, $listRequest->getData());
    }

    /**
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getNextRangeHeader
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

        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($nextRangeHeader, $listRequest->getNextRangeHeader());
    }

    /**
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getContentRangeHeader
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
        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($contentRangeHeader, $listRequest->getContentRangeHeader());

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
        $listRequest = new ListResponse($this->paginator, $offset, $this->range, $this->acceptRanges);
        $this->assertEquals($contentRangeHeader, $listRequest->getContentRangeHeader());
    }

    /**
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getAcceptRangesHeader
     */
    public function testGetAcceptRangesHeader()
    {
        $acceptRangesHeader = array(
            'Accept-Ranges' => implode(', ', $this->acceptRanges)
        );

        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($acceptRangesHeader, $listRequest->getAcceptRangesHeader());
    }

    /**
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getHeaders
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

        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals($headers, $listRequest->getHeaders());
    }

    /**
     * @covers \LoftDigital\RestBundle\Model\ListRequest::getStatusCode
     */
    public function testGetStatusCode()
    {
        $listRequest = new ListResponse($this->paginator, $this->offset, $this->range, $this->acceptRanges);
        $this->assertEquals(206, $listRequest->getStatusCode());

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

        $listRequest = new ListResponse($paginator, $this->offset, $this->range, $this->acceptRanges);

        $this->assertEquals(200, $listRequest->getStatusCode());
    }
}
