<?php

namespace Sunhill\Tests\Unit\Storage\AbstractObjectStorage;

use Sunhill\Storage\AbstractObjectStorage;
use Sunhill\Storage\stdClass;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Query\QueryParser\QueryNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\StringNode;

class DummyAbstractObjectStorage extends AbstractObjectStorage
{
    public static $DataPool;
    
    public static $Data = [
        'objects'=>[
            [
                'id'=>1,
                '_classname'=>'ChildObject',
                '_uuid'=>'de4961ab-f548-4402-8adc-f6d33e80134e',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',                
            ],  
            [
                'id'=>2,
                '_classname'=>'ChildObject',
                '_uuid'=>'5a1f9541-4245-4e20-99c6-2229c9b95707',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>3,
                '_classname'=>'ChildObject',
                '_uuid'=>'e7e1dc3f-9db0-42b1-b141-41ae94db9c5c',
                '_read_cap'=>'reader',
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>4,
                '_classname'=>'ChildObject',
                '_uuid'=>'8807dbef-eb26-41f9-ac89-3744cfb262a0',
                '_read_cap'=>null,
                '_modify_cap'=>'modifier',
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 20:55:00',
            ],
        ],
        'parentobjects'=>[
            ['id'=>1,'parent_int'=>123,'parent_string'=>'ABC'],
            ['id'=>2,'parent_int'=>234,'parent_string'=>'BCE'],
            ['id'=>3,'parent_int'=>345,'parent_string'=>'CDE'],
            ['id'=>4,'parent_int'=>456,'parent_string'=>'DEF'],
        ],
        'parentobjects_parent_sarray'=>[
            ['container_id'=>1,'index'=>0,'element'=>321],
            ['container_id'=>1,'index'=>1,'element'=>432],
            ['container_id'=>1,'index'=>2,'element'=>543],
            ['container_id'=>2,'index'=>0,'element'=>323],
            ['container_id'=>2,'index'=>1,'element'=>434],
            ['container_id'=>2,'index'=>2,'element'=>545],
        ],
        'childobjects'=>[
            ['id'=>1,'child_int'=>111,'child_string'=>'AAA'],
            ['id'=>2,'child_int'=>222,'child_string'=>'BBB'],
            ['id'=>3,'child_int'=>333,'child_string'=>'CCC'],            
            ['id'=>4,'child_int'=>444,'child_string'=>'DDD'],
        ],
        'childobjects_child_sarray'=>[
            ['container_id'=>1,'index'=>0,'element'=>322],
            ['container_id'=>1,'index'=>1,'element'=>433],
            ['container_id'=>1,'index'=>2,'element'=>544],
            ['container_id'=>3,'index'=>0,'element'=>329],
            ['container_id'=>3,'index'=>1,'element'=>439],
            ['container_id'=>3,'index'=>2,'element'=>549],            
        ],
        'tagobjectassigns'=>[
            ['container_id'=>1,'tag_id'=>1],
            ['container_id'=>1,'tag_id'=>2],
            ['container_id'=>2,'tag_id'=>2],
            ['container_id'=>2,'tag_id'=>3],
            ['container_id'=>3,'tag_id'=>4],
        ],
        'attributeobjectassigns'=>[
            ['container_id'=>1,'attribute_id'=>1],
            ['container_id'=>1,'attribute_id'=>2],
            ['container_id'=>2,'attribute_id'=>2],
            ['container_id'=>2,'attribute_id'=>3],
            ['container_id'=>3,'attribute_id'=>4],
        ],
    ];
    
    public function __construct()
    {
        $this->setStructure(ChildObject::getExpectedStructure());    
    }
    
    public static function getRecords(string $storage_id, string $key, $value)
    {
        $result = [];
        foreach (static::$DataPool[$storage_id] as $data_set) {
            if ($data_set[$key] == $value) {
                $result[] = $data_set;
            }
        }
        return $result;
    }
    
    /**
     * Updates the storage with the subid. It uses $key to identiy the record(s) and sets the givenvalues
     *
     * @param unknown $key
     * @param unknown $values
     */
    protected function updateStorageSubid(string $subid, int $key, array $values, string $key_field = 'id')
    {         
        foreach ($values as $value_key=>$value) {
            static::$DataPool[$subid][$key-1][$value_key] = $value;
        }
    }
    
