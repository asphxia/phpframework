<?php
/**
 * Description of RenderHelpers
 *
 * @author asphyxia
 */
namespace Core;
use Core\Exception as Exception;
use Core\Utils\Logger as Logger;
final class Core {
    public static $errorReporting = false;
    public static $displayErrors = false;

    public static $ROOT = __ROOT__;

    public function __construct(array $options = array()) {
        mb_internal_encoding('UTF-8');
        
        $e = new Exception();
        set_exception_handler(array($e, 'handleException'));
        set_error_handler(array($e, 'handleError'));
        register_shutdown_function( array( $e, 'captureShutdown' ) );

        if (isset($options['startSession'])) {
            session_start();
        }

        if (isset($options['environment']['development'])) {
          self::$errorReporting = E_ALL;
          self::$displayErrors = true;
        }else{
          # TODO clean this up
          self::$errorReporting = isset($options['environment']['errorReporting']) ? $options['environment']['errorReporting'] : self::$errorReporting;
          self::$displayErrors = isset($options['environment']['displayErrors']) ? $options['environment']['displayErrors'] : self::$displayErrors;
        }

        error_reporting(self::$errorReporting);
        error_reporting(self::$errorReporting);
        ini_set( 'display_errors', self::$displayErrors);


        if (isset($options['environment']['development'])) {
            # TODO Avoid hard dependency on chromephp
            Logger::getInstance()->setDriver(\ChromePhp::getInstance());
            Logger::getInstance()->enabled = true;
        }
    }
}
