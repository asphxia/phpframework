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
 * Description of Logger
 *
 * @author asphyxia
 */
namespace Core\Utils;
class Logger extends Singleton {
    private $logger = null;
    public static $_instance = null;
    public $enabled = false;
    
    public function setDriver($logger) {
      $this->logger = $logger;
    }

    public function debug($log, $func = 'debug') {
      if ($this->enabled) {
        $this->logger->$func($log);
      }
    }
    
    public static function log($log) {
      self::getInstance()->debug($log, 'log');
    }

    public static function info($log) {
      self::getInstance()->debug($log, 'info');
    }

    public static function warn($log) {
      self::getInstance()->debug($log, 'warn');
    }

    public static function error($log) {
      self::getInstance()->debug($log, 'error');
    }
}
