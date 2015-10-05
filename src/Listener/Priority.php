<?php
namespace Rubicon\Loop\Listener;

/**
 * Defines priority used by loop library to attach listeners.
 * This may help to define how to attach your own relatively, without magic numbers around
 */
interface Priority
{
    const LOW       = -100;
    const VERY_LOW  = -1000;
    const NORMAL    = 1;
    const HIGH      = 100;
    const VERY_HIGH = 1000;
}