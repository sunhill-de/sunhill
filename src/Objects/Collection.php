<?php
/**
 * @file Collection.php
 * Is also a database storable record but when inherited it "flattens" the used database to only two tables 
 * (the objects table and the data table)
 * Lang en
 * Reviewstatus: 2024-11-13
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 
 *
 * Wiki: 
 */

namespace Sunhill\Objects;

/**
 * The basic class for default storable records (in this case objects)
 * @author klaus
 *
 */
class Collection extends ORMObject
{
   
    protected static $inherited_inclusion = 'include';

    protected static function setupInfos()
    {
        static::addInfo('name', 'Collection');
        static::addInfo('description', 'The basic class for collections.', true);
        static::addInfo('storage_id', 'objects'); // doesn't matter, the root is always objects
        static::addInfo('initiable', false);
    }
    
}