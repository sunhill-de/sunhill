<?php
/**
 * @file QueryParser.php
 * The parser that parses query strings 
 * Lang en
 * Reviewstatus: 2025-03-31
 * Localization: complete
 * Documentation: complete
 * Tests: not unit testable
 * @note: This class is not unit testable because it is just a simplified bundle of a parser and lexer combined with a specialized language for queries
 * Coverage:
 */

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\Parser;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Lexer;

class QueryParser extends Parser
{
        
    public function __construct()
    {
        $this->loadLanguageDescriptor(new QueryParserLanguage());    
    }
    
    public function parseQueryString(string $query_string): Node
    {
        $lexer = new Lexer($query_string);
        $lexer->loadLanguageDescriptor(new QueryParserLanguage());
        
        return $this->parse($lexer);
    }
    
}