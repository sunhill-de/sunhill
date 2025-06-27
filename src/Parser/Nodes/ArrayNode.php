<?php
/**
 * @file ArrayNode.php
 * A node that represents an array of nodes
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 85.71 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

class ArrayNode extends Node
{
        
    public function __construct(?Node $first_element)
    {
        if (!is_null($first_element)) {
            parent::__construct('array',['values'=>[$first_element]]);
        } else {
            parent::__construct('array',['values'=>[]]);            
        }
    }

    public function addElement(Node $element): static
    {
        $this->children['values'][] = $element;
        
        return $this;
    }
    
    public function getElement(int $index): ?Node
    {
        if (($index >= 0) && ($index < $this->elementCount())) {
            return $this->children['values'][$index];
        }
        return null;
    }
    
    public function elementCount(): int
    {
        return count($this->children['values']);
    }
    
    public function getDatatype(): ?string
    {
        return 'array';
    }
    
    protected function getLowestSubtype(?string $first, ?string $second): ?string
    {
        if ($first == $second) {
            return $first;
        }
        if (($first == 'mixed') || ($second == 'mixed')) {
            return 'mixed';
        }
        if ((($first == 'integer') && ($second == 'float')) ||
            (($first == 'float') && ($second == 'integer'))) {
                return 'float';
            }
        if ((($first == 'date') && ($second == 'datetime')) ||
            (($first == 'datetime') && ($second == 'date'))) {
                    return 'datetime';
                }
        if (is_null($first) || is_null($second)) {
                return null;
            }
        return 'mixed';    
    }
    
    public function getDataSubtype(): ?string
    {
        switch ($this->elementCount()) {
            case 0:
                return 'empty';
            case 1:
                return $this->getElement(0)->getDatatype();
            case 2:
                return $this->getLowestSubtype($this->getElement(0)->getDatatype(), $this->getElement(1)->getDatatype());
            default:
                $return = $this->getLowestSubtype($this->getElement(0)->getDatatype(), $this->getElement(1)->getDatatype());
                for ($i=2;$i<$this->elementCount();$i++) {
                    $return = $this->getLowestSubtype($return,$this->getElement($i)->getDatatype());
                }
                return $return;
        }
    }
        
}
