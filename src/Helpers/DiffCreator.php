<?php

/**
 * @file DiffCreator.php
 * Provides a class that creates diffs out of two arrays
 * Lang en
 * Reviewstatus: 2024-09-01
 * Create date: 2025-08-10
 * Localization: incomplete
 * Documentation: complete
 * Tests: BasicTest.php
 * Coverage Unit: 97.83% (2025-06-06)
 */

namespace Sunhill\Helpers;

use Sunhill\Basic\Base;

/**
 * A helper class that is used by the get_diff() function
 *
 * @author klaus
 */
class DiffCreator extends Base
{
    /**
     * Traverses the given structure and searches for elements that are removed in the new structure
     *
     * @return \stdClass
     */
    private function traverseGiven(\stdClass $given, \stdClass $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        $result = new \stdClass;
        foreach ($given as $key => $entry) {
            if (! isset($new->$key)) {
                $result->$key = $entry;
            } else {
                if ($this->isTraversable($entry)) {
                    if (($new->$key == '*') && ($accept_new_asterik)) {
                        continue;
                    }
                    $subdiff = $this->traverseGiven($given->$key, $new->$key, $accept_given_asterik, $accept_new_asterik);
                    if (! empty((array) $subdiff)) {
                        $result->$key = $subdiff;
                    }
                } else {
                    if (($entry !== '*') && ! (($new->$key == '*') && ($accept_new_asterik)) && ($given->$key !== $new->$key)) {
                        $result->$key = $given->$key;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Traverses the new structures and searches for elements that are newly added
     *
     * @return \stdClass
     */
    private function traverseNew(\stdClass $given, \stdClass $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        $result = new \stdClass;
        foreach ($new as $key => $entry) {
            if (! isset($given->$key)) {
                $result->$key = $entry;
            } else {
                if ($this->isTraversable($entry)) {
                    if (($given->$key == '*') && ($accept_given_asterik)) {
                        continue;
                    }
                    $subdiff = $this->traverseNew($given->$key, $new->$key, $accept_given_asterik, $accept_new_asterik);
                    if (! empty((array) $subdiff)) {
                        $result->$key = $subdiff;
                    }
                } else {
                    if (($entry !== '*') && ! (($given->$key == '*') && ($accept_given_asterik)) && ($given->$key !== $new->$key)) {
                        $result->$key = $new->$key;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Retraverses the given structure and searches for changees
     *
     * @param  unknown  $given
     * @param  unknown  $new
     * @param  unknown  $new_array
     */
    private function retraverseGiven($given, &$new, $new_array)
    {
        foreach ($given as $key => $entry) {
            if (! isset($new->$key) && (isset($new_array->$key))) {
                $new->$key = new \stdClass;
                if ($this->isTraversable($given->$key)) {
                    $this->retraverseGiven($given->$key, $new->$key, $new_array->$key);
                }
            }
        }
    }

    /**
     * Retraverses the new structure and searches for changes
     *
     * @param  unknown  $given
     * @param  unknown  $new
     * @param  unknown  $given_array
     */
    private function retraverseNew(&$given, $new, $given_array)
    {
        foreach ($new as $key => $entry) {
            if (! isset($given->$key) && (isset($given_array->$key))) {
                $given->$key = new \stdClass;
                if ($this->isTraversable($new->$key)) {
                    $this->retraverseNew($given->$key, $new->$key, $given_array->$key);
                }
            }
        }
    }

    /**
     * When both given structures are "traversable" performs the diff
     *
     * @return \stdClass
     */
    private function getTraversableDiff(\stdClass $given, \stdClass $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        $result = new \stdClass;
        $result->given = $this->traverseGiven($given, $new, $accept_given_asterik, $accept_new_asterik);
        $result->new = $this->traverseNew($given, $new, $accept_given_asterik, $accept_new_asterik);
        $this->retraverseGiven($result->given, $result->new, $new);
        $this->retraverseNew($result->given, $result->new, $given);

        return $result;
    }

    /**
     * Returns true when an diff could be performed on the given variable
     *
     * @param  unknown  $test
     */
    private function isTraversable($test): bool
    {
        return is_array($test) || is_a($test, \Traversable::class) || is_a($test, \stdClass::class);
    }

    /**
     * The main function of the DiffCreator
     *
     * @param  unknown  $given
     * @param  unknown  $new
     * @return \stdClass
     */
    public function getDiff($given, $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        if ($this->isTraversable($given) && ($this->isTraversable($new))) {
            return $this->getTraversableDiff($given, $new, $accept_given_asterik, $accept_new_asterik);
        }
        throw new \Exception("Can't get a diff out of the input data");
    }
}
