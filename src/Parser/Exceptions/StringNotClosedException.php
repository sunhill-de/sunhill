<?php
/**
 * @file StringNotClosedException.php
 * Provides the StringNotClosedException
 * Lang en
 * Reviewstatus: 2025-03-14
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Parser\Exceptions;

/**
 * An exception that is raised, when a string with " or ' doesn't have a closing pair
 * @author klaus
 *
 */
class StringNotClosedException extends LexerException {}