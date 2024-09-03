<?php

namespace Sunhill\Plugins;

use Sunhill\Query\ArrayQuery;

class PluginQuery extends ArrayQuery
{
    protected $plugins = [];
    
    protected $allowed_order_keys = ['none','name','author','version','state'];
    
    public function __construct(array $plugins)
    {
        parent::__construct();
        $this->plugins = $plugins;
    }
    
    protected function getRawData()
    {
        return $this->plugins;
    }
    
    public function getKey($entry, $key)
    {
        switch ($key) {
            case 'name':
                return $entry->getName();
            case 'author':
                return $entry->getAuthor();
            case 'version':
                return $entry->getVersion();
            case 'state':
                return $entry->getState();
        }
    }
    
    public function propertyExists($entry, $key)
    {
        return in_array($key, ['name','author','version','state']);
    }
    
    
}