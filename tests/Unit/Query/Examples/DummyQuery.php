<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Query\BasicQuery;
use Illuminate\Support\Collection;

class DummyQuery extends BasicQuery
{
    
    public $assembled_query = '';
    
    private function assembleArguments($arguments)
    {
        $result = '';
        $first = true;
        foreach ($arguments as $argument) {
            $result .= ($first?"":",").$this->assembleField($argument);
            $first = false;
        }
        return $result;
    }
    
    private function assembleField($field)
    {
        switch ($field->type) {
            case 'field':
                return $field->name;
            case 'function_of_field':
                return $field->function.'( '.$this->assembleArguments($field->argument).' )';
            case 'function_of_value':
                return $field->function.'( '.$this->assembleArguments($field->argument).' )';
            case 'reference':
                return $field->parent.' -> '.$this->assembleField($field->reference);
            case 'const':
                return '"'.$field->value.'"';
            case 'callback':
                return 'callback';
            case 'subquery':
                return 'subquery';
            case 'array':
                $return = '';
                $first = true;
                foreach ($field->value as $entry) {
                    $return .= ($first?"":",").$entry;
                    $first = false;
                }
                return "[ ".$return." ]";
        }
    }
    
    private function addWhereConditions()
    {
        $this->assembled_query .= ',where:(';
        $first = true;
        foreach ($this->where_statements as $field) {
            $this->assembled_query .= ($first?'':',').'[';
            $this->assembled_query .= $field->connect.';'.$this->assembleField($field->field).';';
            $this->assembled_query .= $field->operator.';'.$this->assembleField($field->condition);
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
            $this->assembled_query .= ($first?"":",")."[";
            switch ($field->type) {
                case 'field':
                    $this->assembled_query .= $field->field.",".$field->direction;
                    break;
                case 'callback':
                    $this->assembled_query .= "callback,".$field->direction;
                    break;
            }
            $first = false;
            $this->assembled_query .= "]";
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
    
    private function addFieldsCondition()
    {
        $this->assembled_query .= 'fields:(';
        $first = true;
        foreach ($this->fields as $field) {
            $this->assembled_query .= ($first?"":",").$this->assembleField($field);
            $first = false;
        }
        $this->assembled_query .= ')';
    }
    
    protected function doAssembleQuery()
    {
        $this->addFieldsCondition();
        $this->addWhereConditions();
        $this->addOrderConditions();
        $this->addGroupConditions();
        $this->addOffset();
        $this->addLimit();
    }
    
}
