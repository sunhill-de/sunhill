<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call([
            ArrayOnlyChildObjects_child_sarraySeeder::class,
            ArrayOnlyChildObjectsSeeder::class,
            Attr_float_attributeSeeder::class,
            Attr_int_attributeSeeder::class,
            Attr_str_attributeSeeder::class,
            AttributeObjectAssignsSeeder::class,
            AttributesSeeder::class,
            BigParentsSeeder::class,
            ChildObjects_child_sarraySeeder::class,
            ChildObjectsSeeder::class,
            DummiesSeeder::class,
            DummyChildrenSeeder::class,
            DummyGrandChildrenSeeder::class,
            ObjectsSeeder::class,
            ParentObjects_parent_sarraySeeder::class,
            ParentObjectsSeeder::class,
            ParentReferences_parent_rarraySeeder::class,
            ParentReferencesSeeder::class,
            SkippingDummyChildrenSeeder::class,
            SkippingDummyGrandChildrenSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class,
            TagsSeeder::class
        ]);
    }
    
}