<?php
/**
 * @file InvalidStatementException.php
 * Provides the InvalidStatementException
 * Lang en
 * Reviewstatus: 2025-02-09
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Query\Exceptions;

/**
 * This excpetion is raised when an order(), limit(), offset(), group() or where() statement was uses
 * with a finalisation that need none of those (like insert or upsert)
 * 
 * @author klaus
 *
 */
class InvalidStatementException extends QueryException {}