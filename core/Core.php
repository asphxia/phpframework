<?php
/**
 * Description of RenderHelpers
 *
 * @author asphyxia
 */
require_once dirname(__FILE__) . '/../core/CoreLoader.php';

final class Core {
    public function __construct() {
        error_reporting(E_ALL);
        mb_internal_encoding('UTF-8');
        spl_autoload_register(array(new CoreLoader(), 'loadClass'));
        #set_exception_handler(array(new CoreException(), 'handleException'));
        session_start();
    }
}