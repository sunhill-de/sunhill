<?php
/**
 * @file InvalidOrderException.php
 * Provides the InvalidOrderException
 * Lang en
 * Reviewstatus: 2024-10-07
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Query\Exceptions;

/**
 * This excpetion is raised when an order direction other that asc or desc or a not sortable
 * key was used.
 * 
 * @author klaus
 *
 */
class InvalidOrderException extends QueryException {}