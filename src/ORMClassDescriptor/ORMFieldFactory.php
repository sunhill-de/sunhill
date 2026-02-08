<?php
/**
 * @file ORMClassDescriptor/ORMFieldFactory.php
 * Provides the ORMFieldFactory class.
 * Last reviewed: 2026-02-06
 * Created: 2026-02-06
 * Author: Klaus
 */

namespace Sunhill\ORMClassDescriptor;

use Sunhill\ORMField\ORMField;

interface ORMFieldFactory
{
    
    public function string(string $name): ORMField;
    
    public function integer(string $name): ORMField;
    
    public function float(string $name): ORMField;
    
    public function date(string $name): ORMField;
    
    public function time(string $name): ORMField;
    
    public function datetime(string $name): ORMField;

    public function boolean(string $name): ORMField;
    
    public function object(string $name): ORMField;
    
    public function array(string $name): ORMField;

    public function map(string $name): ORMField;
    
}