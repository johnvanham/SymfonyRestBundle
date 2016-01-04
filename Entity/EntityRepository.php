<?php

namespace LoftDigital\RestBundle\Entity;

use Doctrine\ORM\EntityRepository as GenericEntityRepository;
use Rss\UserApiBundle\Entity\User;

/**
 * Entity Repository
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2016
 * @package LoftDigital\RestBundle\Entity
 */
class EntityRepository extends GenericEntityRepository
{
    /** @var User */
    protected $user;

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
    public function findAllowedStoresForUserId()
    {
        if ($this->user === null) {
            return [];
        }

        $userStores = $this->getEntityManager()
            ->createQuery('
                SELECT u2s.storeId
                FROM RssUserApiBundle:UserToStore u2s
                WHERE u2s.userId = :userId
            ')
            ->setParameter('userId', $this->user->getId())
            ->getResult();

        $userBrandStores = $this->getEntityManager()
            ->createQuery('
                SELECT s.id AS storeId FROM RssUserApiBundle:UserToBrand u2b
                JOIN RssEntityApiBundle:Store AS s WITH s.brandId = u2b.brandId
                WHERE u2b.userId = :userId
                GROUP BY s.brandId
            ')
            ->setParameter('userId', $this->user->getId())
            ->getResult()
        ;

        $stores = array_unique(array_map(function ($item) {
            return $item['storeId'];
        }, array_merge($userStores, $userBrandStores)));

        return $stores;
    }
}
