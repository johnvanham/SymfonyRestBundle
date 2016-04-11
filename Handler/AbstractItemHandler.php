<?php

namespace LoftDigital\SymfonyRestBundle\Handler;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\UserBundle\Model\User;
use LoftDigital\SymfonyRestBundle\Entity\EntityRepository;
use LoftDigital\SymfonyRestBundle\Model\ListResponse;

/**
 * Item handler abstract class
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\Handler
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

    /** @var User */
    protected $user;

    /** @var int */
    protected $offset;

    /** @var Paginator */
    protected $paginator;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param string $entityClass
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
     * Get entity manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Get repository
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get entity class
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Get range
     *
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Get order
     *
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

        throw new UnsupportedRangeFormatException(sprintf(
            'Unsupported range format "%s"',
            $range
        ));
    }

    /**
     * Set data order
     *
     * @param string $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $formattedOrder = strtoupper($order);

        if ($formattedOrder == null) {
            $this->order = $this->getDefaultOrder();

            return $this;
        }

        if (in_array($formattedOrder, $this->getAcceptOrders())) {
            $this->order = $formattedOrder;

            return $this;
        }

        throw new UnsupportedOrderException(sprintf(
            'Unsupported order type "%s"',
            $order
        ));
    }

    /**
     * Returns list of accept order types
     *
     * @return array
     */
    public function getAcceptOrders()
    {
        return [
            Criteria::ASC,
            Criteria::DESC
        ];
    }

    /**
     * Returns default order type
     *
     * @return string
     */
    public function getDefaultOrder()
    {
        return Criteria::ASC;
    }

    /**
     * Set user object
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->repository->setUser($this->user);

        return $this;
    }

    /**
     * Get list response
     *
     * @return ListResponse
     */
    public function getListResponse()
    {
        return new ListResponse(
            $this->paginator,
            $this->offset,
            $this->range,
            $this->getAcceptRanges()
        );
    }
}
