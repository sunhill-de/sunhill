<?php
/**
 * @file ObjectCoreStorage.php
 * An abstract class that loads all fields of an object from a persitent storage (everything except 
 * attributes and tags) 
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-12-19
 * Localization: none
 * Documentation: 
 * Tests: 
 * Coverage: 
 */

namespace Sunhill\Storage\ObjectStorage;

use Sunhill\Storage\PersistentPoolStorage;

abstract class ObjectCoreStorage extends PersistentPoolStorage
{
    
}