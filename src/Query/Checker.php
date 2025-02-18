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

class Checker extends Base
{
    
    protected $structure;
    
    public function __construct(\stdClass $structure)
    {
        $this->structure = $structure;
    }
}