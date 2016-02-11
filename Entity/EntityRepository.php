<?php

namespace LoftDigital\SymfonyRestBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository as GenericEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Rss\UserApiBundle\Entity\User;

/**
 * Entity Repository
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2016
 * @package LoftDigital\SymfonyRestBundle\Entity
 */
class EntityRepository extends GenericEntityRepository
{
    /** @var User */
    protected $user;

    /** @var array */
    protected $allowedStores = [];

    /** @var string */
    protected $orderBy;

    /** @var string */
    protected $order = Criteria::ASC;

    /**
     * Set user
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Find all store IDs for user
     *
     * @return array
     */
    public function findAllowedStoresForUser()
    {
        if ($this->user === null) {
            return [];
        }

        if (!empty($this->allowedStores)) {
            return $this->allowedStores;
        }

        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping->addScalarResult('store_id', 'storeId');

        $userStores = $this->getEntityManager()
            ->createNativeQuery('
                SELECT u2s.store_id
                FROM user_to_store u2s
                WHERE u2s.user_id = :userId
            ', $resultSetMapping)
            ->setParameter('userId', $this->user->getId())
            ->getResult();

        $userBrandStores = $this->getEntityManager()
            ->createNativeQuery('
                SELECT s.store_id
                FROM user_to_brand u2b
                JOIN store AS s ON s.brand_id = u2b.brand_id
                WHERE u2b.user_id = :userId
            ', $resultSetMapping)
            ->setParameter('userId', $this->user->getId())
            ->getResult();

        $this->allowedStores = array_unique(array_map(function ($item) {
            return $item['storeId'];
        }, array_merge($userStores, $userBrandStores)));

        return $this->allowedStores;
    }

    /**
     * Set order by
     *
     * @param string $orderBy
     *
     * @return $this
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Set order
     *
     * @param string $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}
