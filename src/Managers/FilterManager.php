<?php

namespace Sunhill\Managers;

use Sunhill\Filter\FilterContainer;
use Sunhill\Filter\FilterException;

/**
 * The FilterManager manages the filters. A filter is an operation on a filter container that
 * is executed of certain filter conditions match. The result of the execution must be 
 * one of:
 * CONTINUE = The execution was successful and the other filters should be executed
 * SUFFICIENT = The execution was successful and the whole execution chain should be considered as successful
 * STOP = The execution should be stopped at this point, no other filters should be executed
 * FAILURE = The execution was not successful the remaining filters should not be executed
 * 
 * @author lokal
 *
 */
class FilterManager
{
    
    protected $filters = [];
    
    /**
     * Clears the filters
     */
    public function clearFilters()
    {
        $this->filters = [];    
    }
    
    public function addFilters($filters)
    {
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                $this->addFilters($filter);
            }
            return $this;
        }
        if (is_string($filters) && class_exists($filters)) {
            $filters = new $filters();
        }
        if (is_object($filters)) {
            $this->filters[] = $filters;
            return;
        }
        throw new FilterException("Can't handle given filter");
    }
    
    protected function getGroupedFilters(string $group): array
    {
        return array_filter($this->filters, function($item) use ($group)
        {          
            $det_group = $item->getGroup();           
            return $item->getGroup() == $group; 
        });
    }
    
    protected function orderByPriority(array $filters): array
    {
        usort($filters, function($item1, $item2) 
        {
           $item1 = $item1->getPriority();
           $item2 = $item2->getPriority();
           if ($item1 == $item2) {
               return 0;
           }
           return ($item1 < $item2) ? -1 : 1;           
        });
        return $filters;
    }
    
    public function getFiltersByGroup(string $group): array
    {
        return $this->orderByPriority($this->getGroupedFilters($group));
    }
    
    public function executeFilters(array $filters, FilterContainer $container): string
    {
        $result = 'INSUFFICIENT';
        foreach ($filters as $filter) {
            $filter->setContainer($container);
            if (!$filter->matches($container)) {
                continue;
            }
            switch ($filterresult = $filter->execute($container)) {
                case 'STOP':
                    return $result;
                case 'FAILURE':
                    return 'FAILURE';
                case 'SUFFICIENT':
                    $result = 'SUCCESS';
                    break;
                case 'SUFFICIENTSTOP':
                    return 'SUCCESS';
                case 'CONTINUE':
                    break;
                default:
                    throw new FilterException("Unknown filter result '$filterresult'");
            }
        }
        return $result;
    }
    
    public function execute(string $group, FilterContainer $container): string
    {
        return $this->executeFilters($this->getFiltersByGroup($group), $container);
    }
    
}