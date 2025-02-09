<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Query\BasicQuery;
use Illuminate\Support\Collection;

class DummyQuery extends BasicQuery
{
    
    public $assembled_query = '';
    
    private function addWhereConditions()
    {
        $this->assembled_query .= 'where:(';
        $first = true;
        foreach ($this->where_statements as $field) {
            $this->assembled_query .= ($first?'':',').'[';
            $this->assembled_query .= $field->connect.';'.$field->field.';';
            $this->assembled_query .= $field->operator.';'.$field->condition;
            $this->assembled_query .= ']';                
            $first = false;
        }
        $this->assembled_query .= ')';        
    }
    
    private function addOrderConditions()
    {
        $this->assembled_query .= ',order:(';
        $first = true;
        foreach ($this->order_fields as $field) {
            $this->assembled_query .= ($first?'':',').$field;
            $first = false;
        }
        $this->assembled_query .= ')';
    }
    
    private function addGroupConditions()
    {
        $this->assembled_query .= ',group:(';
        $first = true;
        foreach ($this->group_fields as $field) {
            $this->assembled_query .= ($first?'':',').$field;
            $first = false;
        }
        $this->assembled_query .= ')';
    }
    
    private function addOffset()
    {
        $this->assembled_query .= ',offset:('.$this->offset.')';    
    }
    
    private function addLimit()
    {
        $this->assembled_query .= ',limit:('.$this->limit.')';
    }
    
    protected function doAssembleQuery()
    {
        $this->addWhereConditions();
        $this->addOrderConditions();
        $this->addGroupConditions();
        $this->addOffset();
        $this->addLimit();
    }
    
}