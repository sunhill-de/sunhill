<?php

/**
 * @file StorageSystemNotFoundException.php
 * Defines the StorageSystemNotFoundException
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-12-11
 * Localization: not necessary
 * Documentation: complete
 * Tests: none
 * Coverage: none
 */
namespace Sunhill\Managers\Exceptions;

/**
 * This exception is thrown when a storage system is requested that isn't implemented
 * 
 * @author lokal
 *
 */
class StorageSystemNotFoundException extends ManagerException {}