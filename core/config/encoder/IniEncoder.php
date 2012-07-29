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
 * Description of IniEncoder
 *
 * @author asphyxia
 */
namespace Core\Configuration;
use Core\Exception as Exception;

class IniEncoder implements EncoderInterface {
    /**
     *
     * @var type 
     */
    private static $VERSION = '0.1';
    
    /**
     *
     * @var type 
     */
    private static $INHERITAGE_SEPARATOR = ':';
    
    /**
     *
     * @var type 
     */
    private static $INCLUSION_KEYWORD = 'include';
    
    /**
     *
     * @var type 
     */
    private static $INCLUSION_ARRAY = 'files';
    
    /**
     *
     * @var type 
     */
    private $_datasrc = null;

    
    /**
     * 
     */
    private $_includePath = null;
    
    /**
     *
     * @return type 
     */
    public function getVersion() {
        return self::$VERSION;
    }
    
    /**
     *
     * @param type $data
     * @return type 
     */
    private function processInheritance(Array $data){
        foreach ($data as $key => $val) {
            if (strstr($key, self::$INHERITAGE_SEPARATOR)) {
                $section = explode(self::$INHERITAGE_SEPARATOR, $key);

                if (array_key_exists($section[1], $data)){
                    $data[$section[0]] = $data[$section[1]];
                    foreach ($data[$key] as $item => $val) {
                            $data[$section[0]][$item] = $val;
                    }
                }
                unset($data[$key]);
            }
        }
        return $data;
    }
    
    /**
     *
     * @param type $data
     * @return null 
     */
    private function getIncludeSection(Array $data) {
        // walk through every configuration section.
        foreach ($data as $section => $val) {
            // if there is any `include` section we process it.
            if ($section == self::$INCLUSION_KEYWORD) {
                if (isset($val[self::$INCLUSION_ARRAY])){
                    return $val[self::$INCLUSION_ARRAY];
                }else{
                    return null;
                }
            }    
        }
        return null;
    }
    
    /**
     *
     * @param type $original
     * @param type $new
     * @return type 
     */
    private function mergeSection($original, $new) {
        // For each element in the section (can be arrays too)
        foreach ($new as $key => $value) {
            // If it's not setted up and an array
            if (!isset($original[$key])) {
                $original[$key] = $value;
            }elseif (is_array($original[$key])){
                $original[$key] = $this->mergeSection($original[$key], $value);
            }
        }
        return $original;
    }
    
    /**
     *
     * @param type $original
     * @param type $toMerge
     * @return type 
     */
    private function mergeArray($original, $toMerge) {
        // For every section
        foreach ($toMerge as $sectionName => $sectionContents) {
            if (isset($original[$sectionName])) {
                $original[$sectionName] = $this->mergeSection($original[$sectionName], $sectionContents);
            }else{
                $original[$sectionName] = $sectionContents;
            }
        }
        return $original;
    }
    
    /**
     *
     * @param type $data
     * @return type 
     */
    private function processInclusion($data) {
        if (null !== $includes = $this->getIncludeSection($data) ) {
            // for each item in the `include` section (relative paths)
            foreach ($includes as $file) {
                $inclusion = $this->processConfig($this->_includePath . $file);
                $data = $this->mergeArray($data, $inclusion);
            }    
        }
        return $data;
    }
    
    /**
     *
     * @param type $data
     * @return type 
     */
    public function processConfig($datasrc = null) {
        if ($datasrc == null) $datasrc = $this->_datasrc;
        $data = @parse_ini_file($datasrc, true);
        if (is_array($data)) {
            $data = $this->processInclusion($data);
            $data = $this->processInheritance($data);
        }else{
            throw new Exception('Couldn\'t open configuration file: `' . $datasrc . '`');
        }
        return $data;
    }

    /**
     *
     * @param type $datasource
     * @return type 
     */
    public function setDataSource($datasource) {
        if (is_string($datasource) && file_exists($datasource)) {
            $this->_datasrc = $datasource;
        }else{
            throw new Exception('Invalid data source: `'.$datasource.'`');
        }
        return $this->_datasrc;
    }
    
    /**
     *
     * @return type 
     */
    public function getDataSource() {
        return $this->_datasrc;
    }
    
    /**
    *
    * @param type $includePath
    * @return type 
    */
    public function setIncludePath($includePath) {
        if (is_string($includePath) && file_exists($includePath)) {
            $this->_includePath = $includePath;
        }else{
            throw new Exception('Invalid include path: `' . $includePath.'`');
        }
        return $this->_includePath;
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
     * @param type $data
     * @param type $format 
     */
    public function encode($data, $format) {
    }

}