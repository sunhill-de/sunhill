<?php
use Sunhill\Checker\Checker;
use Sunhill\Checker\CheckException;

function runCheck(Checker $checker, string $method, bool $repair)
{
    try {
        $checker->$method($repair);
    } catch (CheckException $e) {
        
    }
    return $checker->getLastResult();
}

