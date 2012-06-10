<?php

/**
 * Description of RenderHelpers
 *
 * @author asphyxia
 */

namespace Core\Render;

final class Render {
    private $config = null;
    private $availablePages = null;

    private $pages = null;
    private $extension = null;
    
    # TODO Add a controller path for pages: views/controller/view.phtml
    # currently it looks for pages/templates/views/whatever into views/*.phtml
    # with the page name as the controller. We need to have different templates
    # for the actions in the controller.
    private $page        = null;
    private $namespace   = null;
    
    public static $DEFAULT_PAGE_DIR = '../application/views';
    public static $DEFAULT_PAGE = 'Index';
    public static $DEFAUL_EXTENSION = '.phtml';

    public function __construct(array $configuration = array()) {
        if (isset($configuration['pages'])) {
            $this->setAvailablePages($configuration['pages']);
        }
        if (isset($configuration['config'])) {
            $this->setConfig($configuration['config']);
        }
    }
    public function setAvailablePages(Array $pages) {
        if (is_array($pages)) {
            $this->availablePages = $pages;
        }else{
            $this->availablePages = array();
        }
        return $this->availablePages;
    }
    public function getAvailablePages() {
        return ($this->availablePages) ? $this->availablePages : array();
    }
    
    public function setConfig(Array $config) {
        if (is_array($config)) {
            if (isset($config['extension'])) {
                $config['extension'] = $this->setExtension($config['extension']);
            }
            if (isset($config['pages'])) {
                $config['pages'] = $this->setPages( dirname(__FILE__) . '/' . $config['pages']);
            }
            $this->config = $config;
        }else{
            $this->config = array();
        }
        return $this->config;
    }
    public function getConfig() {
        return ($this->config) ? $this->config : array();
    }
    
    public function setPages($dir) {
        if (false !== $this->validPath($dir)) {
            $this->pages = $dir;
        }
        return $this->pages;
    }
    public function getPages() {
        return ($this->pages)?$this->pages : self::$DEFAULT_PAGE_DIR;
    }
    
    public function setNamespace($namespace) {
        $namespace = \ucfirst($namespace);
        if (false !== $this->validPath($this->getPages() . '/' .  $namespace)) {
            $this->namespace = $namespace;
        }
        return $namespace;
    }
    public function getNamespace() {
        return $this->namespace;
    }
    
    public function setActivePage($page, $namespace = null) {
        if (!is_null($namespace)) {
            $this->setNamespace($namespace);
        }
        if (false !== $this->validPath($this->getPageFullPath($page) )) {
            $this->page = $page;
        }else{
            $this->page =  self::$DEFAULT_PAGE;
        }
        return $this->page;
    }
    public function getActivePage() {
        return ($this->page)?$this->page : self::$DEFAULT_PAGE;
    }
    
    public function setExtension($ext) {
        if (preg_match('/^\.[a-z]{1,5}$/', $ext)) {
            $this->extension = $ext;
        }else{
            $this->extension = self::$DEFAUL_EXTENSION;
        }
        return $this->extension;
    }
    public function getExtension() {
        return ($this->extension)?$this->extension : self::$DEFAUL_EXTENSION;
    }
    
    private function validPath($path) {
        $fullpath = $path; // dirname(__FILE__)
        if (file_exists($fullpath)) {
            return $fullpath;
        }else{
            return false;
        }
    }
    # TODO: Add buffering (so echos won't show up immediatly, also, good to work with firephp)
    # TODO: Optional response format (json for httpxmlrequest, html for normal http requests)
    
    private function getPageFullPath($page) {
        return $this->getPages() . $this->getNamespace() . '/' .  \ucfirst($page) . '/' . $this->getExtension();
    }

    private function requirePage() {
        $ap = $this->getPageFullPath($this->getActivePage());
        if (file_exists( $ap )) {
            return $ap;
        }else{
            throw new \Exception('View not found! ' . $ap);
        }
    }
    
    public function output($data = null) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $$key = $val;
            }
        }
        require $this->requirePage();
    }

}
