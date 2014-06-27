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

use Bernard\Producer;
use Pompdelux\PHPRedisBundle\Client\PHPRedis;

/**
 * Class Scheduler
 *
 * @package Pompdelux\BernardSchedulerBundle
 */
class Scheduler
{
    /**
     * @var \Pompdelux\PHPRedisBundle\Client\PHPRedis
     */
    private $redis;

    /**
     * @param PHPRedis    $redis
     */
    public function __construct(PHPRedis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param int $seconds
     * @param Job $job
     *
     * @return $this
     */
    public function enqueueIn($seconds, Job $job)
    {
        return $this->enqueueAt(new \DateTime('+ '.(int) $seconds.' seconds'), $job);
    }

    /**
     * @param \DateTime $timestamp
     * @param Job       $job
     *
     * @return $this
     */
    public function enqueueAt(\DateTime $timestamp, Job $job)
    {
        $ts = $timestamp->getTimestamp();
        $this->redis->rPush('scheduler:job-'.$ts, $job);
        $this->redis->zAdd('scheduler:queue', $ts, $ts);

        return $this;
    }
}
