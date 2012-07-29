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
require_once __ROOT__.'vendor/ChromePHP/ChromePHP.php';
class Logger extends \ChromePHP {
    public function __construct()
    {
        @ob_start();
        return ChromePHP::getInstance(true);
    }
}
