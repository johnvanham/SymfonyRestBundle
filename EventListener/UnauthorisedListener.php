<?php

namespace LoftDigital\SymfonyRestBundle\EventListener;

use FOS\UserBundle\Model\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

/**
 * Unauthorised Listener
 *
 * Symfony returns HTTP 401 header for both invalid token and unauthorised
 * request. This listener throws exception for expired tokens. The exception is
 * processed in Exception Listener, where original header and message are
 * amended.
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <http://weareloft.com>, 2016
 * @package LoftDigital\SymfonyRestBundle\EventListener
 */
class UnauthorisedListener
{
    /** @var Request */
    protected $request;

    /** @var UserManager */
    protected $userManager;

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     * @param UserManager $userManager
     */
    public function __construct(
        RequestStack $requestStack,
        UserManager $userManager
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->userManager = $userManager;
    }

    /**
     * Kernel response listener
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response->getStatusCode() === 401) {
            if ($response->getContent() === '') {
                throw new CredentialsExpiredException('Credentials have expired.');
            }

            $data = json_decode($response->getContent());
            if (isset($data->message)) {
                $this->processExceptionData($data->message);
            }
        }
    }

    /**
     * Process exception data
     *
     * @param $message
     */
    public function processExceptionData($message)
    {
        if ($message == 'Bad credentials') {
            $username = $this->request->get('_username');
            if ($username !== null) {
                $user = $this->userManager->findUserByUsername($username);
                if ($user !== null
                    && $user->getConfirmationToken() !== null
                    && $user->isEnabled() === false
                ) {
                    throw new AccessDeniedException(
                        'User account was not validated.'
                    );
                }
            }

            throw new BadCredentialsException('Invalid credentials.');
        }
    }
}
