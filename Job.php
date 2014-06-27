<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pompdelux\BernardSchedulerBundle;

use Bernard\Message\DefaultMessage;

/**
 * Class Job
 *
 * @package Pompdelux\BernardSchedulerBundle
 */
class Job
{

    /**
     * @var string
     */
    private $queue;

    /**
     * @var string
     */
    private $handler;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Construct
     *
     * @param string $queue
     * @param string $handler
     * @param array  $parameters
     */
    public function __construct($queue, $handler, array $parameters = [])
    {
        $this->queue      = $queue;
        $this->handler    = $handler;
        $this->parameters = $parameters;
    }

    /**
     * Get name of the queue
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Get Bernard Message
     *
     * @return DefaultMessage
     */
    public function getMessage()
    {
        return new DefaultMessage($this->handler, $this->parameters);
    }
}
