<?php
/**
 * @file Module.php
 * A basic class for a sunhill module. A module bundles functions of the site. A module could
 * be all actions (CRUD) for a database entity.
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2025-03-20
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Modules/ModulesTest.php
 * Coverage: 
 */

namespace Sunhill\Modules;

use Sunhill\Basic\Base;
use Sunhill\Modules\Exceptions\InvalidModuleNameException;

/**
 * The basic class for a sunhill module. Modules have a parent->child relation with "site" being 
 * the root of the module tree. 
 * Modules have a name and a visible name. The difference between those is, that a name only may
 * contain ascii chars because it is used for the genration of urls. The visible name is a 
 * user friedly version of the name that can contain spaces and non-ascii chars
 * @author klaus
 *
 */
class Module extends Base
{
    
    /**
     * The current name of this module
     * @var string
     */
    protected string $name = '';
    
    /**
     * The visible name of this module
     * @var unknown
     */
    protected ?string $visible_name = null;
    
    /**
     * The parent module of this module
     * @var unknown
     */
    protected ?Module $parent = null;
    
    /**
     * Sets the name of the module. It checks if this name is valid
     * 
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        if (!preg_match("/^([a-zA-Z_])([a-zA-Z0-9])*$/", $name)) {
            throw new InvalidModuleNameException("The name '$name' is not allowed for a module name");
        }
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * Returns the name of the module
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Sets the visible user freidly name of this module
     * 
     * @param string $name
     * @return static
     */
    public function setVisibleName(string $name): static
    {
        $this->visible_name = $name;
        
        return $this;
    }
    
    /**
     * Returns the userfriedly name of the module
     * 
     * @return string
     */
    public function getVisibleName(): string
    {
        if (is_null($this->visible_name)) {
            return $this->name;
        } else {
            return $this->visible_name;
        }
    }
    
    /**
     * Sets the parent of the module
     * 
     * @param Module $parent
     * @return Module
     */
    public function setParent(Module $parent): Module
    {
        $this->parent = $parent;
        
        return $parent;
    }
    
    /**
     * Returns the parent of the module
     * 
     * @return Module|NULL
     */
    public function getParent(): ?Module
    {
        return $this->parent;
    }
    
    /**
     * Returns the object of all arent objects (wen includde_self is set even its own)
     * 
     * @param bool $include_self
     * @return array
     */
    public function getParents(bool $include_self = false): array
    {
        if ($this->parent) {
            $result = $this->parent->getParents(true);
        } else {
            $result = [];
        }
        if ($include_self) {
            $result[] = $this;   
        }
        
        return $result;
    }
    
    /**
     * Returns the name of the parent object incluing its own name. Dependig on separator
     * as an array or a string
     * 
     * @param string $separator
     * @return string|string|string[]
     */
    public function getParentNames(?string $separator = null)
    {
        if ($this->parent) {
            if ($separator) {
                return $this->parent->getParentNames($separator).$separator.$this->name;
            } else {
                $result = $this->parent->getParentNames($separator);
                $result[] = $this->name;
                return $result;
            }
        } else {
            if ($separator) {
                return $this->name;
            } else {
                $result = [$this->name];
                return $result;
            }
        }
    }
    
}