<?php
/**
 * @file Tag.php
 * A class that represents as tag
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\Tags;

use Sunhill\Basic\Base;

class Tag extends Base
{
    
    protected $id;
    
    protected $name;
    
    protected $parent;
    
    public function __construct(int $id)
    {
        parent::__construct();
        $this->id = $id;
    }
    
    public function getID(): int
    {
        return $this->id;
    }
    
    protected function loaded(): bool
    {
        return !is_null($this->name);
    }
}