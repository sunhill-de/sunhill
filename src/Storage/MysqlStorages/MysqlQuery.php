<?php

namespace Sunhill\Storage\MysqlStorages;

use Sunhill\Basic\Base;

class MysqlQuery extends Base
{
        
    protected $basic_table = '';
    
    protected $tables = [];
    
    protected $reverse_tables = [];
    
    private $next_letter = 'a';
    
    public function __construct(string $basic_table)
    {
        parent::__construct();
        $this->basic_table = $basic_table;
        $this->addTable($basic_table);
    }
    
    protected function addTable(string $name, string $join = 'inner')
    {
        $current_letter = $this->next_letter++;
        $info = new \stdClass();
        
        $info->name = $name;
        $info->letter = $current_letter;
        $info->join = $join;
        
        $this->tables[$name] = $info;
        $this->reverse_tables[$current_letter] = $info;
        
        return $current_letter;
    }
}