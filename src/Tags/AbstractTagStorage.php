<?php

/**
 * @file AbstractTagStorage.php
 * A class that is the base for storages for tags
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-08-12
 * Create date: 2025-05-25
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage Unit: 100  (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Tags;

use Sunhill\Basic\Base;
use Sunhill\Tags\Exceptions\TagIDNotFoundException;
use Sunhill\Tags\Exceptions\TagNameAmbiguousException;
use Sunhill\Tags\Exceptions\TagNameNotFoundException;

abstract class AbstractTagStorage extends Base
{
    /**
     * The worker for the TagStorage that searches the tag with the given condition
     */
    abstract protected function searchTag(array $condition);

    /**
     * Returns true when a tag with the given id exists
     */
    public function IDexists(int $id): bool
    {
        $results = $this->searchTag(['id' => $id]);

        return count($results) == 1;
    }

    /**
     * Searches for a tag with the given name. It throws an exception when too many or none where found
     */
    public function searchName(string $name): int
    {
        $result = $this->searchTag(['name' => $name]);
        if (count($result) == 0) {
            throw new TagNameNotFoundException("The tag with the name '$name' was not found.");
        }
        if (count($result) > 1) {
            throw new TagNameAmbiguousException("The tag with the name '$name' is ambiguous.");
        }

        return $result[0]->id;
    }

    /**
     * Loads the tag with the id out of the storage
     *
     * @return unknown
     */
    public function load(int $id)
    {
        $result = $this->searchTag(['id' => $id]);
        if (count($result) !== 1) {
            throw new TagIDNotFoundException("The tag with the id '$id' was not found.");
        }

        return $result[0];
    }
}
