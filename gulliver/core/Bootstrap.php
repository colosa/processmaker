<?php
/**
 * Class Bootstrap
 *
 * This class tries encapsulate some common tasks on SysGeneric bootstrap file
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com>
 */
class Bootstrap
{
    public $startingTime;
    public $autoloader;
    public $routes = array();

    protected $matchRoute = array();

    public function __construct($config)
    {
        $this->startingTime = microtime(true);

        // Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
        define('PATH_SEP', DIRECTORY_SEPARATOR);

        $config['path_trunk'] = rtrim($config['path_trunk'], PATH_SEP) . PATH_SEP;

        if (! array_key_exists('path_trunk', $config)) {
            throw new Exception("path_trunk config not defined!");
        }

        if (! is_dir($config['path_trunk'] . 'gulliver')) {
            throw new Exception(sprintf(
                "Gulliver Framework not found in path trunk: '%s'", $config['path_trunk']
            ));
        }

        define('PATH_TRUNK',    $config['path_trunk']);

        // Including these files we get the PM paths and definitions (that should be just one file.


        $this->autoloader = Autoloader::getInstance();
    }

    public function addRoute($name, $pattern, $basePath, $type = '', $skip = false)
    {
        $this->routes[$name] = array(
            'pattern'  => $pattern,
            'basePath' => $basePath,
            'type'     => $type,
            'skip'     => $skip
        );
    }

    public function route($uri)
    {
        foreach ($this->routes as $name => $route) {
            //$urlPattern = addcslashes( $urlPattern , '/'); ???
            $route['pattern'] = addcslashes( $route['pattern'] , './');
            $route['pattern'] = '/^' . str_replace(array('*','?'), array('.*','.?'), $route['pattern']) . '$/';

            // remove url GET params '..?var=val&....'
            list($uri, ) = explode('?', $uri);

            if (preg_match($route['pattern'], $uri, $match)) {
                $this->matchRoute = $route;
                $this->matchRoute['name']  = $name;
                $this->matchRoute['match'] = $match[1];
                $this->matchRoute['path']  = $this->matchRoute['basePath'] . $match[1];

                return $route['skip'] ? false : true;
            }
        }

        return false;
    }

    public function getMatchRoute()
    {
        return $this->matchRoute;
    }

    public function dispatchResource()
    {
    }

    public function configure()
    {
    }

    protected function registerClasses()
    {
        $this->autoloader->registerClass('G', PATH_TRUNK . 'gulliver/system/class.g');
    }
}
