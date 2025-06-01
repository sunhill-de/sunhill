<?php

namespace Sunhill\Tests\Unit\Tags\Examples;

use Sunhill\Tags\AbstractTagStorage;

class DummyTagStorage extends AbstractTagStorage
{
    
    public static $tags = [
        1=>['id'=>1,'name'=>'TagA','parent_id'=>null],
        2=>['id'=>2,'name'=>'TagB','parent_id'=>null],
        3=>['id'=>3,'name'=>'TagC','parent_id'=>null],
        4=>['id'=>4,'name'=>'TagD','parent_id'=>null],
        5=>['id'=>5,'name'=>'TagA','parent_id'=>4],
        6=>['id'=>6,'name'=>'TagE','parent_id'=>null],
        7=>['id'=>7,'name'=>'TagF','parent_id'=>7],
        8=>['id'=>8,'name'=>'TagG','parent_id'=>8],
    ];
    protected function searchTag(array $condition)
    {
        $result = [];
        $search_key = array_keys($condition)[0];
        $search_value = array_values($condition)[0];
        foreach (static::$tags as $id => $tag) {
            if ($tag[$search_key] == $search_value) {
                $result[] = makeStdClass($tag);
            }
        }
        return $result;
    }
    
}