<?php

/**
 * @file GivenClassNotAPropertyException.php
 * Provides the given exception
 * 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: not necessary
 * Documentation: complete
 * Tests: none
 * Coverage: none
 */
namespace Sunhill\Managers\Exceptions;

/**
 * Is raised when registerProperty() is called with a class that is not a property
 * @author klaus
 *
 */
class GivenClassNotAPropertyException extends PropertiesManagerException {}