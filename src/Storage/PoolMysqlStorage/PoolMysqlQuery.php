<?php
/**
 * @file PoolMysqlCreator.php
 * A helping class that isolates the creation procedure from the rest of the storage
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-11-15
 * Localization: none
 * Documentation: unknown
 * Tests: /Unit/Storage/PoolMysqlStorage/AppendTest.php
 * Coverage: 
 * PSR-State: completed
 */

namespace Sunhill\Storage\PoolMysqlStorage;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;
use Sunhill\Query\BasicQuery;
use Illuminate\Support\Collection;

class PoolMysqlQuery extends BasicQuery
{
     
    protected $structure;
    
    protected $target_subid;
    
    protected $tables;
    
    public function __construct(string $target_subid, $structure)
    {
        parent::__construct();
        $this->target_subid = $target_subid;
        $this->structure = $structure;
    }
    
    private function assembleTables()
    {
        $table = new \stdClass();
        $table->letter = 'a';
        $table->join = 'inner';
        $this->tables[$this->target_subid] = $table;
        
        $letter = 'b';
        foreach ($this->conditions as $condition) {
            if (!isset($this->tables[$this->structure[$condition->key]->storage_subid])) {
                $table = new \stdClass();
                $table->letter = $letter++;
                $table->join = 'inner';
                $this->tables[$this->structure[$condition->key]->storage_subid] = $table;
            }
        }
    }
    

    private function joinTables($query)
    {
        foreach ($this->tables as $table => $infos) {
            if ($table == $this->target_subid) {
                continue;
            }
            switch ($infos->join) {
                case 'inner':
                    $query = $query->join($table.' as '.$infos->letter,'a.id','=',$infos->letter.'.id');
                    break;
                case 'left':
                    $query = $query->leftJoin($table.' as '.$infos->letter,'a.id','=',$infos->letter.'.id');
                    break;
                case 'left':
                    $query = $query->rightJoin($table.' as '.$infos->letter,'a.id','=',$infos->letter.'.id');
                    break;
            }
        }
        return $query;
    }
    
    private function getLetterOfField(string $field): string
    {
        return $this->tables[$this->structure[$field]->storage_subid]->letter;    
    }
    
    private function assembleWhere($query)
    {
        foreach ($this->conditions as $condition) {
            switch ($condition->connection) {
                case 'and':
                    $query = $query->where($this->getLetterOfField($condition->key).'.'.$condition->key,$condition->relation,$condition->value);
                    break;
            }
        }
        
        return $query;
    }
    
    /**
     * Assembles the query according to the given conditions and returns 
     * pseudo query that is further processed by a finalizing call.
     */
    protected function assmebleQuery()
    {
        $this->assembleTables();
        $query = DB::table($this->target_subid.' as a');
        $query = $this->joinTables($query);
        $query = $this->assembleWhere($query);
        
        return $query;
    }
    
    /**
     * Returns the count of record that the previously assembled query returns
     *
     * @param unknown $assambled_query
     * @return int
     */
    protected function doGetCount($assambled_query): int
    {
        return $assambled_query->count();
    }
    
    /**
     * Returns a Collection object of all records that match the given query conditions.
     *
     * @param unknown $assembled_query
     */
    protected function doGet($assembled_query): Collection
    {
        return $assembled_query->get();        
    }
    
    /**
     * Returns if the field exists or a pseudo field of that name exists
     *
     * @param string $field
     * @return bool
     */
    protected function fieldExists(string $field): bool
    {
        
    }
    
    /**
     * Returns if the field can be uses as a sorting key
     *
     * @param string $field
     * @return bool
     */
    protected function fieldOrderable(string $field): bool
    {
        
    }
       
    /**
     * Deletes the records that match the condition
     *
     * @param unknown $assembled_query
     * @return int
     */
    protected function doDelete($assembled_query): int
    {
        return $assembled_query->delete();
    }
    
    /**
     * Updates the records that match the condition
     *
     * @param unknown $assembled_query
     * @param array $fields
     * @return int
     */
    protected function doUpdate($assembled_query, array $fields): int
    {
        return $assembled_query->update($fields);
    }
    
    /**
     * Inserts one or more new records into the pool of records
     *
     * @param unknown $assembled_query
     * @param array $fields
     * @return int
     */
    protected function doInsert($assembled_query, array $fields)
    {
        return $assembled_query->insert($fields);
    }
    
    
}