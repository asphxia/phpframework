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
 * Description of MustacheRender
 *
 * @author asphyxia
 */
namespace Core\Render;
use Core\Utils\Logger as Logger;
use \Mustache_Engine as Mustache;
use \Mustache_Loader_FilesystemLoader as MustacheLoader;

class MustacheRender extends Render {
    private $m;
    public function __construct(array $configuration = array()) {
        parent::__construct($configuration);
        $this->m = new Mustache(array(
            'cache' => realpath($this->getViewsPath() .'/../../cache/'),
            'partials_loader' => new MustacheLoader($this->getLayoutsPath(),
                    array('extension' => $this->getViewsExtension())),
        ));
        Logger::log($this->m);
    }
    public function output($data = null) {
        ob_clean();
        ob_start();
        
        // get the view/partial contents
        parent::output($data);
        $out = ob_get_clean();
        
        Logger::log(array($out, $data));
        echo $this->m->render($out, $data);
    }

}