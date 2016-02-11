<?php

namespace LoftDigital\SymfonyRestBundle\RPC;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AbstractRPC
 *
 * @author Tomas Lang <tomas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package Rss\EnquiryApiBundle\RPC
 */
abstract class AbstractRPC extends Controller
{
    /**
     * Publish message to channel
     *
     * @param string $uri
     * @param array $data
     */
    protected function publish($uri, array $data)
    {
        $this->get('thruway.client')->publish(
            $uri,
            [$data],
            [],
            ['acknowledge' => true]
        )->then(
            function () {
            },
            function ($error) {
                $this->get('logger')->info(json_encode($error));
            }
        );
    }
}
