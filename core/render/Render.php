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
use Core\Exception as Exception;
/**
 * Description of Render
 *
 * @author asphyxia
 */
class Render {
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
    private $master = null;
    
    /**
     *
     * @var type 
     */
    private $root = null;
    
    /**
     *
     * @var type 
     */
    private $namespace = null;
    
    /**
     * 
     */
    private $layoutsPath = null;
    
    /**
     *
     * @param array $configuration 
     */
    public function __construct(array $configuration = array()) {
        if (isset($configuration['pages'])) {
            $this->setAvailablePages($configuration['pages']);
        }
        if (isset($configuration['config'])) {
            $this->setConfiguration($configuration['config']);
        }
    }
    
    /**
     *
     * @param array $config
     * @return type 
     */
    public function setConfiguration(Array $config) {
        if (!is_array($config)) {
            $config = array($config);
        }
        foreach ($config as $key => $val) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $config[$key] = $this->$method($val);
            }else{
                throw new Exception('Method doesnt exists: `'. $method .'`');
            }
        }
        return $this->config;
    }
    
    /**
     *
     * @return type 
     */
    public function getConfiguration() {
        return ($this->config) ? $this->config : array();
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
     * @param type $dir
     * @return type 
     */
    public function setViewsPath($dir) {
        $dir = realpath($this->getRootPath() . '/' . $dir);
        if ($dir && false !== $this->validPath($dir)) {
            $this->pages = $dir;
        }else{
            throw new Exception('Invalid pages path: `' . $dir .'`');
        }
        return $this->pages;
    }
    
    /**
     *
     * @return type 
     */
    public function getViewsPath() {
        return $this->pages;
    }
    
    /**
     *
     * @param type $master 
     */
    public function setMasterPage($master) {
        $master = realpath($this->getRootPath() . '/' . $master);
        if (false !== $this->validPath($master)) {
            $this->master = $master;
        }else{
            throw new Exception('Invalid master view: `' . $master . '`');
        }
        return $this->master;
    }
    
    /**
     * 
     */
    public function getMasterPage() {
      return $this->master;  
    }
    
    /**
     *
     * @param type $root
     * @return type
     * @throws Exception 
     */
    public function setRootPath($root) {
        $root = realpath(__DIR__ . '/' . $root);
        if (false !== $this->validPath($root)) {
            $this->root = $root;
        }else{
            throw new Exception('Invalid root path: `' . $root. '`');
        }
        return $this->root;
    }
    
    /**
     *
     * @return type 
     */
    public function getRootPath() {
        return $this->root;
    }
    
    /**
     *
     * @param type $namespace
     * @return type 
     */
    public function setNamespace($namespace) {
        $namespace = \ucfirst($namespace);
        $fullpath = realpath($this->getRootPath() . '/' .  $namespace);
        if (false !== $this->validPath($fullpath)) {
            $this->namespace = $namespace;
        }else{
            throw new Exception('Invalid namespace path: `' . $fullpath .'`');
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
     */
    public function getLayoutsPath() {
        return $this->layoutsPath;
    }
    
    /**
     * 
     */
    public function setLayoutsPath($path) {
        $path = realpath($this->getRootPath() . '/' . $path);
        if (false !== $this->validPath($path)) {
            $this->layoutsPath = $path;
        }else{
            throw new Exception('Invalid layout path: `' . $path .'`');
        }
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
        $fullpath = $this->getPageFullPath($page);
        if (false !== $this->validPath($fullpath)) {
            $this->page = $page;
        }else{
            throw new Exception('Invalid page: `'.$fullpath. '`');
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
    public function setViewsExtension($ext) {
        if (preg_match('/^\.[a-z]{1,5}$/', $ext)) {
            $this->extension = $ext;
        }else{
            throw new Exception('Invalid extension: `'.$ext.'`');
        }
        return $this->extension;
    }
    
    /**
     *
     * @return type 
     */
    public function getViewsExtension() {
        return $this->extension;
    }
    
    /**
     *
     * @param type $path
     * @return boolean 
     */
    private function validPath($path) {
        $fullpath = realpath($path);
        if (false !== $fullpath && file_exists($fullpath)) {
            return $fullpath;
        }else{
            throw new Exception('Invalid path: `' . $fullpath . '`');
        }
    }
    
    /**
     *
     * @param type $page
     * @return type 
     */
    private function getPageFullPath($page) {
        $path = realpath($this->getViewsPath() . '/' .  strtolower($page) . $this->getViewsExtension());
        return $path;
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
            throw new Exception('View not found: `' . $this->getActivePage() . '`');
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
        @ob_clean();
        @ob_start();
        include $this->requirePage();
        $partial = @ob_get_clean();
        if (isset($this->master) && $this->master != '') {
            require $this->master;
        }else{
            echo $partial;
        }
    }

}
