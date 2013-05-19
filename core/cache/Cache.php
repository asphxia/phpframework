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
 * Description of Cache
 *
 * @author asphyxia
 */
namespace Core\Cache;
use Core\Utils\Logger as Logger;
use Core\Exception as Exception;
use \Stash\Handler\FileSystem as StashFS;
use \Stash\Box as StashBox;

class Cache {
    /**
     *
     * @var type 
     */
    private $handler;
    
    /**
     *
     * @var type 
     */
    private $cache;

    /**
     *
     * @var type 
     */
    private $config;
    
    /**
     *
     * @var type 
     */
    public static $INVALID_CACHE_KEY = -1;

    /**
     *
     * @var type 
     */
    public static $ERROR_WRITTING_CACHE = -2;
    
    /**
     *
     * @param type $config 
     */
    public function __construct(Array $config = array()) {
        $this->config = $config;
        if (!isset($this->config['cachePath'])) {
            throw new Exception('Cache path not defined!');
        }
        // Logger::warn('Config: ' . print_r($this->config,1));
        $this->handler = new StashFS(array(
            'path' => realpath($this->config['cachePath']),
        ));
        StashBox::setHandler($this->handler);
    }

    /**
     *
     * @param string $dataKey cache-key to be rewrite
     */
    private function getDataKey($dataKey) {
        foreach ($this->config['key'] as $arr) {
            $arr['name'] = $quoted = preg_quote($arr['name'], '/');
            // Logger::info(print_r($arr, 1));

            $countReplacements = 0;
            $res = preg_replace('/' . $quoted . '/', $arr['value'], $dataKey, -1, $countReplacements);
            if (null !== $res && $countReplacements >= 1) {
                return $res;
            }
        } 
        return null;
    }

    /**
     * 
     * @param type $dataKey
     * @return boolean 
     */
    public function rebuild($dataKey) {
        $rewriteKey = $this->getDataKey($dataKey);
        $this->dataKey = !empty($rewriteKey) ? $rewriteKey : $dataKey;

        if (!empty($this->dataKey)) {
            Logger::warn('Cache-Key: ' . $this->dataKey);
            
            $this->cache = StashBox::getCache($this->dataKey);
            $this->data = $this->cache->get();

            return $this->cache->isMiss();

        }else{
            Logger::error('Invalid Cache-key!: ' . $this->dataKey);
            return self::$INVALID_CACHE_KEY;
        }
        return false;
    }

    /**
     *
     * @return type 
     */
    public function invalidateCache() {
        Logger::warn('Rebuild-Cache: True');
        Logger::warn('Cache-Key: ' . $this->dataKey);

        if (!empty($this->dataKey)) {
            return $this->cache->clear();
        }else{
            Logger::error('Empty Cache-key!');
            return self::$INVALID_CACHE_KEY;
        }
    }
    
    /**
     *
     * @param type $data
     * @return boolean 
     */
    public function setData($data) {
        if (false === $this->cache->set($data)) {
            Logger::log('Cache-error: Couldn\'t save cache!');
            return self::$ERROR_WRITTING_CACHE;
        }
        return true;
    }
    
    /**
     *
     * @return boolean 
     */
    public function getData() {
        if ($this->cache) {
            return $this->cache->get();
        }else{
            return false;
        }
    }

    /**
     * 
     */
    public function grabOutput() {
        @ob_clean();
        ob_start();
    }

    /**
     *
     * @return type 
     */
    public function getOutput() {
        return ob_get_clean();
    }

}