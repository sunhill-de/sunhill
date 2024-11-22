<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @file sunhill_helpers.php
 * A collection of gobally avaiable functions that are useful in the sunhill framework
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-10-05
 * Localization: not needed
 * Documentation: all public
 * Wiki: /Little_helper
 * Tests: Unit/InfoMarket/Marketeer.php
 * Coverage: unknown
 * PSR-State: complete
 */

/**
 * Creates a stdClass out of an associated array.
 * 
 * @param array $values
 * @return \StdClass
 * 
 * @wiki: /Little_helper#makeStdClass()
 */
function makeStdclass(array $values): \StdClass
{
    $result = new \StdClass();
    foreach ($values as $key => $value) {
        $result->$key = $value;
    }
    return $result;
}

function getScalarMessage(string $message, mixed $variable,string $replace = ""): string
{
    return str_replace(':variable',(is_scalar($variable))?"'$variable'":$replace,$message);
}

function DBTableExists(string $table_name): bool
{
    return Schema::hasTable($table_name);
}

function DBTableHasColumn(string $table_name, string $column_name): bool
{
    return Schema::hasColumn($table_name, $column_name);
}

function DBTableColumnType(string $table_name, string $column_name): string
{
    return DB::getSchemaBuilder()->getColumnType($table_name, $column_name);
}

function DBTableColumnAdditional(string $table_name, string $column_name): \stdClass
{
    $column = DB::connection()->getDoctrineColumn($table_name, $column_name);
    $result = new \stdClass();
    $result->length = $column->getLength();
    
    return $result;
}
