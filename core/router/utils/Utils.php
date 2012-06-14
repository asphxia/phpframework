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
 * Description of Utils
 *
 * @author asphyxia
 */
namespace Core\Router\Utils;
use Core\Exception;

class Utils {
    /**
     * The app $_REQUEST (defined by Front controller)
     * 
     * @var Array 
     */
    protected $request;
    
    /**
     * 
     */
    protected $namespace = 'Application';
    
    /**
     * Derived from Request
     * 
     * @var String 
     */
    protected $controller;
    
    /**
     * The Action, derived from Request
     * 
     * @var String 
     */
    protected $action;
    
    /**
     * Derived from Request
     * 
     * @var Array 
     */
    protected $params;
    
    /**
     *
     * @var type 
     */
    protected $includePath;
    
    /**
     * Shortcut method to set up the class.
     * String
     * @param array $params 
     */
    public function setConfiguration(Array $params) {
        foreach ($params as $key => $val) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($val);
            }
        }
    }

    /**
     *
     * @param type $namespace
     * @return type 
     */
    public function setNamespace($namespace) {
        return $this->namespace = $namespace;
    }
    
    /**
     *
     * @return type 
     */
    public function getNamespace() {
      return $this->namespace;  
    }
    
    /**
     * Gets the controller.
     * 
     * @return type 
     */
    public function getController() {
        return $this->controller;
    }
    
    /**
     * Sets the controller.
     * 
     * @param String $controller
     * @return String 
     * @throws Exception 
     */
    public function setController($controller) {
        //if (\preg_match('@^[\w\\\/]$@', $controller)) {
            return $this->controller = $controller;
        //}else{
        //    throw new Exception('Invalid controller name');
        //}
    }
    
    /**
     * Gets the action.
     * 
     * @return type 
     */
    public function getAction() {
        return $this->action;
    }
    
    /**
     * Sets the action.
     * 
     * @param String $action
     * @return String
     * @throws Exception 
     */
    public function setAction($action) {
        //if (preg_match('/^[a-zA-Z]$/', $action)) {
            return $this->action = $action;
        //}else{
        //    throw new Exception('Invalid action name.');
        //}
    }
   
    /**
     * Gets the include path.
     *  
     * @return String 
     */
    public function getIncludePath() {
        return $this->includePath;
    }
    
    /**
     * Sets the include path.
     * 
     * @param String $path
     * @return String
     * @throws Exception 
     */
    public function setIncludePath($path) {
        if (preg_match('/[a-zA-Z\/\.]/', $path)) {
            if (file_exists($path)) {
                return $this->includePath = $path;
            }else{
                throw new Exception('Provided class path could not be found: `' . $path .'`.');
            }
        }else{
            throw new Exception('Include path contains invalid characters.');
        }
    }
    
    /**
     * Gets the params
     * 
     * @return Array 
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * Sets the params
     * 
     * @param array $params
     * @return Array 
     */
    public function setParams(Array $params) {
        return $this->params = $params;
    }
    
    /**
     * Gets the request
     * 
     * @return String
     */
    public function getRequest() {
        return $this->request;
    }
    
    /**
     * Sets the request
     * 
     * @param String $request 
     */
    public function setRequest($request) {
        $this->request = $request;
    }
    
    /**
     * Tries to load (require construct) the given controller.
     * 
     * @throws Exception 
     */
    protected function requireController() {
        if ($this->getIncludePath() == '') {
            throw new Exception('No include path defined');
        }
        if ($this->getController() == '') {
            throw new Exception('No controller defined.');
        }
        $controller = $this->getIncludePath() . '/' . $this->getController() . '.php';
        if (file_exists($controller)) {
            require_once $controller;
        }else{
            throw new Exception('Controller not found: `' . $controller . '`');
        }
    }
    
    /**
     * Process the given path to get the controller, action and params.
     * 
     * @param type $path 
     */
    public function processPath($path) {
        $request = explode('/', $path);
        if (isset($request[1]) && $request[1] != '') {
            $this->setController($request[1]);
        }
        
        if (isset($request[2]) && $request[2] != '') {
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

    /**
     * Created an array of params for the given method.
     * 
     * Uses the method signature to determine the methods parameters default values.
     * 
     * @param \ReflectionMethod $rm
     * @param array $request
     * @return string 
     */
    protected function parseParams(\ReflectionMethod $rm, Array $request = null) {
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

}