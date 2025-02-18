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

class Executor extends Base
{
  
  protected $query_object;
  
  public function __construct(QueryObject $query)
  {
      $this->query_object = $query;
  }
  
}    
