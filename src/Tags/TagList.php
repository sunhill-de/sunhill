<?php
/**
 * @file TagList.php
 * A class that provides access to the list of tags associated to an object
 * @author Klaus Dimde
 * Lang en
 * Create date: 2025-05-25
 * Reviewstatus: 2025-05-25
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */
namespace Sunhill\Tags;

use Sunhill\Basic\Base;
use Sunhill\Storage\AbstractStorage;

class TagList extends Base implements \ArrayAccess, \Countable
{
    protected $tag_list = [];
    
    protected $storage;
    
    public function __construct(AbstractStorage $storage)
    {
        $this->storage = $storage;
        $this->storage->setValue('_tags',[]);
    }
    
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->tag_list[$offset]);
    }
    
    public function offsetGet(mixed $offset): mixed
    {
        return $this->tag_list[$offset];
    }
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $value = $this->createTag($value);
        if (is_null($offset)) {
            $this->tag_list[] = $value;
        } else {
            $this->tag_list[$offset] = $value;
        }
        $this->storage->setIndexedValue('_tags', $offset, $value->getID());
    }
    
    public function offsetUnset(mixed $offset): void
    {
        unset($this->tag_list[$offset]);
    }
    
    public function count(): int
    {
        return count($this->tag_list);
    }
    
    public function add($tag)
    {
        $tag = $this->createTag($tag);
        $this->tag_list[] = $tag;
    }
    
    protected function createTag($tag_id): Tag
    {
        if (is_a($tag_id, Tag::class)) {
            return $tag_id;
        } else if (is_int($tag_id)) {
            $tag = new Tag();
            $tag->load($tag_id);
            return $tag;
        } 
        throw new \Exception("Can't handle given tag");
    }
    
    public function clear()
    {
        $this->tag_list = [];
    }
}
