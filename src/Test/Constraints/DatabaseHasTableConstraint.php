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
class DatabaseHasTableConstraint extends Constraint {
    
    /**
     * The matches method test if the given table exists in the current database 
     * {@inheritDoc}
     * @see \PHPUnit\Framework\Constraint\Constraint::matches()
     */
    public function matches($other) : bool {
        return Schema::hasTable($other);
/*        $tables = DB::select('SHOW TABLES LIKE "'.$other.'"');
        return !empty($tables); */
    }
    
    /**
     * Returns an addition to phpunit's return to make a correct sentence
     * {@inheritDoc}
     * @see \PHPUnit\Framework\SelfDescribing::toString()
     */
    public function toString() : string {
        return 'has this table';
    }
    
    protected function failureDescription($other): string
    {
        return 'Database ' . $this->toString();
    }

}
