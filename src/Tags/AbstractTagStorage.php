<?php
/**
 * @file AbstractTagStorage.php
 * A class that is the base for storages for tags
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-05-25
 * Create date: 2025-05-25
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage Unit: 100 % (2025-06-06)
 * PSR-State: completed
 */
namespace Sunhill\Tags;

use Sunhill\Basic\Base;
use Sunhill\Tags\Exceptions\TagNameNotFoundException;
use Sunhill\Tags\Exceptions\TagNameAmbiguousException;
use Sunhill\Tags\Exceptions\TagIDNotFoundException;

abstract class AbstractTagStorage extends Base
{
    
    abstract protected function searchTag(array $condition);
    
    public function IDexists(int $id): bool
    {
        $results = $this->searchTag(['id'=>$id]);
        return count($results) == 1;
    }
    
    public function searchName(string $name): int
    {
        $result = $this->searchTag(['name'=>$name]);
        if (count($result) == 0) {
            throw new TagNameNotFoundException("The tag with the name '$name' was not found.");
        }
        if (count($result) > 1) {
            throw new TagNameAmbiguousException("The tag with the name '$name' is ambiguous.");
        }
        return $result[0]->id;
    }
    
    public function load(int $id)
    {
        $result = $this->searchTag(['id'=>$id]);
        if (count($result) !== 1) {
            throw new TagIDNotFoundException("The tag with the id '$id' was not found.");
        }        
        return $result[0];
    }
}