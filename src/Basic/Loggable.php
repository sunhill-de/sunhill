<?php
/**
 * @file Loggabe.php
 * Provides a class that provides an abstraction of logging methods
 * Lang en
 * Reviewstatus: 2023-03-14
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Basic/LoggableTest.php
 * Coverage: 27.45% (2023-03-14)
 */

namespace Sunhill\Basic;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

/**
 * Consts for the different log levels
 * @var unknown
 */
define('LL_DEBUG',-1);
define('LL_INFO',-2);
define('LL_NOTICE',-3);
define('LL_WARNING',-4);
define('LL_ERROR',-5);
define('LL_CRTITICAL',-6);
define('LL_ALERT',-7);
define('LL_EMERGENCY',-8);

/**
 * Baseclass for all classes that can send messages to the framework log
 * This class does a filtering via a variable called loglevel which tells the class up to what urgency the 
 * messages should be ignored.
 * The different urgencies are standarized:
 * - debug()
 * - info()
 * - notice()
 * - warning()
 * - error()
 * - critical()
 * - alert()
 * - emergency()
 * 
 * These messages receive a string of the log message
 * @author klaus
 */
class Loggable extends Base {
	/**
	 * Saves the command object for displaying things on the screen 
	 */
    private $command;
    
    /**
     * Saves the current log level
     * @var int
     */
    private $loglevel=LL_ERROR;
    
    /**
     * Saves the current display level
     * @var int
     */
    private $displaylevel = LL_ERROR;
    
    /**
     * Setter for the Loglevel
     * @param int $loglevel
     * @return Loggable
     */
    public function setLoglevel(int $loglevel): Loggable 
    {
        $this->loglevel = $loglevel;
        return $this;
    }
    
    /**
     * Getter for the Loglevel
     * @return int
     */
    public function getLoglevel(): int 
    {
        return $this->loglevel;
    }
    
    public function setCommand(Command $command): Loggable 
    {
        $this->command = $command;
        return $this;
    }
    
    /**
     * Setter for the display level
     * @param int $displaylevel
     * @return Loggable
     */
    public function setDisplaylevel(int $displaylevel): Loggable 
    {
        $this->displaylevel = $displaylevel;
        return $this;
    }
    
    /**
     * Getter for the Displaylevel
     * @return int
     */
    public function getDisplaylevel(): int 
    {
        return $this->displaylevel;
    }
    
    private function processMessage(int $level, string $message): void 
    {
        if ($this->checkLoglevel($level)) {
            switch ($level) {
                case LL_DEBUG:
                    Log::debug($message); break;
                case LL_INFO:
                    Log::info($message); break;
                case LL_NOTICE:
                    Log::notice($message); break;
                case LL_WARNING:
                    Log::warning($message); break;
                case LL_ERROR:
                    Log::error($message); break;
                case LL_CRTITICAL:
                    Log::critical($message); break;
                case LL_ALERT:
                    Log::alert($message); break;
                case LL_EMERGENCY:
                    Log::emergency($message); break;
            }
        }
        if ($this->checkDisplaylevel($level)) {
            if (!is_null($this->command)) {
                if ($level > LL_WARNING) {
                    $this->command->info($message);
                } else {
                    $this->command->error($message);
                }
            } else {
                echo $message;
            }
        }             
    }
    
    /**
     * Enters a debug message into the log if the loglevel is on LL_DEBUG
     * @param string $message
     */
    protected function debug(string $message): void 
    {
        $this->processMessage(LL_DEBUG,$message);
    }
    
    /**
     * Enters a info message into the log if the loglevel is on LL_INFO or lower
     * @param string $message
     */
    protected function info(string $message): void
    {
        $this->processMessage(LL_INFO,$message);
    }
    
    /**
     * Enters a notice message into the log if the loglevel is on LL_NOTICE or lower
     * @param string $message
     */
    protected function notice(string $message): void
    {
        $this->processMessage(LL_NOTICE,$message);
    }
    
    /**
     * Enters a warning message into the log if the loglevel is on LL_WARNING or lower
     * @param string $message
     */
    protected function warning(string $message): void 
    {
        $this->processMessage(LL_WARNING,$message);
    }
    
    /**
     * Enters an error message into the log if the loglevel is on LL_ERROR or lower
     * @param string $message
     */
    protected function error(string $message): void 
    {
        $this->processMessage(LL_ERROR,$message);
    }
    
    /**
     * Enters a critical message into the log if the loglevel is on LL_CRITICAL or lower
     * @param string $message
     */
    protected function critical(string $message): void 
    {
        $this->processMessage(LL_CRITICAL,$message);
    }
    
    /**
     * Enters a alert message into the log if the loglevel is on LL_ALERT or lower
     * @param string $message
     */
    protected function alert(string $message): void 
    {
        $this->processMessage(LL_ALERT,$message);
    }
    
    /**
     * Enters an emergency message into the log if the loglevel is on LL_EMERGENCY or lower
     * @param string $message
     */
    protected function emergency(string $message): void 
    {
        $this->processMessage(LL_EMERGENCY,$message);
    }
    
    /**
     * Checks if the requested loglevel is higher than the currently set. If it returns true, the message
     * is passed to the log system. 
     * @param int $requested
     * @return boolean
     */
    private function checkLoglevel(int $requested): bool 
    {
        if ($requested >= $this->loglevel) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Checks if the requested displaylevel is higher than the currently set. If it returns true, the message
     * is passed to the display
     * @param int $requested
     * @return boolean
     */
    private function checkDisplaylevel(int $requested): bool 
    {
        if ($requested >= $this->displaylevel) {
            return true;
        } else {
            return false;
        }
    }
}
