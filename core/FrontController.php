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
use Core\Configuration\IniEncoder as IniEncoder;
use Core\Router\Router as Router;
use Core\Render\Render as Render;
use Core\Cache\Cache as Cache;

/**
 * 
 */
final class FrontController extends Singleton {

    /**
     *
     * @var type 
     */
    protected static $_instance;
    
    /**
     *
     * @var type 
     */
    private $routerEngine = null;
    
    /**
     *
     * @var type 
     */
    private $response = null;
    
    /**
     *
     * @var type 
     */
    private $configEngine = null;
    
    /**
     *
     * @var type 
     */
    private $cacheEngine = null;
    
    /**
     *
     * @var type 
     */
    public static $CONFIGURATION_FILES = array('user', 'app', 'default');
    
    /**
     *
     * @var type 
     */
    public static $INCLUDE_PATHS = array('', 'config/');
    
    /**
     *
     * @param Router $router 
     */
    public function setRouterEngine(Router $router = null) {
        $this->routerEngine = is_null($router) ? $this->getRouter() : $router;
    }

    /**
     *
     * @return type 
     */
    public function getRouterEngine() {
        if (!$this->routerEngine) {
            $config = $this->getConfigurationEngine();
            $defaults = $config->getConfiguration('defaults');
            $system = $config->getConfiguration('system');
            $rewrites = $config->getConfiguration('rewrites');
            $this->routerEngine = new Router(array(
                'namespace' => $defaults['namespace'],
                'controller'=> $defaults['controller'],
                'action'    => $defaults['action'],
                'request'   => $_SERVER['REQUEST_URI'],
                'rewrites'  => $rewrites,
            ));
            
            $this->routerEngine->setIncludePath($this->preprocessPath(__DIR__ . '/' . $system['includePath']));

        }
        Logger::log($this->routerEngine);
        return $this->routerEngine;
    }
    
    /**
     *
     * @param Configuration $configuration 
     */
    public function setConfigurationEngine(Configuration $configuration = null) {
        $this->configEngine = is_null($configuration) ? $this->getConfiguration() : $configuration;
    }

    /**
     *
     * @return type 
     */
    public function getConfigurationEngine() {
        if (!$this->configEngine) {
            $this->configEngine = Configuration::getInstance();
            
            $configurationFiles = array();
            if (isset($_SERVER['SERVER_NAME']) && null !== $serverName = $_SERVER['SERVER_NAME']) {
                array_unshift(self::$CONFIGURATION_FILES, $serverName);
            }
            foreach (self::$INCLUDE_PATHS as $path) {
                foreach (self::$CONFIGURATION_FILES as $fileName) {   
                    $configurationFiles[] = $path . $fileName;
                }
            }
            foreach ($configurationFiles as $config) {
                $path = __DIR__ . '/../' . $config . '.ini';
                $config = realpath($path);
                Logger::log($path);
                if (file_exists($config)) {
                    $this->configEngine->setDataSource($config);
                    break;
                }
            }
            $this->configEngine->setEncoder(new IniEncoder());
        }
        return $this->configEngine;
    }

    /**
     *
     * @return \Core\render
     * @throws Exception 
     */
    public function getRenderEngine() {
        if (!$this->renderEngine) {
            $router = $this->getRouterEngine();
            $config = $this->getConfigurationEngine();

            if (null !== $render = $config->getConfiguration(array('system' => 'render'))) {
                if (!class_exists($render['name'])) {
                    require __DIR__ . '/' . $render['path'];
                }
                $configuration = $this->preprocessPaths($config->getConfiguration(array('render')));

                $render = new $render['name'](array('config' => $configuration));
                $render->setActivePage($router->getAction(), $router->getController());
                return $render;

            } else {
                throw new Exception('Render not found: `' . $render . '`');
            }
        }
        return $this->renderEngine;
    }

    /**
     *
     * @return type 
     */
    public function getCacheEngine() {
        if (!$this->cacheEngine) {
            $config = $this->getConfigurationEngine();
            $config = $this->preprocessPaths($config->getConfiguration('cache'));
            Logger::log($config);
            $this->cacheEngine = new Cache( $config );
        }
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
    private function preprocessPaths($configuration) {
        foreach ($configuration as $key => $val) {
            $val = $this->preprocessPath($val);
            $configuration[$key] = $val;
        }
        return $configuration;
    }
    private function preprocessPath($path){
        foreach (array('$namespace','$controller', '$action') as $variable) {
            $x = str_replace('$', '', $variable);
            $value = $this->getRouterEngine()->getConfiguration(array($x));
            $path = str_replace($variable, $value[$x], $path);
        }
        return $path;
    }
    public function getBasePath() {
        return $this->getRouterEngine()->getRequest();
    }
    
    public function redirect($to) {
        header("Location: " . $to);
    }
}