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
 * Description of Application
 *
 * @author asphyxia
 */
namespace Core;
use Core\FrontController as FrontController;
use Core\Configuration as Configuration;

class Application {
    /**
     *
     * @var type 
     */
    public $baseUrl;
    /**
     * Global configuration array
     * @var type 
     */
    public $config;
    

    // remove this
    public static $ROOT;
    
    /**
     * 
     */
    public function __construct(){
        
        self::$ROOT = Core::$ROOT;
        
        $this->router = FrontController::getInstance()->getRouterEngine();
        $this->namespace = $this->router->getNamespace();

        // Remove .ini from here. Must be configurable!
        $this->appConfigFilename = ucfirst(strtolower($this->namespace)) . '.ini';

        //Logger::log($this->includePath);
        // global app settings (for all apps)
        $this->globalConfig = FrontController::getInstance()->getConfigurationEngine();
        $this->config = $this->globalConfig->getConfiguration(array('app'));
        
        // individual settings defined by namespace
        $this->includePath = $this->config['root-path'] . $this->namespace . '/config/';
        $this->appConfig = new Configuration\Configuration();
        $this->appConfig->setConfiguration($this->appConfigFilename,
                        new Configuration\IniEncoder(),
                        $this->includePath
                    );
        $this->config += $this->appConfig->getConfiguration(array('self'));

        $this->basetUrl = $this->config['base-url'];
        $this->appPath = $this->config['app-path'];

    }
}