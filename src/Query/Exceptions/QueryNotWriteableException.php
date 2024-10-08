<?php
/**
 * @file QueryNotWriteableException.php
 * Provides the QueryNotWriteableException
 * Lang en
 * Reviewstatus: 2024-10-07
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Query\Exceptions;

/**
 * Is raised when delete(), update() or insert() is called and the query is marked as not writeable
 * @author klaus
 *
 */
class QueryNotWriteableException extends QueryException {}