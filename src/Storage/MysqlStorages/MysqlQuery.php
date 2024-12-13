<?php

namespace Sunhill\Storage\MysqlStorages;

use Sunhill\Basic\Base;
use Illuminate\Support\Facades\DB;

class MysqlQuery extends Base
{
        
    protected $current_query;
    
    protected $basic_table = '';
    
    protected $tables = [];
    
    protected $reverse_tables = [];
    
    private $next_letter = 'a';
    
    public function __construct(string $basic_table)
    {
        parent::__construct();
        $this->basic_table = $basic_table;
        $this->addTable($basic_table);
        $this->current_query = DB::table($basic_table.' as a');
    }
    
    private function createDescriptor(string $name, string $join, string $letter): \stdClass
    {
        $info = new \stdClass();
        
        $info->name = $name;
        $info->letter = $letter;
        $info->join = $join;
        
        return $info;
    }
    
    private function addToTable(string $name, string $letter, \stdClass $info)
    {
        $this->tables[$name] = $info;
        $this->reverse_tables[$letter] = $info;        
    }
    
    protected function addTable(string $name, string $join = 'inner')
    {
        $current_letter = $this->next_letter++;
        
        $info = $this->createDescriptor($name, $join, $current_letter);
        $this->addToTable($name, $current_letter, $info);

        return $current_letter;
    }
 
    public function join(string $table, string $join_field = 'id')
    {
        $letter = $this->addTable($table, 'inner');
        $this->current_query->join($table.' as '.$letter,'a.id','=',$letter.'.'.$join_field);
        return $this;
    }
    
    public function first()
    {
        return $this->current_query->first();    
    }
    
    public function get()
    {
        return $this->current_query->get();    
    }
    
    public function __call($method, $parameters)
    {
        $this->current_query = $this->current_query->$method(... $parameters);
        return $this;
    }
}