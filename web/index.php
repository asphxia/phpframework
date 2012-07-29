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
namespace Core;
// Require components
require dirname(__FILE__) . '/../core/Core.php';

// Initialize the FrontControllers
// Call the given controller and action
$Fc = FrontController::getInstance();
$Fc->routeController();

// Now through the singleton FC we have our body developed behind the scenes
// and ready to GO... may be.
$Fc->getRenderEngine()->output($Fc->getResponse());

// je veux dormir, dormir plutot que vivre