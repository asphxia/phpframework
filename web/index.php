<?php
/**
 * MVC common bootstrap file. This entry point gives our framework
 * a solid structure ensuring all users are handled consistently by
 * the same code and configurations.
 *
 * @version $Id$
 * @copyright ${legal.notice}
 * @license ${license}
 * @built ${buildtime}
 */
use Core\FrontController as FrontController;
use Core\Utils\Logger as Logger;
use Core\Utils\Timer as Timer;

// Define root's constants
define('__ROOT__', realpath(dirname(__FILE__) . '/../') . '/');

// Register Autoloaders paths
require_once __ROOT__.'vendor/Autoloader/Autoloader.php';

// Remove default current (web) autoloader path
Autoloader::getRegisteredAutoloader()->remove();

// Load custom paths
foreach (array('core', 'vendor', 'apps', 'libs') as $classPath) {
    $_autoloader = new \Autoloader(__ROOT__ . $classPath);
    $_autoloader->register();
}

// Initialize the FrontControllers
// Call the given controller and action
$Fc = FrontController::getInstance();
$Cache = $Fc->getCacheEngine();

// Initialize timers for performance measurements
$timer = new Timer();
$timer->start();

// Debug information 
Logger::info( 'Request-uri: '. $_SERVER['REQUEST_URI'] );
if (!empty($_POST)) {
    Logger::info( 'Post-info: ' . print_r($_POST,1));
}
Logger::info( 'Base-path: ' . $Fc->getBasePath() );

// If cache is outdated or there is logic to be done
if ( $Cache->rebuild( $Fc->getBasePath() ) && $Fc->routeController() ) {
    $Cache->invalidateCache();

    // Grab the output
    $Cache->grabOutput();

    $Fc->getRenderEngine()->output( $Fc->getResponse() );
    echo "<!-- " . date( DATE_ATOM ) . " -->";

    // Get the output
    $data = $Cache->getOutput();

    Logger::info( "Cached-at: " . date( DATE_ATOM ) );
    
    // Save it
    $Cache->setData( $data );
}

// Show it
$timer->stop();
$loadTime = $timer->result();

Logger::log( "Took  ${loadTime} to generate" );

echo $Cache->getData();

// je veux dormir, dormir plutot que vivre