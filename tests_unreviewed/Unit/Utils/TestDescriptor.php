<?php
namespace Sunhill\Tests\Unit\Utils;

/**
 *
 * @file UtilDescriptorTest.php
 * lang: en
 * dependencies: FilemanagerTestCase
 */
use Sunhill\Utils\Descriptor;

class TestDescriptor extends Descriptor {
    
    
    protected $autoadd = false;
    
    public $flag = '';
    
    protected function setupFields() {
        $this->test = 'ABC';
        $this->test2 = 'DEF';
    }
    
    protected function test_changing(Descriptor $diff) {
        if ($diff->from == 'ABC') {
            return true;
        } else {
            return false;
        }
    }
    
    protected function test_changed(Descriptor $diff) {
        $this->flag = $diff->from."=>".$diff->to;
    }
}
