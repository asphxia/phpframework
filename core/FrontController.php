<?php

/**
 * Description of FrontController
 *
 * @author asphyxia
 * @copyright copyleft 2012
 */
namespace Core;
use Core\Utils\Logger as Logger;
use Core\Utils\Singleton as Singleton;
use Core\Configuration\Configuration as Configuration;
use Core\Configuration\XmlEncoder as XmlEncoder;
use Core\Router\Router as Router;
use Core\Cache\Cache as Cache;

/**
 * 
 */
final class FrontController extends Singleton {

    /**
     * Singleton instance variable.
     * 
     * @var type Object|null
     */
    protected static $_instance;
    
    /**
     *  Current Router instance.
     * 
     * @var type Object|null
     */
    private $routerEngine = null;
    
    /**
     * Router's call result.
     * 
     * @var type mixed|null
     */
    private $response = null;
    
    /**
     * Current Configuration instance.
     * 
     * @var type Object|null
     */
    private $configEngine = null;
    
    /**
     * Current Cache instance.
     * 
     * @var type Object|null
     */
    private $cacheEngine = null;
    
    /**
     * Configuration files to look at
     * 
     * @var type Array
     */
    public static $CONFIGURATION_FILES = array('user', 'app', 'default');
    
    /**
     * Directory to search for configuration files
     * 
     * @var type Array
     */
    public static $INCLUDE_PATHS = array('', 'config/');
    
    /**
     * Sets the Router to handle routing and dispatching.
     * 
     * @param Router $router 
     */
    public function setRouterEngine(Router $router = null) {
        $this->routerEngine = is_null($router) ? $this->getRouter() : $router;
    }

    /**
     * Gets the current routerEngine or instantiates one
     * 
     * @return type Object
     */
    public function getRouterEngine() {
        if (isset($this->routerEngine)) {
            return $this->routerEngine;
        }
        $config = $this->getConfigurationEngine();

        $defaults = $config->getConfiguration('defaults');
        $system = $config->getConfiguration('system');
        $rewrites = $config->getConfiguration('rewrites');
        if (!is_array($rewrites) || empty($rewrites)) {
            $rewrites['key'] = array();
        }

        $this->routerEngine = new Router(array(
            'namespace' => $defaults['namespace'],
            'controller'=> $defaults['controller'],
            'action'    => $defaults['action'],
            'request'   => $_SERVER['REQUEST_URI'],
            'rewrites'  => $rewrites['key'],
        ));

        $this->routerEngine->setIncludePath($this->preprocessPath(__DIR__ . '/' . $system['includePath']));

        return $this->routerEngine;
    }
    
    /**
     * Sets the configuration Engine
     * 
     * @param Configuration $configuration 
     */
    public function setConfigurationEngine(Configuration $configuration = null) {
        $this->configEngine = is_null($configuration) ? $this->getConfiguration() : $configuration;
    }

    /**
     * Gets the current configuration Engine or instantiates one
     * 
     * @return type Object
     */
    public function getConfigurationEngine() {
        if (isset($this->configEngine)) {
            return $this->configEngine;
        }
        $this->configEngine = new Configuration();

        // Preprocess and concatenates the arrays CONFIGURATION_FILES and INCLUDE_PATHS
        $configurationFiles = array();
        if (isset($_SERVER['SERVER_NAME']) && null !== $serverName = $_SERVER['SERVER_NAME']) {
            array_unshift(self::$CONFIGURATION_FILES, $serverName);
        }

        foreach (self::$INCLUDE_PATHS as $path) {
            foreach (self::$CONFIGURATION_FILES as $fileName) {   
                $configurationFiles[] = $path . $fileName;
            }
        }

        // TODO Get encoder from environment (setenv)
        // Create a config bootstrap? a PHP that handles the basic configs of the system
        $configFile = false;
        foreach ($configurationFiles as $config) {
            $path = __DIR__ . '/../' . $config . '.xml';
            if (false != $config = realpath($path)) {
                $configFile  = $config;
                break;
            }
        }
        if (!$configFile) {
            throw new Exception('No configuration files found.');
        }

        $this->configEngine->setDataSource($configFile);

        // TODO: Make this configurable!!
        $this->configEngine->setEncoder(new XmlEncoder());

        return $this->configEngine;
    }

    /**
     * Gets the current render Engine or instantiates one
     * 
     * @return \Core\render
     * @throws Exception 
     */
    public function getRenderEngine() {
        if (isset($this->renderEngine)) {
            return $this->renderEngine;
        }
        
        $router = $this->getRouterEngine();
        $config = $this->getConfigurationEngine();

        $render = $config->getConfiguration(array('system' => 'render'));
        if (null !== $render) {
            
            require_once __DIR__ . '/' . $render['path'];
            
            $configuration = $this->preprocessPaths($config->getConfiguration(array('render')));
            $this->renderEngine = new $render['name'](array('config' => $configuration));
            $this->renderEngine->setActivePage($router->getAction(), $router->getController());
            
            return $this->renderEngine;
            
        } else {
            throw new Exception('Render not found: `' . $render . '`');
        }
        
        
    }

    /**
     *
     * @return type 
     */
    public function getCacheEngine() {
        if (isset($this->cacheEngine)) {
            return $this->renderEngine;
        }

        $config = $this->getConfigurationEngine();
        $cacheConfig = $this->preprocessPaths($config->getConfiguration('cache'));
        $this->cacheEngine = new Cache( $cacheConfig );

        return $this->cacheEngine;
    }
    
    /**
     *
     * @param type $cacheEngine 
     */
    public function setCacheEngine($cacheEngine = null) {
        $this->configEngine = is_null($cacheEngine) ? $this->getCacheEngine() : $cacheEngine;
    }
    
    /**
     *
     * @return type 
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     *
     * @param type $response
     * @return type 
     */
    private function setResponse($response) {
        return $this->response = $response;
    }

    /**
     * Call our controller and action given and parsed by our __constructor() method.
     *
     * @return void
     */
    public function routeController() {
        if (false !== $res = $this->getRouterEngine()->routeController()) {
            $this->setResponse($res);
            return true;
        }else{
            // ass Router->getResponseCode() or similar to handle this
            return $res;
        }
    }
    
    /**
     * Replaces configuration pseudo variables $namespace, $controller, $action with actual values.
     * 
     * @param type Array
     * @return type Array
     */
    private function preprocessPaths($configuration) {
        foreach ($configuration as $key => $val) {
            $val = $this->preprocessPath($val);
            $configuration[$key] = $val;
        }
        return $configuration;
    }
    
    /**
     * 
     * @param type $path
     * @return type
     */
    private function preprocessPath($path){
        foreach (array('$namespace','$controller', '$action') as $variable) {
            $x = str_replace('$', '', $variable);
            $value = $this->getRouterEngine()->getConfiguration(array($x));
            $path = str_replace($variable, $value[$x], $path);
        }
        return $path;
    }
    
    /**
     * 
     * @return type
     */
    public function getBasePath() {
        return $this->getRouterEngine()->getRequest();
    }
    
    /**
     * 
     * @param type $to
     */
    public function redirect($to) {
        if (!headers_sent()) {
            header("Location: " . $to);
        }else{
            echo '<meta http-equiv="refresh" content="1;url=' . $to . '">';
        }
    }
}