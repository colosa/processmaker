<?php
/**
 * Class Autoloader
 *
 * Adaptation of Alchemy/Component/ClassLoader on https://github.com/eriknyk/ClassLoader for php 5.2.x
 *
 * ""SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names. ""
 *
 * Singleton Class
 *
 * @version   1.0
 * @author    Erik Amaru Ortiz <aortiz.erik@gmail.com>
 * @link      https://github.com/eriknyk/phpalchemy
 * @copyright Copyright 2012 Erik Amaru Ortiz
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Autoloader
{
    /**
     * Holds singleton object
     *
     * @var ClassLoader
     */
    protected static $instance = null;
    protected $includePaths = array();
    protected $includeClassPaths = array();

    protected $relativeIncludePaths = array();

    /**
     * Creates a new SplClassLoader and installs the class on the SPL autoload stack
     *
     * @param string $ns The namespace to use.
     */
    public function __construct()
    {
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);

        spl_autoload_register(array($this, 'loadClass'));
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string $includePath
     */
    public function getIncludePaths()
    {
        return $this->includePaths;
    }

    /**
     * register a determinated namespace and its include path to find it.
     *
     * @param string $namespace namespace of a class given
     * @param string $includePath path where the class exists
     * @param string $excludeNsPart contais a part of class to exclude from classname passed by SPL hanlder
     */
    public function register($namespace, $includePath)
    {
        $this->includePaths[$namespace] = rtrim($includePath, DS) . DS;
    }

    public function registerClass($classname, $includeFile)
    {
        $includeFile = strpos($includeFile, '.php') !== false ? $includeFile : $includeFile . '.php';
        $classname = strtolower($classname);

        if (! file_exists($includeFile)) {
            throw new Exception("Error, Autoloader can't register a inexisten file: " . $includeFile);
        }

        if (strpos($classname, '*') !== false) {
            $tmp = str_replace('*', '(.+)', $classname);
            $this->relativeIncludePaths[$tmp] = $includeFile;
            return;
        }

        if (array_key_exists($classname, $this->includeClassPaths)) {
            throw new Exception("Error, class '%s' is already registered in Autoloader.");
        }

        $this->includeClassPaths[$classname] = strpos($includeFile, '.php') !== false ? $includeFile : $includeFile . '.php';
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return void
     */
    protected function loadClass($class)
    {
        $className = strtolower($class);

        if (array_key_exists($className, $this->includeClassPaths)) {
            require_once $this->includeClassPaths[$className];
            return true;
        } else {
            foreach ($this->relativeIncludePaths as $relClassname => $includeFile) {
                if ($r = preg_match('/'.$relClassname.'/', $className, $match)) {
                    require_once $includeFile;
                    return true;
                }
            }
        }

        $filename = str_replace('_', DS, $class) . '.php';

        foreach ($this->includePaths as $namespace => $includePath) {
            //var_dump($includePath . $filename);
            if (file_exists($includePath . $filename)) {
                require_once $includePath . $filename;

                return true;
            }
        }

        return false;
    }
}

