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
    private $configuration;
    
    public static $ROOT;
    
    /**
     * 
     */
    public function __construct(){
        
        self::$ROOT = Core::$ROOT;
        
        // global app settings (for all apps)
        $this->config = FrontController::getInstance()->getConfigurationEngine();
        $this->configuration = $this->config->getConfiguration(array('app'));
         
        // deprecated stuff. move away.
        $this->postUrl = $this->configuration['postUrl'];
        $this->getUrl = $this->configuration['getUrl'];
        $this->basePath = $this->getUrl;
        $this->baseUrl = $this->basePath;
                        
        $this->rebaseGetUrl = $this->postUrl = $this->configuration['rebaseGetUrl'];
        $this->rebasePostUrl = $this->postUrl = $this->configuration['rebasePostUrl'];
        //

    }
}