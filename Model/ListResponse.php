<?php

namespace LoftDigital\SymfonyRestBundle\Model;

use Countable;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * List request response handler
 *
 * Handles creating correct headers and response codes for a pagination
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\Handler
 */
class ListResponse implements ResponseInterface
{
    /** @var int List offset */
    protected $offset;

    /** @var int */
    protected $totalItemCount;

    /** @var int */
    protected $pageItemCount;

    /** @var \ArrayIterator|\Traversable */
    protected $iterator;

    /** @var array */
    protected $acceptRanges;

    /** @var string */
    protected $range;

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->totalItemCount;
    }

    /**
     * @return int
     */
    public function getPageItemCount()
    {
        return $this->pageItemCount;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @return array
     */
    public function getAcceptRanges()
    {
        return $this->acceptRanges;
    }

    /**
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param Paginator|Countable $paginator
     * @param int $offset
     * @param string $range
     * @param array $acceptRanges
     */
    public function __construct(Countable $paginator, $offset, $range, array $acceptRanges)
    {
        $this->totalItemCount = count($paginator);
        $this->iterator = $paginator->getIterator();
        $this->pageItemCount = $this->iterator->count();
        $this->offset = $offset;
        $this->range = $range;
        $this->acceptRanges = $acceptRanges;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->iterator->getArrayCopy();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return array_merge(
            $this->getAcceptRangesHeader(),
            $this->getContentRangeHeader(),
            $this->getNextRangeHeader()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        if ($this->totalItemCount > $this->pageItemCount) {
            return 206;
        }

        return 200;
    }

    /**
     * Returns Accept-Ranges header
     *
     * This header specify properties which can be used to sort a response.
     *
     * @return array
     */
    public function getAcceptRangesHeader()
    {
        return array(
            'Accept-Ranges' => implode(', ', $this->acceptRanges)
        );
    }

    /**
     * Returns Next-Range header
     *
     * This header can be passed to Range header when iterating over Next-Range.
     *
     * @return array
     */
    public function getNextRangeHeader()
    {
        return array(
            'Next-Range' => sprintf(
                "%s; max=%d,offset=%d",
                $this->range,
                $this->pageItemCount,
                $this->offset +$this->pageItemCount
            )
        );
    }

    /**
     * Returns Content-Range header
     *
     * This header indicates range of values returned.
     *
     * @return array
     */
    public function getContentRangeHeader()
    {
        $recordsTo = $this->totalItemCount;
        if (($this->offset + $this->pageItemCount) <= $this->totalItemCount) {
            $recordsTo = $this->offset + $this->pageItemCount;
        }

        return array(
            'Content-Range' => sprintf(
                "%s %d-%d/%d",
                $this->range,
                $this->offset + 1,
                $recordsTo,
                $this->totalItemCount
            )
        );
    }
}
