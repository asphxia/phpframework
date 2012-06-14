<?php
/**
 * Description of RenderHelpers
 *
 * @author asphyxia
 */
namespace Core;
require_once dirname(__FILE__) . '/../core/Autoload.php';
final class Core {
    public function __construct() {
        error_reporting(E_ALL);
        mb_internal_encoding('UTF-8');
        spl_autoload_register(array(new Autoload(), 'loadClass'));
        #set_exception_handler(array(new CoreException(), 'handleException'));
        session_start();
    }
}
new Core();