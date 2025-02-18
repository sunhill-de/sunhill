<?php

/**
 * @file Executor.php
 * A class that executes the assmbles and validated query and returns the wanted results
 * Lang en
 * Reviewstatus: 2025-02-18
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/ExecutorTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;

class Executor extends QueryHandler
{
  public function __construct(QueryObject $query)
  {
     $this->setQueryObject($query);
  }
  
}    
