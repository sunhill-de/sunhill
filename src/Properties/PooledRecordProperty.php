<?php
/**
 * @file PooledRecordProperty.php
 * Defines a property as a base for all record that can load themself out of a pool of data
 * Lang en
 * Reviewstatus: 2024-10-24
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage Unit: 46.15 % (2025-06-06)
 *
 * Wiki: /PooledRecordProperties
 * tests /tests/Unit/Properties/PooledRecordProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Properties\Exceptions\WrongStorageSetException;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Query\BasicQuery;
use Sunhill\Properties\Exceptions\NoStorageSetException;
use Sunhill\Query\Query;
use Sunhill\Parser\Analyzer;
use Sunhill\Parser\Executor;

class PooledRecordProperty extends PersistentRecordProperty
{
    
    /**
     * Loads the record with the given id out of the pool
     * 
     * @param unknown $id
     */
    public function load($id)
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $storage->setStructure($this->getStructure());
        $storage->load($id);
    }
    
    public function IDexists($id): bool
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $storage->setStructure($this->getStructure());
        return $storage->IDExists($id);        
    }
    
    protected function isValidStorage(AbstractStorage $storage): bool
    {
        return is_a($storage, PersistentPoolStorage::class);
    }
    
    public function getID(): mixed
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        return $storage->getID();
    }
    
    /**
     * Non-static function to delete a pooles record. When an id is given it deletes the record
     * with the given id. If none is given, it deletes the current record
     * 
     * @param unknown $id
     */
    public function delete($id = null)
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $id = $id ?? $this->getID();
        $storage->delete($id);
    }
    
    /**
     * Static function to delete a pooled record. The id must not be null. It deletes the record
     * with the given id.
     * 
     * @param mixed $id
     */
    public static function erase(mixed $id)
    {
        if (empty($storage_class = static::getStorageClass())) {
            throw new NoStorageSetException('No default storage defines while calling ::erase()');
        }
        $object = new static();
        $object->delete($id);
    }
    
    /**
     * Executes a migration for this kind of pooled record
     */
    public static function migrate()
    {
        $dummy = new static();
        $storage = $dummy->getStorage();
        $storage->migrate();
    }
    
    /**
     * Returns an analyzer for a query_node
     * 
     * @return Analyzer
     */
    protected static function getAnalyzer(): Analyzer
    {
        return new RecordPropertyAnalyzer();
    }
    
    protected static function getExecutor(): Executor
    {
        $dummy = new static();
        return $dummy->getStorage()->getQueryExecutor();
    }
    
    /**
     * Executes a query on data of this kind of object
     *
     * @return BasicQuery
     */
    public static function query(): Query
    {
        $query = new Query();
        $query->setRecordProperty(static::class);
        $query->setAnalyzer(static::getAnalyzer());
        $query->setExecutor(static::getExecutor());
        return $query;        
    }
        
}