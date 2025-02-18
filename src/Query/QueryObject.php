<?php
/**
 * @file QueryObject.php
 * A class that stores information about a query to pass between builder, checker and executor
 * Lang en
 * Reviewstatus: 2025-02-18
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryObjectTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;

class QueryObject extends Base
{
    /**
     * If not empty this list lists the fields that should be returned by first(), only() or get()
     * @var array
     */
    protected $fields = [];
    
    /**
     * Here the where statement are stored (empty if there is no where condition)
     * @var array
     */
    protected $where_statements = [];
    
    /**
     * A list of field that indicate the ordering of the result
     * @var array
     */
    protected $order_fields = [];
    
    /**
     * A list of fields to which the result should be grouped
     * @var array
     */
    protected $group_fields = [];
    
    /**
     * Indicates the limit (the maximum number of results of get() and getIDs()
     * @var integer
     */
    protected $limit = 0;
    
    /**
     * Indicates the first result that should be retured of the result set of get() and getIDs()
     * @var integer
     */
    protected $offset = 0;
    
    
}