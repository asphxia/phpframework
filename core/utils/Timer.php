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
 * Description of Timer
 *
 * @author asphyxia
 */
namespace Core\Utils;

class Timer {
    private $pageStart;
    private $pageEnd;
    private function _time() {
        $load_time = microtime();
        $load_time = explode(' ',$load_time);
        $load_time = $load_time[1] + $load_time[0];
        return $load_time;
    }
    public function start() {
        $this->pageStart = $this->_time();
    }
    public function stop() {
        $this->pageEnd = $this->_time();
    }
    public function result() {
        $final_time = ($this->pageEnd - $this->pageStart);
        return number_format($final_time, 4, '.', '');
    }
}