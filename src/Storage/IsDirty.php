<?php
/**
 * @fileIsDirty.php
 * Helping class that indicates that a previously unknown value was added ans therfore has no
 * shadow entry. This indicates that it is "modifies" but ignored when rolling back.
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\Storage;

class IsDirty {}