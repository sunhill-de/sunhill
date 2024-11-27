<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\MySqlConnection;
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;

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

function DBUnifyType(string $input): string
{
    $input = strtolower($input);
    switch ($input) { 
        case 'varchar':
            return 'string';           
    }
    return $input;
}

function DBTableColumnType(string $table_name, string $column_name): string
{
    return DBUnifyType(DB::getSchemaBuilder()->getColumnType($table_name, $column_name));
}

function DBTableColumnAdditional(string $table_name, string $column_name): \stdClass
{
    $result = new \stdClass();

    if (DB::connection() instanceof SQLiteConnection) {
        $query = DB::select("PRAGMA table_info($table_name)");
        $i = 0;
        while (($query[$i]->name !== '$column_name') && ($i++ < count($query))) { }
        if ($i == count($query)) {
            throw new FieldNotAvaiableException("The column $column_name does not exist in this table");
        }
        $result->name = $column_name;
        $result->type = DBUnifyType($result);
    } else if (DB::connection() instanceof MySqlConnection) {
        $query = DB::select('show full columns from $table_name where Field = "$column_name"');
        
    }
    
    return $result;
}
