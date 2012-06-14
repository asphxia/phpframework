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
//require dirname(__FILE__) . '/../core/Core.php';
require dirname(__FILE__) . '/../core/CoreException.php';
require dirname(__FILE__) . '/../core/utils/Singleton.php';
require dirname(__FILE__) . '/../core/config/EncoderInterface.php';
require dirname(__FILE__) . '/../core/config/encoder/IniEncoder.php';
require dirname(__FILE__) . '/../core/config/Configuration.php';
require dirname(__FILE__) . '/../core/router/utils/Utils.php';
require dirname(__FILE__) . '/../core/router/Router.php';
require dirname(__FILE__) . '/../core/FrontController.php';

// Initialize the FrontControllers
// Call the given controller and action
$Fc = FrontController::getInstance();
$Fc->routeController();

// Now through the singleton FC we have our body developed behind the scenes
// and ready to GO... may be.
$Fc->getRender()->output($Fc->getResponse());

// je veux dormir, dormir plutot que vivre