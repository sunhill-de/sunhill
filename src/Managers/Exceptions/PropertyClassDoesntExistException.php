<?php

/**
 * @file PropertyClassDoesntExistsException.php
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
 * Is raised when registerProperty() is called with a property class that is not accessible
 * @author klaus
 *
 */
class PropertyClassDoesntExistException extends PropertiesManagerException {}