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
namespace Core\Router;
use Core\Exception;

final class Router extends Utils\Utils {
   
    /**
     *
     * @param array $params 
     */
    public function __construct(Array $params = null) {
        if (!is_null($params)) {
            $this->setConfiguration($params);
        }
        if (!is_null($this->request)) {
            $this->processPath($this->request);
        }
    }
    
    /**
     *
     * @return type 
     */
    public function routeController() {
        $this->requireController();

        $namespace  = $this->getNamespace();
        $controller = $this->getController();
        $package    = $namespace . '\\' . $controller;

        $action     = $this->getAction();
        $params     = $this->getParams();

        if (class_exists($package)) {
            $rc = new \ReflectionClass($package);
            if ($rc->hasMethod($action)) {
                $rm = $rc->getMethod($action);
                $instance = $rc->newInstance();
                
                // Calling the action method for the given controller
                if (false !== $res = $rm->invokeArgs($instance, $this->parseParams($rm, $params))){
                    // we return the result and the controller instance
                    if (is_array($res)) {
                        $res['app'] = $instance;
                    }
                    return $res;
                }else{
                    // if the action returns False we return that
                    return false;
                }
                
            }else{
                throw new Exception('Controller class doesn\'t has method `'.$action.'`');
            }
        }else{
            throw new Exception('Controller class doesn\'t exists: `' . $package . '`');
        }
    }
}