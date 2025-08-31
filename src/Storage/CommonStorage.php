<?php

/**
 * @file CommonStorage.php
 * A very simple storage that returns values that are stored internally in an array
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage Unit: 85.71  (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Properties\Exceptions\InvalidIndexException;
use Sunhill\Storage\Exceptions\FieldNotAnArrayException;
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;

abstract class CommonStorage extends AbstractStorage
{
    protected $values = [];

    /**
     * Performs the retrievement of the value
     */
    protected function doGetValue(string $name)
    {
        $this->checkFieldExistence($name);

        return $this->values[$name];
    }

    private function checkFieldExistence(string $name)
    {
        if (! array_key_exists($name, $this->values)) {
            throw new FieldNotAvaiableException("The field '$name' is not avaiable.");
        }
    }

    private function checkFieldIsArray(string $name)
    {
        if (! is_array($this->values[$name])) {
            throw new FieldNotAnArrayException("The field '$name' is not an array.");
        }
    }

    protected function doClearArray(string $name)
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);
        $this->values[$name] = [];
    }

    protected function doGetIndexedValue(string $name, mixed $index): mixed
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);
        if (! isset($this->values[$name][$index])) {
            throw new InvalidIndexException('The index does not exist.');
        }

        return $this->values[$name][$index];
    }

    protected function doGetElementCount(string $name): int
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);

        return count($this->values[$name]);
    }

    protected function doGetOffsetExists(string $name, $index): bool
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);

        return isset($this->values[$name][$index]);
    }

    protected function doGetIsInitialized(string $name): bool
    {
        return isset($this->values[$name]);
    }
}
