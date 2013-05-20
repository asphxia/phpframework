<?php
namespace Core;
use Core\Utils\Logger as Logger;

final class Exception extends \Exception{
    public static function redirect(){
        if ($_SERVER["REQUEST_URI"] !== '/' && !isset($_SESSION['development'])) {
            header("Location: /");
        }
    }
    public static function handleException(\Exception $ex) {
        header("Core-Exception: `" . $ex->getMessage() . '`');
        Logger::error($ex);
        self::redirect();
    }
    public static function handleError( $number, $message, $file, $line )
    {   
        $error = array( 'type' => $number, 'message' => $message, 'file' => $file, 'line' => $line );
        Logger::warn($error);
        self::redirect();
    }
    public static function captureShutdown( )
    {
        $error = error_get_last( );
        if( $error ) {
            ## IF YOU WANT TO CLEAR ALL BUFFER, UNCOMMENT NEXT LINE:
            ob_end_clean( );
            
            // Display content $error variable
            Logger::error($error);
            self::redirect();
        } else {
            return true;
        }
    }
}
