<?php
/**
 * @file ObjectTagAssignsStorage.php
 * An abstract class that loads the list of tags that are associated to an object 
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-12-19
 * Localization: none
 * Documentation: 
 * Tests: 
 * Coverage: 
 */

namespace Sunhill\Storage\ObjectStorage;

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tags\Tag;

abstract class ObjectTagAssignsStorage extends PersistentPoolStorage
{
    
    abstract protected function loadTagIDList(int $object_id);
    
    public function getTagsOfObjects(int $object_id)
    {
        $result = [];
        
        $list = $this->loadTagIDList($object_id);
        foreach ($list as $entry) {
            $tag = new Tag();
            $tag->load($entry->tag_id);
            $result[] = $tag;
        }
        
        return $result;
    }
    
    abstract protected function storeTagIDList(int $object_id, array $tag_ids);
    
    public function storeTagsOfObject(int $object_id, array $tags)
    {
        $ids = [];
        foreach ($tags as $tag) {
            $ids[] = $tag->getID();
        }
        $this->storeTagIDList($object_id, $ids);
    }
}