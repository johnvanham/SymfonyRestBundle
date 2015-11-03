<?php

namespace LoftDigital\RestBundle\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Item handler abstract class
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Handler
 */
abstract class AbstractItemHandler
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var EntityRepository */
    protected $repository;

    /** @var string */
    protected $entityClass;

    /** @var string */
    protected $range;

    /** @var string */
    protected $order;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityClass
     */
    public function __construct(EntityManagerInterface $entityManager, $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
        $this->repository = $this->entityManager->getRepository($this->entityClass);
        $this->range = $this->getDefaultRange();
        $this->order = $this->getDefaultOrder();
    }

    /**
     * Returns list of accept ranges for the handler
     *
     * @return array
     */
    abstract public function getAcceptRanges();

    /**
     * Returns default range
     *
     * @return string
     */
    abstract public function getDefaultRange();

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set search range
     *
     * @param string $range
     *
     * @return $this
     * @throws UnsupportedRangeFormatException
     */
    public function setRange($range)
    {
        if ($range == null) {
            $this->range = $this->getDefaultRange();

            return $this;
        }

        if (in_array($range, $this->getAcceptRanges())) {
            $this->range = $range;

            return $this;
        }

        throw new UnsupportedRangeFormatException("Unsupported range format '$range'");
    }

    /**
     * Set data order
     *
     * @param string $order
     *
     * @return $this
     * @throws UnsupportedOrderException
     */
    public function setOrder($order)
    {
        if ($order == null) {
            $this->order = $this->getDefaultOrder();

            return $this;
        }

        if (in_array($order, $this->getAcceptOrders())) {
            $this->order = $order;

            return $this;
        }

        throw new UnsupportedOrderException("Unsupported order type '$order'");
    }

    /**
     * Returns list of accept order types
     *
     * @return array
     */
    public function getAcceptOrders()
    {
        return array(
            self::ORDER_ASC,
            self::ORDER_DESC
        );
    }

    /**
     * Returns default order type
     *
     * @return string
     */
    public function getDefaultOrder()
    {
        return self::ORDER_ASC;
    }
}
