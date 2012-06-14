<?php

/**
 * Description of FrontController
 *
 * @author asphyxia
 * @copyright copyleft 2012
 */
//require 'FrontControllerHelpers.php';

namespace Core;

final class FrontController extends \Core\Utils\Singleton {

    /**
     * $_controller and $_action will hold our controller class (it's name)
     * and our given controller's action (a method). So, this is like saying
     * Call the method Print of the class HTML, but in the form: HTML/Print
     * 
     * @protected string $_controller the controller name (class name)
     * @protected string $_action the controller's method to call
     * @protected array $_params bidimentional non-assoc array of [keys, values]
     * @protected string $_body output
     */
    protected static $_instance;
    private $router = null;
    private $response = null;
    private $config = null;
    public function setRouter(Router $router = null) {
        $this->router = is_null($router) ? $this->getRouter() : $router;
    }

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

    public function setConfiguration(Configuration $configuration = null) {
        $this->config = is_null($configuration) ? $this->getConfiguration() : $configuration;
    }

    public function getConfiguration() {
        if (is_null($this->config)) {
            $this->config = Configuration\Configuration::getInstance();
            $this->config->setDataSource(dirname(__FILE__) . '/../config/default.ini');
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

    public function getResponse() {
        return $this->response;
    }

    private function setResponse($response) {
        return $this->response = $response;
    }

}