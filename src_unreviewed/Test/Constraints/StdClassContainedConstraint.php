<?php

/**
 * @fileDescriptor DatabaseHasTableConstraint.php
 * Provides the DatabaseHasTableContraint class an extenstion to phpunit for testing if a database has
 * a table
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-09-08
 * Localization: unlocalized
 * Documentation: complete
 * Tests: Unit/Constraints/DatabaseHasTableConstraintTest.php
 * Coverage: unknown
 */

namespace Sunhill\Test\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use Illuminate\Support\Facades\Schema;

/**
 * The HasLinkConstraint class is an extension to the FileConstraint which is in turn an extension to phpunits 
 * Constraint class. It provides an additional test for unit tests in php unit.
 * @author Klaus Dimde
 *
 */
class StdClassContainedConstraint extends Constraint {
    
    protected $search;
    
    public function __construct($search)
    {
        $this->search = $search;    
    }
    
    /**
     * The matches method test if the given table exists in the current database 
     * {@inheritDoc}
     * @see \PHPUnit\Framework\Constraint\Constraint::matches()
     */
    public function matches($other) : bool 
    {
        try {
            foreach ($other as $key => $value) {
                if ($this->search->$key !== $value) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
    /**
     * Returns an addition to phpunit's return to make a correct sentence
     * {@inheritDoc}
     * @see \PHPUnit\Framework\SelfDescribing::toString()
     */
    public function toString() : string {
        return ' is contained';
    }
    
    protected function failureDescription($other): string
    {
        return $this->toString();
    }

}
