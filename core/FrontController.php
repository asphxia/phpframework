<?php

/**
 * Description of FrontController
 *
 * @author asphyxia
 * @copyright copyleft 2012
 */
namespace Core;

/**
 * 
 */
final class FrontController extends \Core\Utils\Singleton {

    /**
     *
     * @var type 
     */
    protected static $_instance;
    
    /**
     *
     * @var type 
     */
    private $router = null;
    
    /**
     *
     * @var type 
     */
    private $response = null;
    
    /**
     *
     * @var type 
     */
    private $config = null;
    
    /**
     *
     * @var type 
     */
    public static $CONFIGURATION_FILES = array('config/user', 'config/app', 'config/default',
                                                'user', 'app', 'default');
    /**
     *
     * @param Router $router 
     */
    public function setRouter(Router $router = null) {
        $this->router = is_null($router) ? $this->getRouter() : $router;
    }

    /**
     *
     * @return type 
     */
    public function getRouter() {
        if (is_null($this->router)) {
            $config = $this->getConfiguration();
            $defaults = $config->getConfiguration('defaults');
            $system = $config->getConfiguration('system');

            $this->router = new Router\Router(array(
                'namespace' => $defaults['namespace'],
                'controller'=> $defaults['controller'],
                'action'    => $defaults['action'],
            ));
            $this->router->processPath($_SERVER['REQUEST_URI']);
            $this->router->setIncludePath(dirname(__FILE__) . '/' . $system['includePath']);

        }
        return $this->router;
    }
    
    /**
     *
     * @param Configuration $configuration 
     */
    public function setConfiguration(Configuration $configuration = null) {
        $this->config = is_null($configuration) ? $this->getConfiguration() : $configuration;
    }

    /**
     *
     * @return type 
     */
    public function getConfiguration() {
        if (is_null($this->config)) {
            $this->config = Configuration\Configuration::getInstance();
            
            foreach (self::$CONFIGURATION_FILES as $config) {
                $config = dirname(__FILE__) . '/../' . $config . '.ini';
                if (file_exists($config)) {
                    $this->config->setDataSource($config);
                    break;
                }
            }
            $this->config->setEncoder(new Configuration\IniEncoder());
        }
        return $this->config;
    }

    /**
     * Call our controller and action given and parsed by our __constructor() method.
     *
     * @return void
     */
    public function routeController() {
        return $this->setResponse($this->getRouter()->routeController());
    }
    
    /**
     *
     * @return \Core\render
     * @throws Exception 
     */
    public function getRender() {
        $router = $this->getRouter();
        $config = $this->getConfiguration();

        if (null !== $render = $config->getConfiguration(array('system' => 'render'))) {
            if (!class_exists($render['name'])) {
                require dirname(__FILE__) . '/' . $render['path'];
            }
            $render = new $render['name']($config->getConfiguration('render'));
            $render->setActivePage($router->getAction(), $router->getController());
            return $render;
            
        } else {
            throw new Exception('Render not found: `' . $render . '`');
        }
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

}