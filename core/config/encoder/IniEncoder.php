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

class IniEncoder implements EncoderInterface {
    private static $VERSION = '0.1';
    private static $INHERITAGE_SEPARATOR = ':';
    private static $INCLUSION_KEYWORD = 'include';
    private static $INCLUSION_ARRAY = 'files';
    private $_datasrc = null;
    
    public function __construct() {
        
    }

    public function __destruct() {
        
    }

    public function getVersion() {
        return self::$VERSION;
    }
    private function processInheritance($data){
        foreach ($data as $key => $val) {
            if (strstr($key, self::$INHERITAGE_SEPARATOR)) {
                $section = @split(self::$INHERITAGE_SEPARATOR, $key);

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
    private function getIncludeSection($data) {
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
    private function mergeArray($original, $toMerge) {
        foreach ($toMerge as $item => $val) {
            $original[$item] = $val;
        }
        return $original;
    }
    private function processInclusion($data) {
        if (null !== $includes = $this->getIncludeSection($data) ) {
            // for each item in the `include` section (relative paths)
            foreach ($includes as $file) {
                $inclusion = $this->processConfig(dirname($this->_datasrc) . '/' . $file);
                $data = $this->mergeArray($data, $inclusion);
            }
        }
        return $data;
    }
    public function processConfig($data = null) {
        if ($data == null) $data = $this->_datasrc;
        $data = parse_ini_file($data, true);
        $data = $this->processInclusion($data);
        $data = $this->processInheritance($data);
        return $data;
    }

    public function setDataSource($datasource) {
        if (is_string($datasource) && file_exists($datasource)) {
            $this->_datasrc = $datasource;
        }else{
            $this->_datasrc = null;
        }
        return $this->_datasrc;
    }

    public function encode($data, $format) {
        
    }

}