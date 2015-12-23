<?php

namespace LoftDigital\RestBundle\Security;

use Rss\UserApiBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Class AbstractRssVoter
 *
 * @author George Mylonas <georgem@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Security\AbstractRssVoter
 */
abstract class AbstractRssVoter extends AbstractVoter
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Checks if users are the same by id
     *
     * @param $checkUser User
     * @param $user User
     *
     * @return bool
     */
    protected function isSameUser($checkUser, $user)
    {
        if ($checkUser->getId() == $user->getId()) {
            return true;
        }

        return false;
    }
}
