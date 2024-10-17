<?php
/**
 * @file base.php
 * Provides a common basic class for all sunhill project classes
 * Lang en
 * Reviewstatus: 2024-10-05
 * Localization: incomplete
 * Documentation: complete
 * 
 * Tests: Unit/Basic/BasicTest.php
 * Coverage: 100% (2024-10-17)
 */

namespace Sunhill\Basic;


use Sunhill\Exceptions\SunhillException;

/**
 * Basic common class for all classes of the sunhill project 
 * @author klaus
 *
 * @wiki /Utility_classes
 */
class Base 
{
	
    /**
     * Empty constructur so parent::__construct() always works
     */
    public function __construct() 
    {        
    }
    
    /**
     * Catchall for unknown variables. It tries to find a get_$varname method and calls it if found. Otherwiese it throws 
     * an excpetion. 
     * @param string $varname Name of the variable
     * @throws SunhillException is throws if no getter is found
     * @return any The value of the variable (return of the getter)
     */
    public function __get(string $varname) 
    {
		$method = "get".ucfirst($varname);
		if (method_exists($this,$method)) {
			return $this->$method();
		} else {
			throw new SunhillException("Variable '$varname' was not found.");
		}
	}
	
    /**
     * Set-Catchall for unknown variables. It tries to find a set_$varname method and calls it if found. 
     * @param string $varname Name of the variable
     * @param unknown $value Value of the variable
     * @throws SunhillException Is thrown if there is no setter
     * @return unknown
     */
	public function __set(string $varname, $value) 
	{
		$method = "set".ucfirst($varname);
		if (method_exists($this,$method)) {
			return $this->$method($value);
		} else {
		    throw new SunhillException("Variable '$varname' was not found.");
		}
	}
	
}