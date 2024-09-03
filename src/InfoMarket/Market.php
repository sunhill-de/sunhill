<?php
/**
 * @file Market.php
 * Provides the Market class as as the basic container for marketeers and properties.
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-14
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/InfoMarket/Marketeer.php
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket;

use Sunhill\Properties\InfoMarket\Exceptions\CantProcessMarketeerException;
use Sunhill\Properties\InfoMarket\Exceptions\MarketeerHasNoNameException;
use Sunhill\Properties\Properties\AbstractProperty;
use Sunhill\Properties\InfoMarket\Exceptions\PathNotFoundException;
use Sunhill\Properties\InfoMarket\Exceptions\UnknownFormatException;

class Market 
{
 
    protected $marketeers = [];

    /** 
     * Tries to translate the given marketeer into a object of a marketeer
     * 
     * @param unknown $marketeer
     * @return unknown
     */
    protected function handleMarketeer($marketeer)
    {
        if (is_a($marketeer, Marketeer::class)) {
            return $marketeer;
        }
        if (is_object($marketeer)) {
            throw new CantProcessMarketeerException("The given marketeer is not a marketeer class.");            
        }
        if (class_exists($marketeer)) {
            return new $marketeer();
        }
        if (is_scalar($marketeer)) {
            throw new CantProcessMarketeerException("The marketeer '$marketeer' can't be processed to a marketeer.");
        } else {
            throw new CantProcessMarketeerException("The given marketeer can't be processed to a marketeer.");            
        }
    }
    
    /**
     * Registers a new marketeer in this market
     * 
     * @param unknown $marketeer
     * @param string $name
     */
    public function registerMarketeer($marketeer, string $name = '')
    {
        $marketeer = $this->handleMarketeer($marketeer);
        if (empty($name)) {
            $name = $marketeer->getName();
        }
        if (empty($name)) {
            throw new MarketeerHasNoNameException("The given marketeer has no default name and no name was given.");
        }
        
        $this->marketeers[$name] = $marketeer;
    }
    
    /**
     * Returns if the market provides the given marketeer
     * 
     * @param string $marketeer
     * @return bool
     */
    public function hasMarketeer(string $marketeer): bool
    {
        return isset($this->marketeers[$marketeer]);
    }
    
    protected function searchProperty(string $path): ?AbstractProperty
    {
        $parts = explode('.', $path);
        $first = array_shift($parts);
        
        if (!$this->hasMarketeer($first)) {
            return null;
        }
        
        return $this->marketeers[$first]->requestItem($parts);    
    }
    
    protected function getProperty(string $path): AbstractProperty
    {
        if (empty($property = $this->searchProperty($path))) {
            throw new PathNotFoundException("The path '$path' was not found.");
        }
        return $property;
    }
    
    /**
     * Returns if the given path exists. 
     * 
     * @param string $path
     * @return bool
     * 
     * @test Unit/InfoMarket/MarketTest::testPathExists()
     */
    public function pathExists(string $path): bool
    {
        return !empty($this->searchProperty($path));
    }
    
    protected function processFormat($input, string $format)
    {
        switch ($format) {
            case 'raw':
                return $input;
            case 'json':
                return json_encode($input);
            case 'stdclass':
                return json_decode(json_encode($input), false);
            case 'array':
                return json_decode(json_encode($input), true);
            default:
                throw new UnknownFormatException("The format '$format' is not known.");
        }        
    }
    
    /**
     * Returns just the value of the given element or raises an exception if none exists
     * @param string $path
     */
    public function requestValue(string $path, string $format = 'raw')
    {
        $value = $this->getProperty($path)->getValue();
        
        return $this->processFormat($value, $format);
    }
    
    /**
     * Takes a array of strings. Each element is a single path that is requested. The method
     * returns an associative array in the vform $path => $vLUE Or raises an exception if at
     * least one path doesn't exist. 
     * 
     * @param array $paths
     * @return array
     */
    public function requestValues(array $paths, string $format = 'raw')
    {
        $result = [];
        foreach ($paths as $path) {
            $result[$path] = $this->requestValue($path, 'raw');
        }
    
        return $this->processFormat($result, $format);        
    }
    
    /**
     * Returns the metadata of the given element or raises an excpetion if the element
     * does not exist.
     * 
     * @param string $path
     * @param string $format The desired return format:$this
     * - stdclass = The metadata should be returned as a \stdClass
     * - array = The metadata should be returned as an associative array
     * - json = The metadata should be returned as a json string
     * @return array
     */
    public function requestMetadata(string $path, string $format = 'stdclass')
    {
        $value = $this->getProperty($path)->getMetadata();
        
        return $this->processFormat($value, $format);
    }
    
    /**
     * Takes an array of strings where each element represents a path which metadata
     * should be regturnes. It raises an exception if at least one element does not 
     * exist.
     * 
     * @param array $paths
     * @param string $format The desired return format:$this
     * - stdclass = The metadata should be returned as a array of StdClass
     * - array = The metadata should be returned as a array of associative arrays
     * - arrayjson = The metadata should be returned as a array of json strings
     * - json = The metadata should be returned as a json string
     * @return array
     */
    public function requestMetadatas(array $paths, string $format = 'stdclass')
    {
        $result = [];
        foreach ($paths as $path) {
            $result[$path] = $this->requestMetadata($path);
        }
        
        return $this->processFormat($result, $format);        
    }
    
    /**
     * Returns the data of the given element or raises an excpetion if the element
     * does not exist. Data is metadata and value combined
     *
     * @param string $path
     * @param string $format The desired return format:$this
     * - stdclass = The metadata should be returned as a \stdClass
     * - array = The metadata should be returned as an associative array
     * - json = The metadata should be returned as a json string
     * @return array
     */
    public function requestData(string $path, string $format = 'stdclass')
    {
        $property = $this->getProperty($path);
        
        $value = $property->getMetadata();
        $value['value'] = $property->getValue();
        $value['human_value'] = $property->getHumanValue();
        
        return $this->processFormat($value, $format);
    }
    
    /**
     * Takes an array of strings where each element represents a path which data
     * should be returned. Data is metadata and value combined. 
     * It raises an exception if at least one element does not
     * exist.
     *
     * @param array $paths
     * @param string $format The desired return format:$this
     * - stdclass = The metadata should be returned as a array of StdClass
     * - array = The metadata should be returned as a array of associative arrays
     * - arrayjson = The metadata should be returned as a array of json strings
     * - json = The metadata should be returned as a json string
     * @return array
     */
    public function requestDatas(array $paths, string $format = 'stdclass')
    {
        $result = [];
        foreach ($paths as $path) {
            $result[$path] = $this->requestData($path);
        }
        
        return $this->processFormat($result, $format);        
    }
    
    /**
     * Tries to write the value $value to the path $path. It raises an exception if 
     * the path doesn't exist or is not writeable
     * 
     * @param string $path
     * @param unknown $value
     */
    public function putValue(string $path, $value)
    {
        $property = $this->getProperty($path);
        
        $property->setValue($value);
    }
    
    public function putValues(array $paths_and_values)
    {
        foreach ($paths_and_values as $path => $value) {
            $this->putValue($path, $value);
        }
    }
    
}