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
use Core\Exception as Exception;

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
     */
    protected $rewrites;

    /**
     *
     * @var type 
     */
    protected $includePath;

    /**
     * Regular expression to determine the validity of a controller name
     * @var type String
     */
    protected static $validControllerName = '/^[a-z_]+$/i';   
    
    /**
     * Regular expression to determine the validity of a namespace name
     * @var type String
     */
    protected static $validNamespaceName = '/^[a-z]+$/i';
    
    /**
     * Regular expression to determine the validity of an action name
     * @var type String
     */
    protected static $validActionName = '/^[a-z]+$/i';
    
    /**
     *
     * @var type 
     */
    protected static $validIncludePath = '/^[a-z\/\.]+$/i';
    
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
    
    public function getConfiguration(Array $params = array()) {
        $result = array();
        foreach (array_values($params) as $key) {
            $method = 'get' . ucfirst(strtolower($key));
            if (method_exists($this, $method)) {
                $res = $this->$method();
                $result[$key] = $res;
            }
        }
        return $result;
    }

    /**
     *
     * @param type $namespace
     * @return type 
     */
    public function setNamespace($namespace) {
        if (\preg_match(self::$validNamespaceName, $namespace)) {
            return $this->namespace = ucfirst(strtolower($namespace));
        }else{
            throw new Exception('Invalid namespace name');
        }
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
        if (\preg_match(self::$validControllerName, $controller)) {
            return $this->controller = ucfirst(strtolower($controller));
        }else{
            throw new Exception('Invalid controller name');
        }
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
        if (\preg_match(self::$validActionName, $action)) {
            return $this->action = lcfirst($action);
        }else{
            throw new Exception('Invalid action name.');
        }
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
        if (\preg_match(self::$validIncludePath, $path)) {
            return $this->includePath = $path;
        }else{
            throw new Exception('Include path contains invalid characters:'.$path);
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
     *
     * @param array $rewrites
     * @return type 
     */
    public function setRewrites(Array $rewrites = array()) { 
        $url = $this->getRequest() ? $this->getRequest() : $_SERVER['REQUEST_URI'];
        $requestUri = str_replace('.php', '', (false != $str = strstr($url, '?', -1)) ? $str : $url );
        $requestUri = $requestUri[strlen($requestUri)-1] != '/' ? "${requestUri}/" : $requestUri;

        foreach ($rewrites as $match => $change) {
            $requestUri = str_replace($match, $change, $requestUri);
        }
        return $requestUri = $this->setRequest($requestUri);
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
        require_once $controller;
    }

    protected function getRequestPath($path, $request){
        $logicalPath = false;
        if (isset($_REQUEST[$path]) && $_REQUEST[$path] !== '') {
            $logicalPath = $_REQUEST[$path];

        }else {
            if (isset($request[$path]) && $request[$path] !== '') {
                
                $logicalPath = $request[$path];
            }
        }
        return $logicalPath;
    }            
    /**
     * Process the given path to get the controller, action and params.
     * 
     * @param type $path 
     */
    public function processPath($path) {
        $req = explode('/', (false !== $str = strstr($path, '?', true)) ? $str : $path);
        $request = array();
        $arrPaths = array('namespace', 'controller', 'action');
        for ($i = 1; $i<=3; $i++) {
            if (isset($req[$i])) {
                $request[$arrPaths[$i-1]] =  $req[$i];
            }else{
                $request[$arrPaths[$i-1]] = null;
            }
        }

        foreach ($arrPaths as $path) {
            if (false !== $res = $this->getRequestPath($path, $request)) {
                $this->setConfiguration(array($path => $res));
            }
        }
        
        $assoc = array();
        $params = array_slice($req, 4);
        $rounded = \floor(\count($params)*2/2)-2;
        for ($i=0; $i<=$rounded; $i++) {
            $assoc[$params[$i]] = $params[++$i];
        }
        $assoc += $_REQUEST;
        $this->setParams($assoc);
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

            if (isset($request[$currentParam]) && $request[$currentParam] != '') {
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