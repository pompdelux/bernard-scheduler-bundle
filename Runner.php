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
use Psr\Log\LoggerInterface;

/**
 * Class Runner
 *
 * @package Pompdelux\BernardSchedulerBundle
 */
class Runner
{
    private $shutdown = false;

    /**
     * @param PHPRedis        $redis
     * @param Producer        $producer
     * @param LoggerInterface $logger
     */
    public function __construct(PHPRedis $redis, Producer $producer, LoggerInterface $logger)
    {
        $this->redis    = $redis;
        $this->producer = $producer;
        $this->logger   = $logger;
    }

    /**
     * Starts an infinite loop calling tick();
     *
     * @param int $interval
     */
    public function run($interval)
    {
        $this->bind();
        $this->logger->debug('Starting scheduler runner.', ['interval='.$interval]);

        while ($this->tick()) {
            sleep($interval);
        }
    }

    /**
     * Mark Consumer as shutdown
     */
    public function shutdown()
    {
        $this->shutdown = true;
    }

    /**
     * @return bool
     */
    private function tick()
    {
        if ($this->shutdown) {
            return false;
        }

        $items = $this->redis->zrangebyscore('scheduler:queue', '-inf', time(), ['limit' => [0, 1]]);

        foreach ($items as $timestamp) {
            $this->produce($timestamp);
            $this->cleanup($timestamp);
        }

        unset ($items, $key, $timestamp);

        return true;
    }

    /**
     * Setup signal handlers for unix signals.
     */
    private function bind()
    {
        pcntl_signal(SIGTERM, array($this, 'shutdown'));
        pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        pcntl_signal(SIGINT, array($this, 'shutdown'));
    }


    /**
     * Send job to bernard
     *
     * @param int $timestamp
     */
    private function produce($timestamp)
    {
        $key   = 'scheduler:job-'.$timestamp;
        $count = $this->redis->lLen($key);

        if (0 == $count) {
            return;
        }

        $this->logger->debug('Processing jobs queued for '.date('Y-m-d H:i:s', $timestamp), [$key, 'job-count='.$count]);

        for ($i=0; $i<$count; $i++) {
            $job = $this->redis->lPop($key);
            $this->producer->produce($job->getMessage(), $job->getQueue());

            $this->logger->debug('Job added to bernard queue: '.$job->getQueue());
        }
    }

    /**
     * Cleanup in redis
     *
     * @param int $timestamp
     */
    private function cleanup($timestamp)
    {
        $key = 'scheduler:job-'.$timestamp;
        if (0 == $this->redis->lLen($key)) {
            $this->redis->del($key);
            $this->redis->zRem('scheduler:queue', $timestamp);
            $this->logger->debug('Cleaning up scheduler:queue', [$key]);
        }
    }
}
