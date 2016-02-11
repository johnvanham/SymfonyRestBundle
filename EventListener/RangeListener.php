<?php

namespace LoftDigital\SymfonyRestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * RangeListener populates range attributes in a request object
 *
 * Range listener process Range HTTP header and extracts order, limit and max returned values from it.
 * Example Range HTTP header:
 * ```
 * Range: name; order=desc,max=100,offset=1;
 * ```
 * When the range header is not set, then range attributes are populated with default values set
 * in configuration file.
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\SymfonyRestBundle\EventListener
 */
class RangeListener
{
    /** @var int */
    protected $max;

    /** @var int */
    protected $offset;

    /** @var string (asc|desc) */
    protected $order;

    /** @var int */
    protected $maxLimit;

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getMaxLimit()
    {
        return $this->maxLimit;
    }

    /**
     * @param int $maxLimit
     */
    public function setMaxLimit($maxLimit)
    {
        $this->maxLimit = $maxLimit;
    }

    /**
     * Populate range parameters on kernel request event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->attributes->set('order', $this->order);
        $request->attributes->set('maxLimit', $this->maxLimit);
        $request->attributes->set('max', $this->max);
        $request->attributes->set('offset', $this->offset);
        $range = $request->headers->get('range');

        if ($range) {
            if (1 === preg_match('/(order)=(?P<order>(\w+))/', $range, $matches)) {
                $request->attributes->set('order', $matches['order']);
            }

            if (1 === preg_match('/(max)=(?P<max>(\d)+)/', $range, $matches)) {
                $request->attributes->set('max', $matches['max']);
            }

            if (1 === preg_match('/(offset)=(?P<offset>(\d)+)/', $range, $matches)) {
                $request->attributes->set('offset', $matches['offset']);
            }

            if (1 === preg_match('/^(?P<range>(\w+))\s*\;/', $range, $matches)) {
                $request->attributes->set('range', $matches['range']);
            }
        }
    }
}
