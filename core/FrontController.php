<?php

/**
 * Description of FrontController
 *
 * @author asphyxia
 * @copyright copyleft 2012
 */
//require 'FrontControllerHelpers.php';
namespace Core;
final class FrontController {

    /**
     * $_controller and $_action will hold our controller class (it's name)
     * and our given controller's action (a method). So, this is like saying
     * Call the method Print of the class HTML, but in the form: HTML/Print
     * 
     * @protected string $_controller the controller name (class name)
     * @protected string $_action the controller's method to call
     * @protected array $_params bidimentional non-assoc array of [keys, values]
     * @protected string $_body output
     * @static object $instance out singleton class instance
     */
    protected static $_instance = null;
    private $router = null;
    private $response = null;
    private $config   = null;

    private function __construct($request, $config) {
        $this->router = new Router($request);
        $this->config = Config\Configuration::getInstance($config['file'], $config['encoder']);
    }
    /**
     * Simple singleton method.
     *
     * @return object the class instance
     */
    public function getInstance(Array $configuration = null, $request = null) {
        if (empty(self::$_instance)) {
            //$_SERVER['REQUEST_URI']
            $config = array('file' => null, 'encoder' => null);
            if (is_array($configuration)) {
                $request = (isset($configuration['request'])) ? $configuration['request'] : $request;
                $config = (isset($configuration['config'])) ? $configuration['config'] : $config;
            }
            self::$_instance = new FrontController($request, $config);
        }
        return self::$_instance;
    }

    /**
     * Call our controller and action given and parsed by our __constructor() method.
     *
     * @return void
     */
    public function routeController() {
        return $this->setResponse($this->router->routeController());
        
    }
    
    public function getRender() {
        if (null !== $render = $this->config->getConfig(array('system', 'render'))) {
            if (!class_exists($render['name'])) {
                require dirname(__FILE__) . $render['path'];
            }
            $render = new $render['name']($this->config->getConfig('render'));
            $render->setActivePage($this->router->getAction(), $this->router->getController());
            return $render;
        } else {
            throw new \Exception('Render not found');
        }
    }

    public function getResponse() {
        return $this->response;
    }
    private function setResponse($response) {
        return $this->response = $response;
    }
}