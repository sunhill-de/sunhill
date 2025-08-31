<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\MySqlConnection;
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Helpers\DiffCreator;

/**
 * @file sunhill_helpers.php
 * A collection of gobally avaiable functions that are useful in the sunhill framework
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2025-08-10
 * Create date: 2024-10-05
 * Localization: not needed
 * Documentation: all public
 * Wiki: /Little_helper
 * Tests: Unit/Helpers/DatabaseUtilsTest.php, DiffCreatorTest.php, HelpersTest.php
 * Coverage Unit: 
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
    $expected = new \stdClass();
    foreach ($values as $field=>$info) {
        if (is_array($info)) {
            $expected->$field = makeStdClass($info);
        } else {
            $expected->$field = $info;
        }
    }
    return $expected;
}

/**
 * Returns an message depending on $variable is a scalar. If it is a scalar, the message part :variable is replaced by it otherwise
 * it is replaces by replace (by default an empty string)
 * 
 * @param string $message
 * @param mixed $variable
 * @param string $replace
 * @return string
 */
function getScalarMessage(string $message, mixed $variable,string $replace = ""): string
{
    return str_replace(':variable',(is_scalar($variable))?"'$variable'":$replace,$message);
}

/**
 * Returns true if the table with the given name exists in the current database
 * 
 * @param string $table_name
 * @return bool
 */
function DBTableExists(string $table_name): bool
{
    return Schema::hasTable($table_name);
}

/**
 * Returns true if the table $table_name in the current database provides a column with the name $column_name
 * 
 * @param string $table_name
 * @param string $column_name
 * @return bool
 */
function DBTableHasColumn(string $table_name, string $column_name): bool
{
    return Schema::hasColumn($table_name, $column_name);
}

/**
 * Helper that unifies column types
 * 
 * @param string $input
 * @return string
 */
function DBUnifyType(string $input): string
{
    $input = strtolower($input);
    switch ($input) { 
        case 'varchar':
            return 'string';           
    }
    return $input;
}

/**
 * Returns the column type of the given column of the given table
 * 
 * @param string $table_name
 * @param string $column_name
 * @return string
 */
function DBTableColumnType(string $table_name, string $column_name): string
{
    return DBUnifyType(DB::getSchemaBuilder()->getColumnType($table_name, $column_name));
}

/**
 * Returns additional parameters of the column
 * 
 * @param string $table_name
 * @param string $column_name
 * @return \stdClass
 */
function DBTableColumnAdditional(string $table_name, string $column_name): \stdClass
{
    $result = new \stdClass();

    if (DB::connection() instanceof SQLiteConnection) {
        $query = DB::select("PRAGMA table_info($table_name)");
        $i = 0;
        while (($i < count($query)) && ($query[$i]->name !== $column_name)) { $i++; }
        if ($i == count($query)) {
            throw new FieldNotAvaiableException("The column $column_name does not exist in this table");
        }
        $result->name = $column_name;
        $result->type = DBUnifyType($query[$i]->type);
        $result->nullable = !$query[$i]->notnull;
        $result->default = $query[$i]->dflt_value;
    } else if (DB::connection() instanceof MySqlConnection) {
        $query = DB::select('show full columns from $table_name where Field = "$column_name"');
        
    }
    
    return $result;
}

/**
 * Creates a diff of the two arrays
 * 
 * @param unknown $given
 * @param unknown $new
 * @param bool $accept_given_asterik
 * @param bool $accept_new_asterik
 * @return \stdClass
 */
function get_diff($given, $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false): \stdClass
{
    $result = new DiffCreator();
    return $result->getDiff($given, $new, $accept_given_asterik, $accept_new_asterik);
}
