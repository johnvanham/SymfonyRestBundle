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
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Checks if users are the same by ID
     *
     * @param User $checkUser
     * @param User $user
     *
     * @return bool
     */
    protected function isSameUser(User $checkUser, User $user)
    {
        return $checkUser->getId() === $user->getId();
    }
}
