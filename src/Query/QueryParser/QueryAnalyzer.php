<?php
/**
 * @file QueryAnalyzer.php
 * The parser that analyzes query strings 
 * Lang en
 * Reviewstatus: 2025-04-25
 * Creation date: 2025-04-25
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\Analyzer;

class QueryAnalyzer extends Analyzer
{
        
    public function __construct()
    {
        $this->loadLanguageDescriptor(new QueryParserLanguage());    
    }
        
}