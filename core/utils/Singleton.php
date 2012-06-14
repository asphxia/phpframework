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
 * Description of Singleton
 *
 * @author asphyxia
 */
namespace Core\Utils;

class Singleton {
    public static function getInstance() {
        if (empty (static::$_instance)) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }
    private function __construct() {}
    public function __destruct() {}
    private function __clone() {}

}