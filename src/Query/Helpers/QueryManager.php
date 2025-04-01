<?php
/**
 * @file QueryManager.php
 * The manager for the Query facade. It capsulates some utilities for queries and makes
 * it easier to unit test.
 * .
 * Lang en
 * Reviewstatus: 2025-04-01
 * Create date: 2025-ß4-01
 * Localization: complete
 * Documentation: complete
 * @subpackage query
 * Tests: 
 * Coverage: 
 */

namespace Sunhill\Query\Helpers;


use Sunhill\Basic\Base;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Query\QueryParser\QueryParser;
use Sunhill\Parser\Nodes\StringNode;

class QueryManager extends Base
{
    
    /**
     * Builds a absgract structure tree out of the given query_string. It does no validation of the syntax. 
     * Depending on $create_string_on_error it creates a string node with the $query_string or re-throws 
     * any exception that raise while parsing.
     * 
     * @param string $query_string
     * @param bool $create_string_on_error if true (default) it creates a string node oout of $query_string
     * if an error occurs while parsing. Otherwise it re throws the \Exception
     * @return Node
     */
    public function parseQueryString(string $query_string, bool $create_string_on_error = true): Node
    {
        $parser = new QueryParser();
        try {
            $result = $parser->parseQueryString($query_string);
        } catch (\Sunhill\Parser\Exceptions\ParsingSubsystemException $e) {
            if ($create_string_on_error) {
                $result = new StringNode($query_string);
            } else {
                throw $e;
            }
        }
        
        return $result;
    }
    
}