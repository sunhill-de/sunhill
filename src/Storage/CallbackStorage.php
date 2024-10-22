<?php
/**
 * @file CallbackStorage.php
 * A very simple storage that stores the values in an array that is retreieven from a callback
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-22
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 1000 % (2024-10-22)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\CallbackMissingException;

class CallbackStorage extends SimpleStorage
{
    
    protected $callback;
    
    public function setCallback(callable $callback): static
    {
        $this->callback = $callback;
        return $this;
    }
    
    private function checkCallback()
    {
        if (is_null($this->callback)) {
            throw new CallbackMissingException("The callback was not set.");
        }
    }
     
    protected function readValues(): array
    {
        $this->checkCallback();
        $callback = $this->callback;
        return $callback();        
    }
    
}