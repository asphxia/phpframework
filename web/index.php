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

// Define root's constants
define('__ROOT__', realpath(dirname(__FILE__) . '/../') . '/');

// Register Autoloaders paths
require_once __ROOT__.'vendor/Autoloader/Autoloader.php';

// Remove default current (web) autoloader path
Autoloader::getRegisteredAutoloader()->remove();

// Load custom paths
foreach (array('core', 'vendor', 'libs') as $classPath) {
    $_autoloader = new \Autoloader(__ROOT__ . $classPath);
    $_autoloader->register();
}

// Initialize the FrontControllers
// Call the given controller and action
$Fc = Core\FrontController::getInstance();
$Fc->routeController();

// Now through the singleton FC we have our body developed behind the scenes
// and ready to GO... may be.
$Fc->getRenderEngine()->output($Fc->getResponse());

// je veux dormir, dormir plutot que vivre