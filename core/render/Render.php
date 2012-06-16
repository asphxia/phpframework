<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @copyright #legal.notice#
 * @version #Id#
 * @built #buildtime#
 */
namespace Core\Render;
/**
 * Description of Render
 *
 * @author asphyxia
 */
final class Render {
    /**
     *
     * @var type 
     */
    private $config = null;
    
    /**
     *
     * @var type 
     */
    private $availablePages = null;

    /**
     *
     * @var type 
     */
    private $pages = null;
    
    /**
     *
     * @var type 
     */
    private $extension = null;
    
    /**
     *
     * @var type 
     */
    private $page = null;
    
    /**
     *
     * @var type 
     */
    private $namespace = null;
    
    /**
     *
     * @param array $configuration 
     */
    public function __construct(array $configuration = array()) {
        if (isset($configuration['pages'])) {
            $this->setAvailablePages($configuration['pages']);
        }
        if (isset($configuration['config'])) {
            $this->setConfig($configuration['config']);
        }
    }
    
    /**
     *
     * @param array $pages
     * @return type 
     */
    public function setAvailablePages(Array $pages) {
        if (is_array($pages)) {
            $this->availablePages = $pages;
        }else{
            $this->availablePages = array();
        }
        return $this->availablePages;
    }
    
    /**
     *
     * @return type 
     */
    public function getAvailablePages() {
        return ($this->availablePages) ? $this->availablePages : array();
    }
    
    /**
     *
     * @param array $config
     * @return type 
     */
    public function setConfig(Array $config) {
        if (is_array($config)) {
            if (isset($config['extension'])) {
                $config['extension'] = $this->setExtension($config['extension']);
            }
            if (isset($config['pages'])) {
                $config['pages'] = $this->setPages( dirname(__FILE__) . '/' . $config['pages']);
            }
            if (isset($config['defaultNamespace'])) {
                $config['defaultNamespace'] = $this->setNamespace( $config['defaultNamespace']);
            }
            if (isset($config['defaultPage'])) {
                $config['defaultPage'] = $this->setActivePage($config['defaultPage']);
            }
            $this->config = $config;
        }else{
            $this->config = array();
        }
        return $this->config;
    }
    
    /**
     *
     * @return type 
     */
    public function getConfig() {
        return ($this->config) ? $this->config : array();
    }
    
    /**
     *
     * @param type $dir
     * @return type 
     */
    public function setPages($dir) {
        if (false !== $this->validPath($dir)) {
            $this->pages = $dir;
        }
        return $this->pages;
    }
    
    /**
     *
     * @return type 
     */
    public function getPages() {
        return $this->pages;
    }
    
    /**
     *
     * @param type $namespace
     * @return type 
     */
    public function setNamespace($namespace) {
        $namespace = \ucfirst($namespace);
        if (false !== $this->validPath($this->getPages() . '/' .  $namespace)) {
            $this->namespace = $namespace;
        }
        return $namespace;
    }
    
    /**
     *
     * @return type 
     */
    public function getNamespace() {
        return $this->namespace;
    }
    
    /**
     *
     * @param type $page
     * @param type $namespace
     * @return type 
     */
    public function setActivePage($page, $namespace = null) {
        if (!is_null($namespace)) {
            $this->setNamespace($namespace);
        }
        if (false !== $this->validPath($this->getPageFullPath($page) )) {
            $this->page = $page;
        }
        return $this->page;
    }
    
    /**
     *
     * @return type 
     */
    public function getActivePage() {
        return $this->page;
    }
    
    /**
     *
     * @param type $ext
     * @return type 
     */
    public function setExtension($ext) {
        if (preg_match('/^\.[a-z]{1,5}$/', $ext)) {
            $this->extension = $ext;
        }
        return $this->extension;
    }
    
    /**
     *
     * @return type 
     */
    public function getExtension() {
        return $this->extension;
    }
    
    /**
     *
     * @param type $path
     * @return boolean 
     */
    private function validPath($path) {
        $fullpath = $path; // dirname(__FILE__)
        if (file_exists($fullpath)) {
            return $fullpath;
        }else{
            return false;
        }
    }
    
    /**
     *
     * @param type $page
     * @return type 
     */
    private function getPageFullPath($page) {
        return $this->getPages() . $this->getNamespace() . '/' .  \ucfirst($page) . $this->getExtension();
    }

    /**
     *
     * @return type
     * @throws \Core\Exception 
     */
    private function requirePage() {
        $ap = $this->getPageFullPath($this->getActivePage());
        if (file_exists( $ap )) {
            return $ap;
        }else{
            throw new \Core\Exception('View not found: `' . $ap . '`');
        }
    }
    
    /**
     *
     * @param type $data 
     */
    public function output($data = null) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $$key = $val;
            }
        }
        require $this->requirePage();
    }

}
