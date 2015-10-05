<?php
namespace Rubicon\Loop;

interface LoopInterface
{
    /**
     * @return void
     */
    public function start();

    /**
     * @return void
     */
    public function stop();

    /**
     * @return bool
     */
    public function isStopped();
}