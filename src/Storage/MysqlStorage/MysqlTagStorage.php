<?php

/**
 * @file MysqlTagStorage.php
 * A tag storage that uses mysql/mariadb to store tags
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-05-28
 * Create date: 2025-05-28
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage Unit: 100.0 (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\AbstractTagStorage;

class MysqlTagStorage extends AbstractTagStorage
{
    protected function searchTag(array $condition)
    {
        return DB::table('tags')->where($condition)->get();
    }
}
