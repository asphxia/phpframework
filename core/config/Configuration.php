<?php

namespace Core\Configuration;
use Exception;

final class Configuration extends \Core\Utils\Singleton {
    protected static $_instance;
    private $_data = null;
    private $_encoder = null;
    private $_config = null;

    /**
     * Shortcut method to setup the configuration file and encoder.
     * 
     * @param String $config Path (absolute) to the configuration file.
     * @param Object $encoder A encoder object (which implements ConfigEncoder interface).
     */
    public function setConfiguration($config, EncoderInterface $encoder = null) {
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
    public function getConfiguration($arrPath, $data = null) {
        if ($data == null && null === $data = $this->getData()) {
            throw new Exception('No configuration loaded.');
        }
        if ($arrPath === null) {
            return $data;
        }elseif (!is_array($arrPath)) {
             $arrPath = array($arrPath);
        }

        $keyCount = array_keys($arrPath);
        if ($keyCount[0] === 0) {
            $arrPath = array($arrPath[0] => '');
        }

        foreach ($arrPath as $key => $val) {
            if (array_key_exists($key, $data)) {
                if (is_array($val)) {
                    return $this->getConfiguration($val, $data[$key]);
                }elseif ($val !== '') {
                    return $data[$key][$val];
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
        if (is_string($configuration) && file_exists($configuration)) {
            $this->_config = $configuration;
        }else{
            throw new Exception('Invalid DataSource provided or DataSource not found.');
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

    private function getData() {
        if ($this->_encoder === null) {
            throw new Exception('No encoder defined.');
        }
        if ($this->_data === null) {
            $this->_encoder->setDataSource($this->_config);
            $this->_data = $this->_encoder->processConfig();
        }
        return $this->_data;
    }
}