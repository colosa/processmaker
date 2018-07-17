<?php

/**
 * class.monologProvider.php
 *
* @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

class MonologProvider
{
    private static $instance = null;
    public $aWorkspaces = null;
    public $formatter;
    public $streamRoutating;
    public $registerLogger;

    //the default format "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
    public $output = "<%level%> %datetime% %channel% %level_name%: %message% %context% %extra%\n";
    public $dateFormat = "M d H:i:s";
    public $numOfKeepFiles = 60;
    public $levelDebug = 100;
    public $bubble = true;
    public $filePermission = 0775;

    public function __construct ($channel, $fileLog)
    {
        //Set Formatter
        $this->formatter = new Monolog\Formatter\LineFormatter($this->output, $this->dateFormat);

        //Set Routating Handler
        $this->streamRoutating = new Monolog\Handler\RotatingFileHandler($fileLog, $this->numOfKeepFiles, $this->levelDebug, $this->bubble, $this->filePermission);
        $this->streamRoutating->setFormatter($this->formatter);

        //Create the channel and register the Logger with StreamRoutating
        $this->registerLogger = new Monolog\Logger($channel);
        $this->registerLogger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
        $this->registerLogger->pushHandler($this->streamRoutating);

    }

    /**
     * to get singleton instance
     *
     * @access public
     * @return object
     */
    public function getSingleton ($channel, $fileLog)
    {
        if (self::$instance == null) {
            self::$instance = new MonologProvider($channel, $fileLog);
        } else {
            self::$instance->setConfig($channel, $fileLog);
        }
        return self::$instance;
    }

    /**
     * Set channel and fileLog
     *
     * @access public
     * @return object
     */
    public function setConfig ($channel, $fileLog)
    {
        //Set Routating Handler
        $this->streamRoutating = new Monolog\Handler\RotatingFileHandler($fileLog, $this->numOfKeepFiles, $this->levelDebug, $this->bubble, $this->filePermission);
        $this->streamRoutating->setFormatter($this->formatter);

        //Create the channel and register the Logger with StreamRoutating
        $this->registerLogger = new Monolog\Logger($channel);
        $this->registerLogger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
        $this->registerLogger->pushHandler($this->streamRoutating);
    }

    /**
     * to register log
     *
     * @access public
     * @return void
     */
    public function addLog ($level, $message, $context)
    {
        switch ($level) {
            case 100://DEBUG
                $this->registerLogger->addDebug($message, $context);
                break;
            case 200://INFO
                $this->registerLogger->addInfo($message, $context);
                break;
            case 250://NOTICE
                $this->registerLogger->addNotice($message, $context);
                break;
            case 300://WARNING
                $this->registerLogger->addWarning($message, $context);
                break;
            case 400://ERROR
                $this->registerLogger->addError($message, $context);
                break;
            case 500://CRITICAL
                $this->registerLogger->addCritical($message, $context);
                break;
            case 550://ALERT
                $this->registerLogger->addAlert($message, $context);
                break;
            case 600://EMERGENCY
                $this->registerLogger->addEmergency($message, $context);
                break;
            default:
                $this->registerLogger->addDebug($message, $context);
        }
    }
}