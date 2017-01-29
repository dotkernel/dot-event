<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-event
 * @author: n3vrax
 * Date: 9/14/2016
 * Time: 1:08 AM
 */

declare(strict_types = 1);

namespace Dot\Event;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Event
 * @package Dot\Event
 */
class Event extends \Zend\EventManager\Event
{
    /** @var  ServerRequestInterface */
    protected $request;

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}
