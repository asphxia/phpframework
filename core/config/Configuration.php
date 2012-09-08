<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @copyright #legal.notice#
 * @version #Id#
 * @built #buildtime#
 */
namespace Core\Configuration;
use Core\Exception;
use Core\Configuration\EncoderInterface as EncoderInterface;
use Core\Utils\Logger as Logger;

/**
 * 
 * @author asphyxia 
 */
final class Configuration {
    
    /**
     *
     * @var type 
     */
    private $_data = null;
    
    /**
     *
     * @var type 
     */
    private $_encoder = null;
    
    /**
     *
     * @var type 
     */
    private $_config = null;
    
    /**
     * 
     */
    private $_includePath = null;

    /**
     * Shortcut method to setup the configuration file and encoder.
     * 
     * @param String $config Path (absolute) to the configuration file.
     * @param Object $encoder A encoder object (which implements ConfigEncoder interface).
     */
    public function setConfiguration($config, EncoderInterface $encoder = null, $includePath = null) {
        $includePath = ($includePath) ? $includePath : __DIR__ . '/../../';
        $this->setIncludePath($includePath);
        $this->setDataSource($config);
        if ($encoder instanceof EncoderInterface) {
            $this->setEncoder($encoder);
        }
    }
    /**
     * Returns the value of a given configuration path.
     * 
     * Paths are key=>value based and can be nested. Per example,
     * to access a value from the following configuration (yaml):
     * 
     * db:
     *      pre:
     *          user: pre-user
     *          pass: pre-password
     *      pro:
     *          user: pro-user
     *          pass: pro-password
     * 
     * The following array could be used as an argument:
     *  array(
     *      'db' => array(
     *          'pre' => 'user'
     *  );
     * 
     * In the previous example this method would return `pre-user`. In order to
     * access all the items in the `pre` item, the following argument could be used:
     * array(
     *  'db' => 'pre'
     * );
     * 
     * @param var $section Array based path (key=>val) to access data on the
     * configuration. In this parameter is a string, it's converted to Array.
     * @return type
     */
    public function getConfiguration($arrPath = null, $data = null) {
        
        // If there is no data (src) configured
        if ($data == null && null === $data = $this->getData()) {
            throw new Exception('No configuration loaded.');
        }
        
        // If there is no arrPath (key=>values paths) we return the entire configuration
        if ($arrPath === null) {
            return $data;
        // If arrPath is a string we convert it to string to work with it
        }elseif (!is_array($arrPath)) {
             $arrPath = array($arrPath);
        }

        // We turn an index-based array to a key-value array
        $keyCount = array_keys($arrPath);
        if ($keyCount[0] === 0) {
            $arrPath = array($arrPath[0] => '');
        }

        // For each key-value we check it existence in the `data` array
        foreach ($arrPath as $key => $val) {
            if (array_key_exists($key, $data)) {
                // If the item contains an array we make a recursive call
                if (is_array($val)) {
                    return $this->getConfiguration($val, $data[$key]);
                    
                // Otherwise, if it's not empty, we return its value
                }elseif ($val !== '') {
                    return $data[$key][$val];

                // Or just the key itself
                }else{
                    return $data[$key];
                }
            }
        }
    }
    
    /**
     * Sets the encoder used to access the data source.
     * 
     * @param Object $encoder An encoder object (that implements the ConfigEncoder interface)
     * @return Object Returns the encoder given.
     */
    public function setEncoder(EncoderInterface $encoder) {
        $this->_data = null;
        $this->_encoder = $encoder;
    }
    
    /**
     * Returns the current configured encoder.
     * 
     * @return Object the current configured encoder.
     */
    public function getEncoder() {
        return $this->_encoder;
    }
    
    /**
     * Sets the configuration file to load data from.
     * 
     * The configuration file must be accessible and be in a format
     * that can be handled by the encoder (if any).
     * 
     * @param String $configuration The path (absolute) for the configuration file.
     * @return bool True or False if the file wasn't found.
     */
    public function setDataSource($configuration) {
        $fullpath = $this->getIncludePath() . '/' . $configuration;
        if (is_string($configuration) && file_exists($fullpath)) {
            $this->_config = $fullpath;
        }else{
            throw new Exception('Invalid DataSource provided or DataSource not found: `' . $fullpath . '`');
        }
    }
    /**
     * Returns the current configuration file.
     * 
     * @return var The path for the configuration file or null if it wasn't defined.
     */
    public function getDataSource() {
        return $this->_config;
    }
    
    /**
     *
     * @param type $includePath 
     */
    public function setIncludePath($includePath) {
        $this->_includePath = $includePath;
    }
    
    /**
     *
     * @return type 
     */
    public function getIncludePath() {
        return $this->_includePath;
    }
    
    /**
     *
     * @return type
     * @throws Exception 
     */
    private function getData() {
        if ($this->_encoder === null) {
            throw new Exception('No encoder defined.');
        }
        if ($this->_data === null) {
            $this->_includePath = $this->_includePath ? $this->_includePath : __DIR__ . '/../../';
            $this->_encoder->setIncludePath($this->_includePath);
            $this->_encoder->setDataSource($this->_config);
            $this->_data = $this->_encoder->processConfig();
        }
        return $this->_data;
    }
}
