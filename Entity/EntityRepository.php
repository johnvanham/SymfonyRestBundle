<?php

namespace LoftDigital\SymfonyRestBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository as GenericEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use FOS\UserBundle\Model\User;

/**
 * Entity Repository
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
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
