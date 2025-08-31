<?php

/**
 * @file NotAQueryNodeException.php
 * Provides the NotAQueryNodeException
 * Lang en
 * Reviewstatus: 2025-06-13
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

/**
 * This exception is thrown when an analyzer or executor is called for an pooled storage and the node
 * is not an query node
 *
 * @author klaus
 */
class NotAQueryNodeException extends StorageException {}