    /**
     * Deletes all references to key from the given
     *
     * @param unknown $key
     */
    protected function deleteStorageSubid(string $subid, int $key, string $key_field = 'id')
    {
        for ($i=0;$i<count(static::$DataPool[$subid]);$i++) {
            if (static::$DataPool[$subid][$i][$key_field] == $key) {
                unset(static::$DataPool[$subid][$i]);
            }
        }
        static::$DataPool[$subid] = array_values(static::$DataPool[$subid]); // Reindex
    }
    
    protected function insertObjects(array $values): int
    {
        $this->setID(count(static::$DataPool['objects'])+1);
        $values['id'] = $this->getID();
        $this->insertStorageSubid('objects', $values);
        return $this->getID();
    }
    
    /**
     * Inserts into the given storage subid the given values. If value is a array of arrays then insert every entry as a separate record
     *
     * @param string $subid
     * @param array $values
     */
    protected function insertStorageSubid(string $subid, array $values)
    {
        if (isset($values[0])) {
            foreach ($values as $value) {
                $this->insertStorageSubid($subid, $value);
            }
        } else {
            static::$DataPool[$subid][] = $values;
        }
    }
    
    /**
     * Loads from the given storage subif the values with the given key
     *
     * @param string $subid
     * @param int $key
     * @return array
     */
    protected function loadStorageSubid(string $subid, int $key, string $key_field = 'id'): array|\stdClass
    {
        $result = [];
        foreach(static::$DataPool[$subid] as $record) {
            if ($record[$key_field] == $key) {
                $result[] = makeStdClass($record);
            }
        }
        return $result;
        if (count($result) == 1) {
            return $result[0];
        } else {
            return $result;
        }
    }
    
    private function getBinaryResult(\stdClass $record, BinaryNode $node)
    {
        $left = $this->getNodeResult($record, $node->left());
        $right = $this->getNodeResult($record, $node->right());
        switch ($node->getType()) {
            case '<':
                return $left < $right;
            case '<=':
                return $left <= $right;
            case '>':
                return $left > $right;
            case '>=':
                return $left >= $right;
            case '=':
                return $left == $right;
            case '<>':
                return $left <> $right;
        }
    }
    
    private function getNodeResult(\stdClass $record, ?Node $node)
    {
        if (is_null($node)) {
            return true;
        }
        switch ($node::class) {
            case BinaryNode::class:
                return $this->getBinaryResult($record, $node);
            case IdentifierNode::class:
                $name = $node->getName();
                return $record->$name;
            case IntegerNode::class:    
            case FloatNode::class:
            case StringNode::class:
                return $node->getValue();                
        }
    }
    
    private function assembleRecord(array $main_record_storage): \stdClass
    {
        $this->setID(null);
        $this->loaded = false;
        $this->load($main_record_storage['id']);
        return makeStdClass($this->values);
    }
    
    private function filterDatasets(string $main_storage_id, QueryNode $node)
    {
        $data_set = [];
        foreach (static::$DataPool[$main_storage_id] as $main_storage_record) {
            $record = $this->assembleRecord($main_storage_record);
            if ($this->getNodeResult($record, $node->getWhere())) {
                $data_set[] = $record;
            }
        }        
        return $data_set;
    }
    
    private function sortDataset(array $data_set, QueryNode $node)
    {
            
    }
    
    private function doExecuteSelect(QueryNode $node)
    {
        $data_set = $this->filterDatasets($this->structure->options['storage_id']->value, $node);
        $this->sortDataset($data_set, $node);
        
        return $data_set;
    }
    
    protected function doExecuteQuery(QueryNode $node)
    {
        switch ($node->verb()) {
            case 'select':
                return $this->doExecuteSelect($node);
        }
    }
    protected function getCurrentStructure(string $storage_subid): \stdClass
    {
        $return = new \stdClass();
        foreach (static::$DataPool as $subid => $values) {
            if ($subid == $storage_subid) {
                $return->$subid = '*';
            } else if (str_starts_with($subid, $storage_subid)) {
                $return->$subid = '*';                
            }
        }
        return $return;
    }

    private function patchGiven(\stdClass $diff)
    {
        foreach ($diff->given as $key => $value) {
            if (!isset($diff->new->$key)) {
                unsset(static::$DataPool[$key]);
            }
        }
    }
    
    private function patchNew(\stdClass $diff)
    {
        foreach ($diff->new as $key => $value) {
            if (!isset($diff->given->$key)) {
                static::$DataPool[$key] = [];
            }
        }        
    }
    
    protected function patchStructure(\stdClass $diff)
    {
        $this->patchGiven($diff);
        $this->patchNew($diff);
    }

    
}