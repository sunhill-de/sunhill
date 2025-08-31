<?php

/**
 * @file ArrayObjectStorage.php
 * This is the implementation of an AbstractObjectStorage that stores the values in arrays
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-04-23
 * Creation date: 2025-04-23
 * Localization: none
 * Documentation: unknown
 * Tests: /tests/Unit/Storage/ArrayObjectStorage/*
 * Coverage Unit:
 */

namespace Sunhill\Storage\ArrayObjectStorage;

use Sunhill\Query\QueryParser\QueryNode;
use Sunhill\Storage\AbstractObjectStorage;

class ArrayObjectStorage extends AbstractObjectStorage
{
    /**
     * Updates the storage with the subid. It uses $key to identiy the record(s) and sets the givenvalues
     *
     * @param  unknown  $key
     * @param  unknown  $values
     */
    protected function updateStorageSubid(string $subid, int $key, array $values, string $key_field = 'id') {}

    /**
     * Deletes all references to key from the given
     *
     * @param  unknown  $key
     */
    protected function deleteStorageSubid(string $subid, int $key, string $key_field = 'id') {}

    /**
     * Inserts into the given storage subid the given values. If value is a array of arrays then insert every entry as a separate record
     */
    protected function insertStorageSubid(string $subid, array $values) {}

    protected function insertObjects(array $values): int {}

    /**
     * Loads from the given storage subif the values with the given key
     *
     * @return array
     */
    protected function loadStorageSubid(string $subid, int $key, string $key_field = 'id'): array|\stdClass {}

    protected function doExecuteQuery(QueryNode $node) {}
}
