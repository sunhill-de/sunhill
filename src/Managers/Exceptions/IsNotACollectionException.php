<?php

/**
 * @file IsNotACollectionException.php
 * Is raised whenn Collection::loadCollection() is called with a class that is not a collection
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-25
 * Localization: not necessary
 * Documentation: complete
 * Tests: none
 * Coverage: none
 */
namespace Sunhill\Managers\Exceptions;

use Sunhill\Managers\ORMManagerException;

class IsNotACollectionException extends CollectionManagerException {}