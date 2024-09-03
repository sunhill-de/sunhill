<?php
/**
 * @file Duration.php
 * A semantic class for describing the duration of an action
 * Lang en
 * Reviewstatus: 2024-02-16
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\Properties\Semantics;

use Sunhill\Properties\Types\TypeInteger;

class Duration extends TypeInteger
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public function getSemantic(): string
    {
        return 'duration';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     */
    public function getSemanticKeywords(): array
    {
        return ['time'];
    }
 
    /**
     * Returns the unique id string for the unit of this property
     *
     * @return string
     */
    public function getUnit(): string
    {
        return 'second';
    }
    
    /**
     * Returns the value in a human readable format. The possible read restrictions are already
     * checked
     * @param unknown $input
     * @return unknown
     * @test AbstractPropertyTest::testFormatForHuman()
     */
    protected function formatForHuman($input)
    {
        if ($input >= 31536000) {
            $val1 = floor($input / 31536000);
            $val2 = floor(($input % 31536000) / 86400);
            if ($val1 == 1) {
                $return = __(':val1 year',['val1'=>$val1]).' ';
            } else {
                $return = __(':val1 years',['val1'=>$val1]).' ';                
            }
            if ($val2 == 1) {
                $return .= __(':val2 day',['val2'=>$val2]);
            } else {
                $return .= __(':val2 days',['val2'=>$val2]);
            }
        } else if ($input >= 86400) {
            $val1 = floor($input / 86400);
            $val2 = floor(($input % 86400) / 3600);
            if ($val1 == 1) {
                $return = __(':val day',['val'=>$val1]).' ';
            } else {
                $return = __(':val days',['val'=>$val1]).' ';
            }
            if ($val2 == 1) {
                $return .= __(':val hour',['val'=>$val2]);
            } else {
                $return .= __(':val hours',['val'=>$val2]);
            }            
        } else if ($input >= 60*60) {
            $val1 = floor($input / 3600);
            $val2 = floor(($input % 3600) / 60);
            if ($val1 == 1) {
                $return = __(':val hour',['val'=>$val1]).' ';
            } else {
                $return = __(':val hours',['val'=>$val1]).' ';
            }
            if ($val2 == 1) {
                $return .= __(':val minute',['val'=>$val2]);
            } else {
                $return .= __(':val minutes',['val'=>$val2]);
            }
        } else if ($input >= 60) {
            $val1 = floor($input / 60);
            $val2 = $input % 60;
            if ($val1 == 1) {
                $return = __(':val minute',['val'=>$val1]).' ';
            } else {
                $return = __(':val minutes',['val'=>$val1]).' ';
            }
            if ($val2 == 1) {
                $return .= __(':val second',['val'=>$val2]);
            } else {
                $return .= __(':val seconds',['val'=>$val2]);
            }
        } else {
            return __(':val seconds',['val'=>$input]);
        }
        return $return;
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'duration');
        static::addInfo('description', 'A duration of something.', true);
        static::addInfo('type', 'semantic');
    }
    
}