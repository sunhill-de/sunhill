<?php
/**
 * @file _PropertyInformation.php
 * A class that stores informations about a property 
 * Lang en
 * Reviewstatus: 2025-04-17
 * Create date: 2025-04-17
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage Unit: 0% (2025-06-06)
 *
 */
namespace Sunhill\SystemProperties;

use Sunhill\Properties\ElementBuilder;
use Sunhill\Semantics\Name;
use Sunhill\Types\TypeEnum;
use Sunhill\Objects\ORMObject;

class _PropertyInformation extends ORMObject
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(Name::class, 'name');
        $builder->addProperty(Name::class, 'namespace');
        $builder->addProperty(Name::class, 'storage_id');
        $builder->addProperty(Name::class, 'description');
        $builder->addProperty(TypeEnum::class, 'accesstype')->setEnumValues(['integer','float','string','date','datetime','time','array','record']);
        
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', '_PropertyInformation');
        static::addInfo('description', 'A class that stores information about a property.', true);
        static::addInfo('storage_id', '_propertyinformations');
    }
    
}
