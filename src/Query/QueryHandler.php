<?php
/**
 * @file QueryHandler.php
 * Provides the QueryHandler basic class
 * Lang en
 * Reviewstatus: 2025-02-18
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryHandlerTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;
use Sunhill\Query\Exceptions\QueryObjectExpectedException;

/**
 * A basic class for BasicQuery, Executor and Checker
 */
class QueryHandler extends Base
{

    protected $query_object;

    /**
     * Sets the query_object
     */
    protected function setQueryObject(QueryObject $query): static
    {
        $this->query_object = $query;
        return $this;
    }

    /**
     * Check if a query_object was set. If not throw an exception otherwise return it
     */
    protected function getQueryObject(): QueryObject    
    {
        if (is_null($this->query_object)) {
          throw new QueryObjectExpectedException("A QueryObject was expected but not defined.");
        }
        return $this->query_object;
    }
}
