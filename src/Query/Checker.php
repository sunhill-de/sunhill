<?php
/**
 * @file Checker.php
 * A base class for query checker
 * Lang en
 * Reviewstatus: 2025-02-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/CheckerTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;

class Checker extends QueryHandler
{
    
    public function __construct(QueryObject $query)
    {
        $this->setQueryObject($query);
    }

    private function checkFields(array $fields)
    {
    }

    private function checkWhereCondition(\stdClass $condition)
    {
    
    }
    
    private function checkWhereStatements(array $where)
    {
        foreach ($where as $where_condition) {
            $this->checkWereCondition($where_condition);
        }    
    }
    
    public function check()
    {
        if ($fields = $this->getQueryObject()->getFields()) {
            $this->checkFields($fields);
        }
        if ($where = $this->getQueryObject()->getWhereStatements()) {
            $this->checWhereStatements($fields);
        }
    }
    
}
