<?php

/**
 * @file NotAllowedInclusionException.php
 * Provides the NotAllowedInclusion
 * Lang en
 * Reviewstatus: 2024-10-25
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Properties\Exceptions;

/**
 * An exception that is raised, when appendElement() is called with the inclusion "embed" and
 * the passed property is not an ancestor of the owning property.
 * 
 * @author Klaus Dimde
 */
class NotAllowedInclusionException extends PropertyException 
{
}
