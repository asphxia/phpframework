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
require dirname(__FILE__) . '/../core/Config.php';
require dirname(__FILE__) . '/../core/Router.php';
require dirname(__FILE__) . '/../core/FrontController.php';

// Initialize the FrontControllers
// Call the given controller and action
FrontController::getInstance(array(
                                'config' => array(
                                        'file' =>  '../config/' . $_SERVER['SERVER_NAME'] . '.yml',
                                        'encoder' => null,
    )))->routeController();
        
// Now through the singleton FC we have our body developed behind the scenes
// and ready to GO... may be.
FrontController::getInstance()->getRender()->output(
     FrontController::getInstance()->getResponse()
);

// je veux dormir, dormir plutot que vivre