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
/**
 *
 * @author asphyxia
 */
interface EncoderInterface {
    public function processConfig();
    public function setDataSource($datasource);
    public function getVersion();
}