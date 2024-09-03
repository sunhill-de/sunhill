<?php
/**
 * @file PluginInstaller.php
 * A basic class for plugin installers
 * Lang en
 * Reviewstatus: 2024-04-22
 * Localization:
 * Documentation:
 * Tests:
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Plugins;

use Sunhill\Framework\Plugins\Exceptions\FileNotFoundException;
use Sunhill\Framework\Plugins\Exceptions\FileAlreadyExistsException;
use Illuminate\Support\Facades\Schema;

abstract class PluginInstaller
{
     
    protected $plugin = '';
    
    /**
     * Getter for $plugin
     * 
     * @return string
     */
    public function getPlugin(): string
    {
        return $this->plugin;
    }
    
    protected $version = '';
    
    /**
     * Getter for $version
     * 
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    protected $owner;
    
    /**
     * Setter for owner
     * 
     * @param unknown $owner
     * @return self
     */
    public function setOwner($owner): self
    {
        $this->owner = $owner;
        return $this;
    }
    
    protected $storage_dir = '';
    
    public function getStorageDir(): string
    {
        if (empty($this->storage_dir)) {
            $this->storage_dir = storage_path('plugins/'.$this->owner->getName());
        }
        return $this->storage_dir;
    }
    
    public function setStorageDir(string $dir): self
    {
        $this->storage_dir = $dir;
        return $this;
    }
    /**
     * Does the installation/upgrade process
     */
    abstract public function execute();
    
    protected function checkRootDir()
    {
        if (!file_exists($this->getStorageDir())) {
            mkdir($this->getStorageDir());
        }
    }
    
    protected function checkFile(string $file, string $what = 'file')
    {
        if (!file_exists($this->getStorageDir().'/'.$file)) {
            throw new FileNotFoundException("The $what '$file' was not found.");
        }
    }
    
    protected function checkFileNonexistance(string $file, string $what = 'file')
    {
        if (file_exists($this->getStorageDir().'/'.$file)) {
            throw new FileAlreadyExistsException("The $what '$file' already exists.");
        }
    }
    
    protected function createDir(string $name)
    {
        $this->checkRootDir();
        $this->checkFileNonexistance($name, 'directory');
        mkdir($this->getStorageDir().'/'.$name);
    }
    
    protected function renameDir(string $from, string $to)
    {
        $this->checkFile($from,'directory');
        $this->checkFileNonexistance($to, 'directory');
        rename($this->getStorageDir().'/'.$from,$this->getStorageDir().'/'.$to);
    }
    
    protected function deleteDir(string $name)
    {
        $this->checkFile($name,'directory');
        rmdir($this->getStorageDir().'/'.$name);        
    }
    
    protected function createFile(string $filename, string $content)
    {
        $this->checkRootDir();
        $this->checkFileNonexistance($filename);
        file_put_contents($this->getStorageDir().'/'.$filename, $content);
    }
    
    protected function renameFile(string $from, string $to)
    {
        $this->checkFile($from);
        $this->checkFileNonexistance($to);
        rename($this->getStorageDir().'/'.$from,$this->getStorageDir().'/'.$to);
    }
    
    protected function deleteFile(string $name)
    {
        $this->checkFile($name);
        unlink($this->getStorageDir().'/'.$name);
    }
    
    protected function createTable(string $name, callable $callback)
    {
        Schema::create($name, $callback);
    }
    
    protected function modifyTable(string $name, callable $callback)
    {
        Schema::table($name, $callback);
    }
    
    protected function deleteTable(string $name)
    {
        Schema::drop($name);
    }
}