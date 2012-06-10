<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @copyright #legal.notice#
 * @version #Id#
 * @built #buildtime#
 */

/**
 * Description of Router
 *
 * @author asphyxia
 */
namespace Core;
final class Router {
    private $request;
    
    private $package;
    private $controller;
    private $action;
    private $params;
    
    private $routes;
    private $classes_path;
    
    static $DEFAULT_CONTROLLER = 'Index';
    static $DEFAULT_ACTION = 'index';
    static $DEFAULT_PACKAGE = 'Application';
    static $DEFAULT_CLASSES_PATH = '../application/controllers/';
    
    public function __construct($url = null, Array $routes = null) {
        $this->request = $url;
        $this->routes = $routes;
        if (isset($routes['package'])) {
            $this->setPackage($routes['package']);
        }
        if (isset($routes['classes_path'])) {
            $this->setClassesPath($routes['classes_path']);
        }

        $request = explode('/', $this->request);

        if (isset($request[1])) {
            $this->setController($request[1]);
        }
        
        if (isset($request[2])) {
            $this->setAction($request[2]);
        }

        if (isset($request[3])) {
            for ($i=0; $i<3; $i++) {
                array_shift($request);
            }
            $assoc = array();
            $rounded = \floor(\count($request)*2/2)-2;
            for ($i=0; $i<=$rounded; $i++) {
                $assoc[$request[$i]] = $request[++$i];
            }
            $this->setParams($assoc);
        }
    }
    public function routeController() {
        $this->requireController();
        $controller = $this->getFullQualifiedController();
        $action     = $this->getAction();
        $params     = $this->getParams();
        
        if (class_exists($controller)) {
            $rc = new \ReflectionClass($controller);
            if ($rc->hasMethod($action)) {
                $rm = $rc->getMethod($action);
                return $rm->invokeArgs($rc->newInstance(), $this->parseParams($rm, $params));
            }
        }
    }
    
    private function parseParams(\ReflectionMethod $rm, Array $request) {
        $params = $rm->getParameters();
        $formalParams = array();

        foreach ($params as $param) {
            $currentParam = $param->getName();

            if (isset($request[$currentParam])) {
                $value = $request[$currentParam];
            }else{
                if ($param->isDefaultValueAvailable()) {
                    $value = $param->getDefaultValue();
                }else{
                    $value = '';
                }
            }

            $formalParams[$currentParam] = $value;
        }
        return $formalParams;
    }
    
    private function requireController() {
        if (file_exists($this->getControllerClass())) {
            require $this->getControllerClass();
        }else{
            return false;
        }
    }
    private function getControllerClass() {
        return $this->getClassesPath() . $this->getController() . '.php';
    }
    public function getFullQualifiedController() {
        return $this->getPackage() . '\\' . $this->getController();
    }
    
    public function getController() {
        return ($this->controller) ? $this->controller : self::$DEFAULT_CONTROLLER;
    }
    public function setController($controller) {
        // Force First letter cap
        if (preg_match('/^[A-Z]{1}[a-zA-Z]{1,15}$/', $controller)) {
            return $this->controller = $controller;
        }else{
            return false;
        }
    }
    
    public function getAction() {
        return ($this->action) ? $this->action : self::$DEFAULT_ACTION;
    }
    public function setAction($action) {
        // lowercase
        if (preg_match('/^[a-z]{1,15}$/', $action)) {
            return $this->action = $action;
        }else{
            return false;
        }
    }
    
    public function getParams() {
        return ($this->params) ? $this->params : array();
    }
    public function setParams(Array $params) {
        return $this->params = $params;
    }
    
    public function getClassesPath() {
        return ($this->classes_path) ? $this->classes_path : $this->setClassesPath(self::$DEFAULT_CLASSES_PATH);
    }
    public function setClassesPath($path) {
        if (preg_match('/^[a-zA-Z\/\.]{1,256}$/', $path)) {
            $full_path = dirname(__FILE__) . '/'. $path;
            if (file_exists($full_path)) {
                return $this->classes_path = $full_path;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getPackage() {
        return ($this->package) ? $this->package : $this->setPackage(self::$DEFAULT_PACKAGE);
    }
    public function setPackage($package) {
        if (preg_match('/^[A-Z]{1}[a-zA-Z]{1,15}$/', $package)) {
            return $this->package = $package;
        }else{
            return false;
        }
    }
}